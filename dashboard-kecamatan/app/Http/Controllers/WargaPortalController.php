<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\WorkDirectory;
use App\Models\UmkmLocal;
use App\Models\WahaN8nSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WargaPortalController extends Controller
{
    public function login()
    {
        return view('public.warga.login');
    }

    public function requestAccess(Request $request)
    {
        $request->validate([
            'no_wa' => 'required|string|max:20',
        ]);

        $inputWa = preg_replace('/[^0-9]/', '', $request->no_wa);
        $basePhone = $inputWa;
        if (str_starts_with($inputWa, '62')) {
            $basePhone = substr($inputWa, 2);
        } elseif (str_starts_with($inputWa, '0')) {
            $basePhone = substr($inputWa, 1);
        }
        $likeClause = '%' . ltrim($basePhone, '0') . '%';

        // Check UMKM
        $umkm = Umkm::where('no_wa', 'like', $likeClause)->first();

        // Check Jasa
        $jasa = WorkDirectory::where('contact_phone', 'like', $likeClause)->first();

        // Check UmkmLocal (Quick Product Listing)
        $umkmLocal = UmkmLocal::where('contact_wa', 'like', $likeClause)->first();

        if ($umkm || $jasa || $umkmLocal) {
            // Find authoritative phone number. Prefer UMKM's, then Jasa's, then UMKM Local.
            $authPhone = $umkm ? $umkm->no_wa : ($jasa ? $jasa->contact_phone : $umkmLocal->contact_wa);
            $name = $umkm ? $umkm->nama_pemilik : ($jasa ? $jasa->display_name : $umkmLocal->name);

            // Normalize for sending WA
            $phoneSend = preg_replace('/[^0-9]/', '', $authPhone);
            if (str_starts_with($phoneSend, '0')) {
                $phoneSend = '62' . substr($phoneSend, 1);
            } elseif (!str_starts_with($phoneSend, '62')) {
                $phoneSend = '62' . $phoneSend;
            }

            // Generate signed URL
            // Route 'warga.verify' takes {phone}
            $signedUrl = URL::temporarySignedRoute(
                'portal_warga.verify', now()->addDays(30), ['phone' => $phoneSend]
            );

            // Send via WAHA
            $waStatus = $this->sendWhatsAppMagicLink($phoneSend, $name, $signedUrl);

            if (!$waStatus) {
                // If bot is offline, fallback logic...
                // Only for testing/bypass
                return redirect($signedUrl)->with('warning', 'Tautan Rahasia berhasil dibuat. Bot WhatsApp Sedang Offline (Dalam Mode Bypass).');
            }

            return view('public.warga.login_success', ['phone' => $authPhone]);
        }

        return back()->with('error', 'Nomor WhatsApp tidak ditemukan di database UMKM maupun Jasa. Pastikan Anda sudah mendaftar terlebih dahulu.')->withInput();
    }

    public function verify(Request $request, $phone)
    {
        if (! $request->hasValidSignature()) {
            return redirect()->route('portal_warga.login')->with('error', 'Link akses tidak valid atau sudah kadaluarsa. Silakan request link baru.');
        }

        // Simpan sesi login warga
        session(['warga_phone' => $phone]);

        return redirect()->route('portal_warga.dashboard')->with('success', 'Berhasil terautentikasi.');
    }

    public function dashboard(Request $request)
    {
        $phone = session('warga_phone');
        
        if (!$phone) {
            return redirect()->route('portal_warga.login')->with('error', 'Sesi berakhir. Silakan login kembali.');
        }

        // Get matching phone numbers
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $basePhone = $cleanPhone;
        if (str_starts_with($cleanPhone, '62')) {
            $basePhone = substr($cleanPhone, 2);
        } elseif (str_starts_with($cleanPhone, '0')) {
            $basePhone = substr($cleanPhone, 1);
        }
        $likeClause = '%' . ltrim($basePhone, '0') . '%';

        // Fetch User's Assets
        $umkms = Umkm::where('no_wa', 'like', $likeClause)->get();
        $jasas = WorkDirectory::where('contact_phone', 'like', $likeClause)->get();
        $umkmLocals = UmkmLocal::where('contact_wa', 'like', $likeClause)->get();

        // Group UmkmLocal by phone number to handle shops with multiple products
        $consolidatedUmkmLocals = $umkmLocals->groupBy('contact_wa')->map(function ($group) {
            $first = $group->first();
            $first->product_count = $group->count(); // Add count for UI
            $first->all_products = $group->pluck('product')->join(', ');
            return $first;
        })->values();

        // Merge structured assets and prevent duplicates
        $allAssets = collect();
        
        // 1. Process UMKMs (and attach local product counts if any)
        foreach ($umkms as $item) {
            // Find corresponding local products by name
            $matchedLocal = $consolidatedUmkmLocals->first(function($loc) use ($item) {
                return strtolower(trim($loc->name)) === strtolower(trim($item->nama_usaha));
            });

            if ($matchedLocal) {
                // Attach product counts to the main UMKM item
                $item->product_count = $matchedLocal->product_count;
                $item->all_products = $matchedLocal->all_products;
                // Remove from local array so it isn't rendered twice
                $consolidatedUmkmLocals = $consolidatedUmkmLocals->reject(fn($loc) => $loc->id === $matchedLocal->id);
            }

            $allAssets->push([
                'data' => $item, 
                'type' => 'umkm', 
                'name' => $item->nama_usaha,
                'name_cooldown' => $item->name_updated_at && $item->name_updated_at->diffInDays(now()) < 30 ? 30 - $item->name_updated_at->diffInDays(now()) : 0
            ]);
        }

        // 2. Add remaining standalone UMKMLocals
        foreach ($consolidatedUmkmLocals as $item) {
            $allAssets->push([
                'data' => $item, 
                'type' => 'umkm_local', 
                'name' => $item->name,
                'name_cooldown' => $item->name_updated_at && $item->name_updated_at->diffInDays(now()) < 30 ? 30 - $item->name_updated_at->diffInDays(now()) : 0
            ]);
        }

        // 3. Add Jasa
        foreach ($jasas as $item) {
            $allAssets->push([
                'data' => $item, 
                'type' => 'jasa', 
                'name' => $item->job_title,
                'name_cooldown' => $item->name_updated_at && $item->name_updated_at->diffInDays(now()) < 30 ? 30 - $item->name_updated_at->diffInDays(now()) : 0
            ]);
        }

        return view('public.warga.dashboard', [
            'allAssets' => $allAssets,
            'umkms' => $umkms,
            'jasas' => $jasas,
            'umkmLocals' => $umkmLocals, // Keep raw if needed elsewhere
            'phone' => $phone
        ]);
    }

    public function bridgeJasa($id)
    {
        $phone = session('warga_phone');
        if (!$phone) return redirect()->route('portal_warga.login');

        // Verify this $id actually belongs to the user
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $basePhone = $cleanPhone;
        if (str_starts_with($cleanPhone, '62')) {
            $basePhone = substr($cleanPhone, 2);
        } elseif (str_starts_with($cleanPhone, '0')) {
            $basePhone = substr($cleanPhone, 1);
        }
        $checkPhone = ltrim($basePhone, '0');

        $jasa = WorkDirectory::findOrFail($id);
        
        // Match checking
        if (!str_contains(preg_replace('/[^0-9]/', '', $jasa->contact_phone), $checkPhone)) {
             return redirect()->route('portal_warga.dashboard')->with('error', 'Anda tidak memiliki akses ke jasa ini.');
        }

        // Grant access
        session(['manage_jasa_id' => $jasa->id]);
        return redirect()->route('economy.manage', $jasa->id);
    }

    public function logout()
    {
        session()->forget('warga_phone');
        session()->forget('manage_jasa_id');
        return redirect()->route('landing')->with('success', 'Anda telah keluar dari Dasbor Warga.');
    }

    /**
     * Update Operational Hours & Holiday Status (Masyarakat friendly)
     */
    public function updateOperationalStatus(Request $request)
    {
        $request->validate([
            'type' => 'required|in:umkm,jasa,umkm_local',
            'id' => 'required',
            'is_on_holiday' => 'required|boolean',
            'operating_hours' => 'nullable|string|max:50',
        ]);

        $model = null;
        if ($request->type === 'umkm') $model = Umkm::find($request->id);
        if ($request->type === 'jasa') $model = WorkDirectory::find($request->id);
        if ($request->type === 'umkm_local') $model = UmkmLocal::find($request->id);

        if (!$model) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        // Simple security: Check if phone matches session
        $phone = session('warga_phone');
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $basePhone = ltrim($cleanPhone, '0');
        if (str_starts_with($basePhone, '62')) $basePhone = substr($basePhone, 2);

        $modelPhone = ltrim(preg_replace('/[^0-9]/', '', $request->type === 'umkm' ? $model->no_wa : ($request->type === 'jasa' ? $model->contact_phone : $model->contact_wa)), '0');
        if (str_starts_with($modelPhone, '62')) $modelPhone = substr($modelPhone, 2);

        if (!str_contains($modelPhone, $basePhone)) {
            return back()->with('error', 'Akses ditolak.');
        }

        // Update correct fields
        if ($request->type === 'umkm_local') {
            // Global update for all products with same phone
            UmkmLocal::where('contact_wa', $model->contact_wa)->update([
                'is_on_holiday' => $request->is_on_holiday,
                'operating_hours' => $request->operating_hours ?: $model->operating_hours
            ]);
        } else {
            $model->update([
                'is_on_holiday' => $request->is_on_holiday,
                'operating_hours' => $request->operating_hours ?: $model->operating_hours
            ]);
        }

        $statusLabel = $request->is_on_holiday ? 'diliburkan' : 'diaktifkan kembali';
        return back()->with('success', "Status berhasil diperbarui! Toko/Jasa Anda kini {$statusLabel}.");
    }

    /**
     * Update Business/Service name from Super Dashboard
     */
    public function updateName(Request $request)
    {
        $request->validate([
            'type' => 'required|in:umkm,jasa,umkm_local',
            'id' => 'required',
            'name' => 'required|string|max:255',
        ]);

        $model = null;
        if ($request->type === 'umkm') $model = Umkm::find($request->id);
        if ($request->type === 'jasa') $model = WorkDirectory::find($request->id);
        if ($request->type === 'umkm_local') $model = UmkmLocal::find($request->id);

        if (!$model) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        // Security check
        $phone = session('warga_phone');
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $basePhone = ltrim($cleanPhone, '0');
        if (str_starts_with($basePhone, '62')) $basePhone = substr($basePhone, 2);

        $modelPhone = ltrim(preg_replace('/[^0-9]/', '', $request->type === 'umkm' ? $model->no_wa : ($request->type === 'jasa' ? $model->contact_phone : $model->contact_wa)), '0');
        if (str_starts_with($modelPhone, '62')) $modelPhone = substr($modelPhone, 2);

        if (!str_contains($modelPhone, $basePhone)) {
            return back()->with('error', 'Akses ditolak.');
        }

        // 30-day Cooldown Check
        $lastUpdate = $model->name_updated_at;
        if ($lastUpdate && $lastUpdate->diffInDays(now()) < 30) {
            $daysLeft = 30 - $lastUpdate->diffInDays(now());
            return back()->with('error', "Nama toko/jasa hanya bisa diubah setiap 30 hari. Silakan tunggu {$daysLeft} hari lagi.");
        }

        // Update correct field
        if ($request->type === 'umkm_local') {
            // Global update for all products with same phone
            UmkmLocal::where('contact_wa', $model->contact_wa)->update([
                'name' => $request->name,
                'name_updated_at' => now()
            ]);
        } else {
            $fieldName = $request->type === 'umkm' ? 'nama_usaha' : ($request->type === 'jasa' ? 'job_title' : 'name');
            $model->update([
                $fieldName => $request->name,
                'name_updated_at' => now()
            ]);
        }

        return back()->with('success', "Identitas berhasil diubah menjadi '{$request->name}'!");
    }

    private function sendWhatsAppMagicLink($phone, $name, $url)
    {
        try {
            $wahaSettings = WahaN8nSetting::getSettings();
            if (!$wahaSettings || !$wahaSettings->isBotOperational()) return false;

            $message = "🔐 *Pusat Kendali Profil Warga*\n\n" .
                       "Halo *{$name}*,\n" .
                       "Seseorang (atau Anda sendiri) meminta akses untuk masuk ke Dasbor Ekonomi & UMKM Kecamatan Digital.\n\n" .
                       "Klik tautan aman di bawah ini untuk mengelola profil *UMKM* atau *Jasa/Pekerjaan* Anda secara langsung tanpa PIN maupun Password:\n" .
                       "{$url}\n\n" .
                       "_PENTING: Tautan ini akan mengelola data warga Anda di aplikasi kecamatan. JANGAN BAGIKAN link ini ke siapapun._";

            $wahaUrl = $wahaSettings->waha_api_url;
            $wahaKey = $wahaSettings->waha_api_key;
            $session = $wahaSettings->waha_session_name ?? 'default';

            if ($wahaUrl) {
                $headers = ['Content-Type' => 'application/json'];
                if ($wahaKey) $headers['X-Api-Key'] = $wahaKey;

                $response = Http::withHeaders($headers)->timeout(8)->post(rtrim($wahaUrl, '/') . '/api/sendText', [
                    'session' => $session,
                    'chatId' => $phone . '@c.us',
                    'text' => $message,
                ]);

                return $response->successful();
            }
            return false;
        } catch (\Exception $e) {
            Log::error('WA Magic Link gagal dikirim untuk Warga Dashboard: ' . $e->getMessage());
            return false;
        }
    }
}
