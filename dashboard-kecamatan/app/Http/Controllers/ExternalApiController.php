<?php

namespace App\Http\Controllers;

use App\Models\UmkmLocal;
use App\Models\Loker;
use App\Models\AppProfile;
use App\Models\PelayananFaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExternalApiController extends Controller
{
    /**
     * Mask phone number for privacy
     * Format: 0812xxxx890
     */
    private function maskPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) < 10) {
            return $phone;
        }

        return substr($phone, 0, 4) . 'xxxx' . substr($phone, -3);
    }

    /**
     * Generate WhatsApp link from phone number
     */
    private function getWaLink(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert 08 to 628 for wa.me
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }

        return 'wa.me/' . $phone;
    }

    /**
     * Search UMKM for WhatsApp Bot
     * With security filtering and phone masking
     */
    public function searchUmkm(Request $request)
    {
        $query = trim($request->query('q', ''));

        $umkms = UmkmLocal::where('is_active', true)
            ->where('is_verified', true)
            ->where('is_flagged', false)
            ->where('module', UmkmLocal::MODULE_UMKM)
            ->when($query && $query !== 'semua', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('product', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('address', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->limit(5)
            ->get();

        $data = $umkms->map(function ($item) {
            return [
                'name' => $item->name,
                'product' => $item->product,
                'address' => $item->address ?? 'Kecamatan Besuk',
                'contact_wa' => $this->maskPhone($item->contact_wa),
                'contact_link' => $this->getWaLink($item->contact_wa),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'count' => $data->count(),
            'website_link' => 'https://besuk.probolinggokab.go.id/umkm'
        ]);
    }

    /**
     * Search Jasa/Services for WhatsApp Bot
     * With security filtering and phone masking
     */
    public function searchJasa(Request $request)
    {
        $query = trim($request->query('q', ''));

        $jasas = umkmLocal::where('is_active', true)
            ->where('is_verified', true)
            ->where('is_flagged', false)
            ->where('module', umkmLocal::MODULE_JASA)
            ->when($query && $query !== 'semua', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('product', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('address', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->limit(5)
            ->get();

        $data = $jasas->map(function ($item) {
            return [
                'name' => $item->name,
                'product' => $item->product,
                'address' => $item->address ?? 'Kecamatan Besuk',
                'contact_wa' => $this->maskPhone($item->contact_wa),
                'contact_link' => $this->getWaLink($item->contact_wa),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'count' => $data->count(),
            'website_link' => 'https://besuk.probolinggokab.go.id/jasa'
        ]);
    }

    /**
     * Search Job Vacancies (Loker) for WhatsApp Bot
     * With security filtering and phone masking
     */
    public function searchLoker(Request $request)
    {
        $query = trim($request->query('q', ''));

        $lokers = Loker::where('status', Loker::STATUS_AKTIF)
            ->where('is_verified', true)
            ->where('is_flagged', false)
            ->when($query && $query !== 'semua', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('title', 'like', "%{$query}%")
                        ->orWhere('job_category', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->limit(5)
            ->get();

        $data = $lokers->map(function ($item) {
            $desaName = $item->nama_desa_manual ?? ($item->desa ? $item->desa->name : 'Kecamatan Besuk');
            return [
                'title' => $item->title,
                'job_category' => $item->job_category,
                'company' => $item->description ? substr(strip_tags($item->description), 0, 50) . '...' : '-',
                'location' => $desaName,
                'work_time' => $item->work_time ?? '-',
                'contact_wa' => $this->maskPhone($item->contact_wa),
                'contact_link' => $this->getWaLink($item->contact_wa),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'count' => $data->count(),
            'website_link' => 'https://besuk.probolinggokab.go.id/loker'
        ]);
    }

    /**
     * Search FAQ for WhatsApp Bot
     * With module filtering
     */
    public function searchFaq(Request $request)
    {
        $query = trim($request->query('q', ''));
        $module = $request->query('module', 'pelayanan');

        $faqs = PelayananFaq::query()
            ->where('is_active', true)
            ->where('module', $module)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('question', 'like', "%{$query}%")
                        ->orWhere('answer', 'like', "%{$query}%")
                        ->orWhere('keywords', 'like', "%{$query}%")
                        ->orWhere('category', 'like', "%{$query}%");
                });
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $data = $faqs->map(function ($item) {
            return [
                'category' => $item->category,
                'question' => $item->question,
                'answer' => $item->answer,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'count' => $data->count(),
            'module' => $module
        ]);
    }

    /**
     * Get Bot Configuration (AI Status, etc.)
     */
    public function getConfig()
    {
        $profile = AppProfile::first(['whatsapp_bot_number', 'is_ai_active']);

        return response()->json([
            'success' => true,
            'data' => [
                'is_ai_active' => (bool) ($profile->is_ai_active ?? false),
                'bot_number' => $profile->whatsapp_bot_number ?? '',
                'ai_message' => 'admin bisa menggunakan ai untuk memori agar lebih sempurna'
            ]
        ]);
    }

    /**
     * Verify owner PIN
     * 
     * POST /api/v1/external/owner/verify-pin
     */
    public function verifyOwnerPin(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'pin' => 'required|string|size:6',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
        $phoneVariants = $this->getPhoneVariants($phone);

        // Check UMKM/Jasa
        $umkm = umkmLocal::whereIn('contact_wa', $phoneVariants)
            ->where('owner_pin', $validated['pin'])
            ->first();

        if ($umkm) {
            return response()->json([
                'success' => true,
                'data' => [
                    'owner_phone' => $phone,
                    'listing_id' => $umkm->id,
                    'listing_type' => $umkm->module ?? 'umkm',
                    'listing_name' => $umkm->name,
                ]
            ]);
        }

        // Check Loker
        $loker = Loker::whereIn('contact_wa', $phoneVariants)
            ->where('owner_pin', $validated['pin'])
            ->first();

        if ($loker) {
            return response()->json([
                'success' => true,
                'data' => [
                    'owner_phone' => $phone,
                    'listing_id' => $loker->id,
                    'listing_type' => 'loker',
                    'listing_name' => $loker->title,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'PIN salah atau tidak ditemukan'
        ], 401);
    }

    /**
     * Get owner's listings by phone number
     * 
     * GET /api/v1/external/owner/listings?phone=6281234567890
     */
    public function getOwnerListings(Request $request)
    {
        $phone = preg_replace('/[^0-9]/', '', $request->query('phone', ''));

        if (empty($phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter phone wajib diisi'
            ], 400);
        }

        // Normalize phone for comparison (handle 08, 628, +628)
        $phoneVariants = $this->getPhoneVariants($phone);

        // Search UMKM
        $umkms = umkmLocal::whereIn('contact_wa', $phoneVariants)
            ->orWhereIn('owner_phone', $phoneVariants)
            ->get(['id', 'name', 'product', 'is_listed', 'is_active', 'module']);

        $listings = [];

        foreach ($umkms as $umkm) {
            $listings[] = [
                'id' => $umkm->id,
                'type' => $umkm->module ?? 'umkm',
                'name' => $umkm->name,
                'product' => $umkm->product,
                'is_listed' => (bool) $umkm->is_listed,
                'is_active' => (bool) $umkm->is_active
            ];
        }

        if (empty($listings)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada listing ditemukan untuk nomor ini'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'owner_phone' => $phone,
                'listings' => $listings
            ]
        ]);
    }

    /**
     * Toggle listing visibility with PIN verification
     * 
     * POST /api/v1/external/owner/toggle-listing
     */
    public function toggleListing(Request $request)
    {
        $validated = $request->validate([
            'listing_id' => 'required|integer',
            'listing_type' => 'required|in:umkm,jasa,loker',
            'action' => 'required|in:open,close',
            'phone' => 'required|string',
            'pin' => 'required|string|size:6',
        ]);

        $listing = null;

        if ($validated['listing_type'] === 'umkm' || $validated['listing_type'] === 'jasa') {
            $listing = umkmLocal::find($validated['listing_id']);
        } elseif ($validated['listing_type'] === 'loker') {
            $listing = Loker::find($validated['listing_id']);
        }

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing tidak ditemukan'
            ], 404);
        }

        // Verify PIN
        if ($listing->owner_pin !== $validated['pin']) {
            return response()->json([
                'success' => false,
                'message' => 'PIN salah'
            ], 401);
        }

        // Verify phone ownership
        $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
        $phoneVariants = $this->getPhoneVariants($phone);

        if (!in_array($listing->contact_wa, $phoneVariants)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor tidak sesuai dengan listing'
            ], 403);
        }

        // Toggle status
        $newStatus = $validated['action'] === 'open';

        if ($listing instanceof umkmLocal) {
            $listing->is_listed = $newStatus;
        } else {
            $listing->status = $newStatus ? Loker::STATUS_AKTIF : Loker::STATUS_NONAKTIF;
        }

        $listing->last_toggle_at = now();
        $listing->save();

        Log::info('Listing toggled via WhatsApp API', [
            'listing_id' => $listing->id,
            'listing_type' => $validated['listing_type'],
            'action' => $validated['action'],
            'new_status' => $newStatus,
            'phone' => $phone
        ]);

        $listingName = $listing->name ?? $listing->title;

        return response()->json([
            'success' => true,
            'message' => $newStatus
                ? "Lapak \"{$listingName}\" telah dibuka"
                : "Lapak \"{$listingName}\" telah ditutup",
            'data' => [
                'id' => $listing->id,
                'type' => $validated['listing_type'],
                'is_open' => $newStatus
            ]
        ]);
    }

    /**
     * Generate phone number variants for matching
     */
    private function getPhoneVariants(string $phone): array
    {
        $variants = [$phone];

        // If starts with 08, add 628 variant
        if (str_starts_with($phone, '08')) {
            $variants[] = '62' . substr($phone, 1);
        }

        // If starts with 628, add 08 variant
        if (str_starts_with($phone, '628')) {
            $variants[] = '0' . substr($phone, 2);
        }

        // If starts with +62, add variants without +
        if (str_starts_with($phone, '+62')) {
            $withoutPlus = substr($phone, 1);
            $variants[] = $withoutPlus;
            if (str_starts_with($withoutPlus, '628')) {
                $variants[] = '0' . substr($withoutPlus, 2);
            }
        }

        return array_unique($variants);
    }
}
