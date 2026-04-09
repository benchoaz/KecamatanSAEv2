<?php

namespace App\Http\Controllers;

use App\Models\WorkDirectory;
use App\Models\Desa;
use App\Models\PublicService;
use App\Models\WahaN8nSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EconomyController extends Controller
{
    /**
     * Display listing of economy (Jobs & UMKM)
     */
    public function index(Request $request)
    {
        $query = WorkDirectory::public();

        // Filter by category if provided
        if ($request->filled('kategori')) {
            $query->where('job_category', $request->kategori);
        }

        // Filter by type if provided
        if ($request->filled('tipe')) {
            $query->where('job_type', $request->tipe);
        }

        // Search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                    ->orWhere('job_title', 'like', "%{$search}%")
                    ->orWhere('service_area', 'like', "%{$search}%");
            });
        }

        $workItems = $query->latest()->paginate(12);

        // Get categories for filter
        $categories = WorkDirectory::public()
            ->select('job_category')
            ->distinct()
            ->pluck('job_category');

        // Ambil UMKM Resmi (Verified) - Prioritas utama
        $officialUmkms = \App\Models\Umkm::where('status', 'aktif')->latest()->limit(4)->get();

        // Ambil UMKM Lokal (Quick Directory)
        $localUmkms = \App\Models\UmkmLocal::where('is_active', true)->latest()->limit(8)->get();

        $defaultTab = $request->get('tab', 'jasa');

        return view('economy.index', compact('workItems', 'categories', 'officialUmkms', 'localUmkms', 'defaultTab'));
    }

    /**
     * Display single work directory item
     */
    public function show($id)
    {
        $workItem = WorkDirectory::public()->findOrFail($id);

        // Get related items (same category, exclude current)
        $relatedItems = WorkDirectory::public()
            ->where('job_category', $workItem->job_category)
            ->where('id', '!=', $workItem->id)
            ->limit(3)
            ->get();

        return view('economy.show', compact('workItem', 'relatedItems'));
    }

    /**
     * Display detail page of a single UMKM store (UmkmLocal)
     */
    public function showProduk($id)
    {
        $produk = \App\Models\UmkmLocal::where('is_active', true)->findOrFail($id);

        // Semua produk dari toko yang sama (berdasarkan nama toko)
        $produkLainnya = \App\Models\UmkmLocal::where('is_active', true)
            ->where('name', $produk->name)
            ->where('id', '!=', $produk->id)
            ->get();

        return view('economy.show_produk', compact('produk', 'produkLainnya'));
    }

    /**
     * Show registration form for Pekerjaan & Jasa
     */
    public function create()
    {
        $desas = Desa::orderBy('nama_desa')->get();
        $categories = WorkDirectory::getCategories();
        $jobTypes = ['jasa' => 'Jasa', 'transportasi' => 'Transportasi', 'keliling' => 'Keliling', 'harian' => 'Harian'];

        return view('economy.create', compact('desas', 'categories', 'jobTypes'));
    }

    /**
     * Store new Pekerjaan & Jasa registration
     */
    public function store(Request $request)
    {
        $request->validate([
            'display_name' => 'required|string|max:255',
            'job_category' => 'required|string',
            'job_type' => 'required|string|in:jasa,transportasi,keliling,harian',
            'job_title' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'service_area' => 'nullable|string|max:255',
            'service_time' => 'nullable|string|max:100',
            'short_description' => 'nullable|string|max:500',
        ]);

        // Create WorkDirectory entry
        $workDir = WorkDirectory::create([
            'display_name' => $request->display_name,
            'job_category' => $request->job_category,
            'job_type' => $request->job_type,
            'job_title' => $request->job_title,
            'contact_phone' => $request->contact_phone,
            'owner_pin' => null, // PIN dihapus, sistem menggunakan Magic Link
            'service_area' => $request->service_area,
            'service_time' => $request->service_time,
            'short_description' => $request->short_description,
            'consent_public' => true,
            'status' => 'pending',
            'data_source' => 'web_form'
        ]);

        // Create Public Service entry for Inbox
        PublicService::create([
            'uuid' => (string) Str::uuid(),
            'desa_id' => $request->desa_id,
            'nama_pemohon' => $workDir->display_name,
            'jenis_layanan' => 'Pendaftaran Pekerjaan & Jasa',
            'uraian' => "Pendaftaran Pekerjaan/Jasa Baru: {$workDir->job_title} ({$workDir->job_category}). Atas nama: {$workDir->display_name}. Kontak: {$workDir->contact_phone}.", // Harusan PIN dihapus
            'whatsapp' => $workDir->contact_phone,
            'status' => PublicService::STATUS_MENUNGGU,
            'category' => PublicService::CATEGORY_PEKERJAAN,
            'source' => 'web_form'
        ]);

        // Send WhatsApp notification
        $this->sendWhatsAppNotification($workDir);

        return redirect()->route('economy.index', ['tab' => 'jasa'])->with('success', 'Terima kasih. Data pekerjaan/jasa Anda akan ditampilkan setelah diverifikasi.');
    }

    /**
     * Redirect Jasa owner to Portal Warga (PIN-less)
     */
    public function loginForm()
    {
        return redirect()->route('portal_warga.login');
    }

    /**
     * Legacy PIN-based auth (Redirect to Portal)
     */
    public function authenticate(Request $request)
    {
        return redirect()->route('portal_warga.login');
    }

    /**
     * Show management page for a specific Jasa
     */
    public function manage($id)
    {
        if (session('manage_jasa_id') != $id) {
            return redirect()->route('economy.login')->with('error', 'Sesi berakhir. Silakan login kembali.');
        }

        $workItem = WorkDirectory::findOrFail($id);
        $desas = Desa::orderBy('nama_desa')->get();
        $categories = WorkDirectory::getCategories();
        $jobTypes = ['jasa' => 'Jasa', 'transportasi' => 'Transportasi', 'keliling' => 'Keliling', 'harian' => 'Harian'];

        return view('economy.manage', compact('workItem', 'desas', 'categories', 'jobTypes'));
    }

    /**
     * Update Jasa data
     */
    public function update(Request $request, $id)
    {
        if (session('manage_jasa_id') != $id) {
            return redirect()->route('economy.login');
        }

        $workItem = WorkDirectory::findOrFail($id);

        $request->validate([
            'display_name' => 'required|string|max:255',
            'job_category' => 'required|string',
            'job_type' => 'required|string|in:jasa,transportasi,keliling,harian',
            'job_title' => 'required|string|max:255',
            'service_area' => 'nullable|string|max:255',
            'service_time' => 'nullable|string|max:100',
            'short_description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,pending'
        ]);

        $workItem->update($request->all());

        return back()->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Send WhatsApp notification (PIN-less)
     */
    private function sendWhatsAppNotification($workDir)
    {
        try {
            $wahaSettings = WahaN8nSetting::getSettings();

            if (!$wahaSettings || !$wahaSettings->isBotOperational()) {
                return;
            }

            // Normalize phone: strip leading 0, ensure starts with 62
            $phone = preg_replace('/[^0-9]/', '', $workDir->contact_phone);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '62')) {
                $phone = '62' . $phone;
            }

            $message = "✅ *Pendaftaran Berhasil!*

";
            $message .= "Halo *{$workDir->display_name}*, terima kasih telah mendaftar di layanan Pekerjaan & Jasa Kecamatan.

";
            $message .= "━━━━━━━━━━━━━━━━━\n";
            $message .= "📝 *Layanan:* {$workDir->job_title}\n";
            $message .= "📅 *Tanggal:* " . now()->format('d/m/Y H:i') . " WIB\n";
            $message .= "━━━━━━━━━━━━━━━━━\n\n";
            $message .= "Untuk merubah status atau mengedit profil Jasa Anda, silakan masuk ke secara aman (tanpa PIN/Password) melalui:\n\n";
            $message .= "🌐 *Pusat Kendali Warga*: \n" . route('portal_warga.login') . "\n\n";
            $message .= "Ketik *MENU* untuk kembali.";

            // Use direct WAHA sendText endpoint
            $wahaUrl = $wahaSettings->waha_api_url;
            $wahaKey = $wahaSettings->waha_api_key;
            $session = $wahaSettings->waha_session_name ?? 'default';

            if ($wahaUrl) {
                $headers = ['Content-Type' => 'application/json'];
                if ($wahaKey) {
                    $headers['X-Api-Key'] = $wahaKey;
                }

                \Illuminate\Support\Facades\Http::withHeaders($headers)
                    ->timeout(8)
                    ->post(rtrim($wahaUrl, '/') . '/api/sendText', [
                        'session' => $session,
                        'chatId' => $phone . '@c.us',
                        'text' => $message,
                    ]);
            }
        } catch (\Exception $e) {
            \Log::warning('WA notification gagal untuk Economia: ' . $e->getMessage());
        }
    }
}
