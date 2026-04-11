<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\UmkmProduct;
use App\Models\UmkmVerification;
use App\Models\UmkmAdminLog;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PublicService;
use Carbon\Carbon;
use App\Models\WahaN8nSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UmkmRakyatController extends Controller
{
    public function index(Request $request)
    {
        $query = Umkm::where('status', 'aktif');

        if ($request->has('desa')) {
            $query->where('desa', $request->desa);
        }

        if ($request->has('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_usaha', 'like', '%' . $request->q . '%')
                    ->orWhere('jenis_usaha', 'like', '%' . $request->q . '%')
                    ->orWhere('deskripsi', 'like', '%' . $request->q . '%');
            });
        }

        $umkms = $query->latest()->paginate(12);
        $desas = Desa::orderBy('nama_desa')->get();

        return view('public.umkm_rakyat.index', compact('umkms', 'desas'));
    }

    public function create()
    {
        $desas = Desa::orderBy('nama_desa')->get();
        $umkmCategories = Umkm::getStandardCategories();
        return view('public.umkm_rakyat.create', compact('desas', 'umkmCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'nik' => 'required|string|digits:16',
            'no_wa' => 'required|string|max:20|unique:umkm,no_wa',
            'desa' => 'required|string',
            'patokan_lokasi' => 'required|string|max:255',
            'jenis_usaha' => 'required|string',
            'foto_usaha' => 'nullable|image|max:2048',
        ], [
            'no_wa.unique' => 'Nomor WhatsApp ini sudah terdaftar sebagai UMKM. Satu nomor hanya diperbolehkan mengelola satu toko resmi.'
        ]);

        $umkm = new Umkm($request->except('foto_usaha'));
        $umkm->status = Umkm::STATUS_PENDING;
        $umkm->source = Umkm::SOURCE_SELF;

        if ($request->hasFile('foto_usaha')) {
            $path = $request->file('foto_usaha')->store('umkm/usaha', 'public');
            $umkm->foto_usaha = $path;
        }

        $umkm->save();

        // Generate OTP
        $otp = strtoupper(Str::random(6));
        UmkmVerification::create([
            'umkm_id' => $umkm->id,
            'kode_verifikasi' => $otp,
            'expired_at' => Carbon::now()->addMinutes(15),
            'is_verified' => false
        ]);

        // Create Public Service entry for Inbox
        \App\Models\PublicService::create([
            'uuid' => (string) Str::uuid(),
            'desa_id' => is_numeric($request->desa) ? $request->desa : Desa::where('nama_desa', $request->desa)->first()?->id,
            'nama_desa_manual' => !is_numeric($request->desa) ? $request->desa : null,
            'nama_pemohon' => $umkm->nama_pemilik,
            'jenis_layanan' => 'Pendaftaran UMKM',
            'uraian' => "Pendaftaran UMKM Baru: {$umkm->nama_usaha}. Jenis Usaha: {$umkm->jenis_usaha}. Pemilik: {$umkm->nama_pemilik}.",
            'whatsapp' => $umkm->no_wa,
            'status' => PublicService::STATUS_MENUNGGU,
            'category' => PublicService::CATEGORY_UMKM,
            'source' => 'web_form'
        ]);

        // Log action
        UmkmAdminLog::create([
            'umkm_id' => $umkm->id,
            'action' => 'create',
            'actor' => 'system',
            'notes' => 'Pendaftaran mandiri via web. Masuk ke Inbox verifikasi.'
        ]);

        return redirect()->route('umkm_rakyat.verify_step', $umkm->id);
    }

    public function verifyStep($id)
    {
        $umkm = Umkm::findOrFail($id);
        $verification = $umkm->verifications()->where('is_verified', false)->latest()->first();

        if (!$verification) {
            return redirect()->route('umkm_rakyat.index');
        }

        // WhatsApp Link generation
        $adminWa = appProfile()->whatsapp_complaint ?? appProfile()->phone ?? "6282121212121";
        $text = "VERIFIKASI UMKM " . $verification->kode_verifikasi;
        $waUrl = "https://wa.me/{$adminWa}?text=" . urlencode($text);

        return view('public.umkm_rakyat.verify', compact('umkm', 'verification', 'waUrl'));
    }

    public function processVerify(Request $request, $id)
    {
        $request->validate(['otp' => 'required|string']);

        $umkm = Umkm::findOrFail($id);
        $verification = $umkm->verifications()
            ->where('kode_verifikasi', strtoupper($request->otp))
            ->where('is_verified', false)
            ->where('expired_at', '>', Carbon::now())
            ->first();

        if ($verification) {
            $verification->update(['is_verified' => true]);
            $umkm->update(['status' => Umkm::STATUS_AKTIF]);

            UmkmAdminLog::create([
                'umkm_id' => $umkm->id,
                'action' => 'verify',
                'actor' => 'system',
                'notes' => 'Verifikasi OTP berhasil.'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi berhasil! Etalase Anda sekarang aktif.',
                'redirect' => route('umkm_rakyat.show', $umkm->slug)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kode OTP tidak valid atau sudah kedaluwarsa.'
        ], 422);
    }

    public function show($slug)
    {
        $umkm = Umkm::where('slug', $slug)->firstOrFail();

        if ($umkm->status !== 'aktif' && !request()->has('preview')) {
            abort(404);
        }

        $products = $umkm->products()->where('is_available', true)->latest()->get();

        return view('public.umkm_rakyat.show', compact('umkm', 'products'));
    }

    public function manage($token)
    {
        $umkm = Umkm::where('manage_token', $token)->firstOrFail();
        return view('public.umkm_rakyat.dashboard', compact('umkm'));
    }

    public function manageProducts($token)
    {
        $umkm = Umkm::where('manage_token', $token)->firstOrFail();
        $products = $umkm->products()->latest()->get();
        return view('public.umkm_rakyat.manage_products', compact('umkm', 'products'));
    }

    public function manageSettings($token)
    {
        $umkm = Umkm::where('manage_token', $token)->firstOrFail();
        $desas = Desa::orderBy('nama_desa')->get();
        $umkmCategories = Umkm::getStandardCategories();
        return view('public.umkm_rakyat.settings', compact('umkm', 'desas', 'umkmCategories'));
    }

    public function updateSettings(Request $request, $token)
    {
        $umkm = Umkm::where('manage_token', $token)->firstOrFail();

        $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'nik' => 'required|string|digits:16',
            'no_wa' => 'required|string|max:20',
            'desa' => 'required|string',
            'patokan_lokasi' => 'nullable|string|max:255',
            'jenis_usaha' => 'required|string',
            'deskripsi' => 'nullable|string',
            'foto_usaha' => 'nullable|image|max:2048',
            'tokopedia_url' => 'nullable|active_url',
            'shopee_url' => 'nullable|active_url',
            'tiktok_url' => 'nullable|active_url',
        ]);

        $data = $request->except('foto_usaha');

        if ($request->hasFile('foto_usaha')) {
            $path = $request->file('foto_usaha')->store('umkm/usaha', 'public');
            $data['foto_usaha'] = $path;
        }

        $umkm->update($data);

        return back()->with('success', 'Profil usaha berhasil diperbarui.');
    }

    public function storeProduct(Request $request, $token)
    {
        $umkm = Umkm::where('manage_token', $token)->firstOrFail();

        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'satuan_harga' => 'nullable|string|max:50',
            'foto_produk' => 'nullable|image|max:2048',
        ]);

        $product = new UmkmProduct($request->except('foto_produk'));
        $product->umkm_id = $umkm->id;
        $product->is_available = true;

        if ($request->hasFile('foto_produk')) {
            $path = $request->file('foto_produk')->store('umkm/products', 'public');
            $product->foto_produk = $path;
        }

        $product->save();

        return back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function deleteProduct($token, $productId)
    {
        $umkm = Umkm::where('manage_token', $token)->firstOrFail();
        $product = UmkmProduct::where('umkm_id', $umkm->id)->findOrFail($productId);
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    public function toggleProductAvailability($token, $productId)
    {
        $umkm = Umkm::where('manage_token', $token)->firstOrFail();
        $product = UmkmProduct::where('umkm_id', $umkm->id)->findOrFail($productId);
        
        $product->is_available = !$product->is_available;
        $product->save();

        return back()->with('success', 'Status ketersediaan produk diperbarui.');
    }

    public function allProducts(Request $request)
    {
        $query = UmkmProduct::whereHas('umkm', function ($q) {
            $q->where('status', Umkm::STATUS_AKTIF);
        });

        if ($request->has('q')) {
            $query->where('nama_produk', 'like', '%' . $request->q . '%')
                ->orWhere('deskripsi', 'like', '%' . $request->q . '%');
        }

        $products = $query->latest()->paginate(16);
        return view('public.umkm_rakyat.all_products', compact('products'));
    }

    public function nearby(Request $request)
    {
        // Simple placeholder for nearby logic
        // In real app, we'd use lat/lng from request
        $umkms = Umkm::where('status', Umkm::STATUS_AKTIF)->latest()->paginate(12);
        return view('public.umkm_rakyat.nearby', compact('umkms'));
    }

    public function login()
    {
        return view('public.umkm_rakyat.login');
    }

    public function sendAccessLink(Request $request)
    {
        $request->validate([
            'no_wa' => 'required|string|max:20',
        ]);

        // Normalize phone number (remove non-digits)
        $inputWa = preg_replace('/[^0-9]/', '', $request->no_wa);

        // Try to find UMKM by exact match or similar (handling 0 vs 62)
        $umkm = Umkm::where('no_wa', 'like', '%' . $inputWa . '%')
            ->orWhere('no_wa', 'like', '%' . ltrim($inputWa, '0') . '%')
            ->first();

        if ($umkm) {
            $waStatus = $this->sendWhatsAppMagicLink($umkm);
            
            // Jika bot offline, kita arahkan langsung (HANYA untuk bypass darurat/testing)
            if (!$waStatus['success']) {
                return redirect($waStatus['url'])->with('warning', 'Bot WhatsApp Sedang Offline. Anda dialihkan langsung ke Dasbor melalui mode Bypass.');
            }

            return view('public.umkm_rakyat.login_success', compact('umkm'));
        }

        return back()->with('error', 'Nomor WhatsApp tidak ditemukan. Pastikan Anda sudah mendaftar.');
    }

    /**
     * Kirim Magic Link via WhatsApp
     */
    private function sendWhatsAppMagicLink($umkm)
    {
        $dashboardUrl = route('umkm_rakyat.manage', $umkm->manage_token);

        try {
            $wahaSettings = WahaN8nSetting::getSettings();

            if (!$wahaSettings || !$wahaSettings->isBotOperational()) {
                return ['success' => false, 'url' => $dashboardUrl];
            }

            // Normalize phone: strip leading 0, ensure starts with 62
            $phone = preg_replace('/[^0-9]/', '', $umkm->no_wa);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '62')) {
                $phone = '62' . $phone;
            }

            $message = "🔐 *Info Akses Dasbor UMKM*\n\n" .
                       "Halo *{$umkm->nama_pemilik}*,\n" .
                       "Seseorang (atau Anda sendiri) meminta akses untuk mengelola etalase toko *{$umkm->nama_usaha}* di portal Kecamatan Digital.\n\n" .
                       "Klik tautan aman di bawah ini untuk masuk ke Dashboard langsung tanpa password:\n" .
                       "{$dashboardUrl}\n\n" .
                       "_PENTING: Link ini adalah akses sensitif yang mengizinkan pengeditan data produk toko Anda. JANGAN BAGIKAN link ini kepada siapa pun._";

            // Use direct WAHA sendText endpoint
            $wahaUrl = $wahaSettings->waha_api_url;
            $wahaKey = $wahaSettings->waha_api_key;
            $session = $wahaSettings->waha_session_name ?? 'default';

            if ($wahaUrl) {
                $headers = ['Content-Type' => 'application/json'];
                if ($wahaKey) {
                    $headers['X-Api-Key'] = $wahaKey;
                }

                $response = Http::withHeaders($headers)
                    ->timeout(8)
                    ->post(rtrim($wahaUrl, '/') . '/api/sendText', [
                        'session' => $session,
                        'chatId' => $phone . '@c.us',
                        'text' => $message,
                    ]);
                    
                if ($response->successful()) {
                     return ['success' => true, 'url' => $dashboardUrl];
                }
            }
            return ['success' => false, 'url' => $dashboardUrl];
        } catch (\Exception $e) {
            Log::error('WA Magic Link gagal dikirim untuk UMKM: ' . $e->getMessage());
            return ['success' => false, 'url' => $dashboardUrl];
        }
    }
}
