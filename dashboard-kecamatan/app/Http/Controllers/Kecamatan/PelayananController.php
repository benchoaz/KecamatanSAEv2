<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\PublicService;
use App\Models\PelayananFaq;
use App\Models\PengunjungKecamatan;
use App\Models\MasterLayanan;
use App\Models\Desa;
use App\Models\Umkm;

use App\Models\WorkDirectory;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Traits\HasWhatsAppNotifications;

class PelayananController extends Controller
{
    use HasWhatsAppNotifications;
    /**
     * Inbox Pengaduan & Pelayanan
     */
    public function inbox(Request $request)
    {
        $category = $request->query('category', 'pelayanan');
        $search = $request->query('search');
        $statusFilter = $request->query('status');

        $query = PublicService::with('desa')->withCount('attachments');

        // Mapping logic for strict separation
        if ($category === 'pelayanan') {
            $query->where('category', PublicService::CATEGORY_PELAYANAN);
        } elseif ($category === 'ekonomi') {
            $query->whereIn('category', [PublicService::CATEGORY_UMKM, PublicService::CATEGORY_PEKERJAAN]);
        } else {
            $query->where('category', $category);
        }

        // Apply Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_pemohon', 'like', "%{$search}%")
                  ->orWhere('uraian', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%")
                  ->orWhere('uuid', 'like', "%{$search}%")
                  ->orWhere('tracking_code', 'like', "%{$search}%");
            });
        }

        // Apply Status Filter
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $complaints = $query->orderBy('created_at', 'desc')
            ->paginate(15)->withQueryString();

        return view('kecamatan.pelayanan.inbox', compact('complaints', 'category', 'search', 'statusFilter'));
    }

    /**
     * Detail Pengaduan / Pelayanan
     */
    public function show($id)
    {
        $complaint = PublicService::with(['desa', 'handler'])->findOrFail($id);

        if (in_array($complaint->category, [PublicService::CATEGORY_UMKM, PublicService::CATEGORY_PEKERJAAN])) {
            $workDir = \App\Models\WorkDirectory::where('contact_phone', $complaint->whatsapp)
                ->where('display_name', $complaint->nama_pemohon)
                ->latest()
                ->first();
            return view('kecamatan.pelayanan.ekonomi_show', compact('complaint', 'workDir'));
        }

        return view('kecamatan.pelayanan.show', compact('complaint'));
    }

    /**
     * Update Tindak Lanjut
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'internal_notes' => 'nullable|string',
            'public_response' => 'nullable|string',
            'completion_type' => 'nullable|in:digital,physical,whatsapp',
            'result_file' => 'nullable|file|mimes:pdf|max:5120',
            'ready_at' => 'nullable|date',
            'pickup_person' => 'nullable|string|max:255',
            'pickup_notes' => 'nullable|string',
            'send_whatsapp_notification' => 'nullable|boolean',
            'wa_reply_text' => 'nullable|string',
        ]);

        $complaint = PublicService::findOrFail($id);
        
        // Custom backend validation for digital completion
        if ($request->status === PublicService::STATUS_SELESAI && $request->completion_type === 'digital') {
            if (!$request->hasFile('result_file') && !$complaint->result_file_path) {
                return redirect()->back()->withErrors(['result_file' => 'Dokumen PDF hasil layanan wajib diunggah untuk diserahkan ke warga.'])->withInput();
            }
        }

        // Validation for WhatsApp reply type
        if ($request->completion_type === 'whatsapp' && empty($request->wa_reply_text)) {
            return redirect()->back()->withErrors(['wa_reply_text' => 'Teks jawaban WhatsApp wajib diisi.'])->withInput();
        }

        $oldStatus = $complaint->status;

        // For WhatsApp completion type, wa_reply_text IS the public_response
        $publicResponse = $request->completion_type === 'whatsapp'
            ? $request->wa_reply_text
            : $request->public_response;

        $updateData = [
            'status'          => $request->status,
            'internal_notes'  => $request->internal_notes,
            'public_response' => $publicResponse,
            'handled_by'      => auth()->id(),
            'handled_at'      => now(),
            'completion_type' => $request->completion_type ?? null,
            'ready_at'        => $request->ready_at ?? null,
            'pickup_person'   => $request->pickup_person ?? null,
            'pickup_notes'    => $request->pickup_notes ?? null,
        ];

        // Handle PDF upload for digital completion
        if ($request->hasFile('result_file')) {
            $path = $request->file('result_file')->store('public_services/results', 'public');
            $updateData['result_file_path'] = $path;
        }

        if (!empty($publicResponse)) {
            $updateData['responded_at'] = now();
        }

        $complaint->update($updateData);

        // EXTRA HOOK FOR UMKM & JASA: Activate the WorkDirectory record!
        if (in_array($complaint->category, [PublicService::CATEGORY_UMKM, PublicService::CATEGORY_PEKERJAAN])) {
            $workDir = \App\Models\WorkDirectory::where('contact_phone', $complaint->whatsapp)
                ->where('display_name', $complaint->nama_pemohon)
                ->latest()
                ->first();
                
            if ($workDir && $request->status === PublicService::STATUS_SELESAI) {
                $workDir->update([
                    'status' => 'active', 
                    'is_verified' => true
                ]);
            } elseif ($workDir && $request->status === PublicService::STATUS_DITOLAK) {
                $workDir->update([
                    'status' => 'inactive', 
                    'is_verified' => false
                ]);
            }
        }

        // Send WhatsApp notification
        $shouldNotify = $request->boolean('send_whatsapp_notification', true);
        if ($shouldNotify && $complaint->whatsapp) {
            if ($request->completion_type === 'whatsapp') {
                // Send the custom reply text directly (regardless of status change)
                $this->sendWaNotification($complaint->fresh(), 'wa_reply');
            } elseif ($oldStatus !== $request->status) {
                $this->sendWaNotification($complaint->fresh(), 'status_update');
            }
        }

        return redirect()->back()->with('success', 'Tindak lanjut pengaduan/pelayanan berhasil diperbarui.');
    }

    /**
     * Statistik Pelayanan
     */
    public function statistics()
    {
        $stats = [
            'total' => PublicService::count(),
            'pending' => PublicService::where('status', PublicService::STATUS_MENUNGGU)->count(),
            'processed' => PublicService::where('status', PublicService::STATUS_DIPROSES)->count(),
            'completed' => PublicService::where('status', PublicService::STATUS_SELESAI)->count(),

            // New sectoral metrics
            'umkm_total' => Umkm::count(),
            'umkm_active' => Umkm::where('status', Umkm::STATUS_AKTIF)->count(),


            // Sectoral metrics (Jobs & Workers)
            'loker_total' => JobVacancy::count(),
            'loker_active' => JobVacancy::where('is_active', true)->count(),
            'pekerja_total' => WorkDirectory::count(),
            'pekerja_public' => WorkDirectory::where('status', 'active')->where('consent_public', true)->count(),

            'by_category' => PublicService::select('jenis_layanan', DB::raw('count(*) as total'))
                ->groupBy('jenis_layanan')
                ->get(),
            'by_village' => PublicService::select('desa_id', DB::raw('count(*) as total'))
                ->with('desa')
                ->groupBy('desa_id')
                ->get(),
        ];

        return view('kecamatan.pelayanan.statistics', compact('stats'));
    }

    /**
     * Buku Tamu (Moved from Pemerintahan)
     */
    public function visitorIndex()
    {
        $visitors = PengunjungKecamatan::with('desaAsal')
            ->orderBy('status', 'desc')
            ->orderBy('jam_datang', 'desc')
            ->take(100)
            ->get();

        $desas = Desa::orderBy('nama_desa')->get();
        return view('kecamatan.pelayanan.visitor.index', compact('visitors', 'desas'));
    }

    public function visitorStore(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|digits:16',
            'desa_asal_id' => 'nullable|exists:desa,id',
            'alamat_luar' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:15',
            'tujuan_bidang' => 'required|string',
            'keperluan' => 'required|string',
        ]);

        PengunjungKecamatan::create($validated);
        return back()->with('success', 'Pengunjung berhasil didaftarkan.');
    }

    public function visitorUpdate(Request $request, $id)
    {
        $visitor = PengunjungKecamatan::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:menunggu,dilayani,selesai'
        ]);

        $visitor->update($validated);
        return back()->with('success', 'Status pengunjung berhasil diperbarui.');
    }

    /**
     * Master Layanan (Self Service)
     */
    /**
     * FAQ Administrasi (Jawaban Otomatis WhatsApp Bot)
     */
    public function faqIndex()
    {
        $faqs = PelayananFaq::orderBy('category')
            ->orderBy('priority', 'desc')
            ->get();

        return view('kecamatan.pelayanan.faq.index', compact('faqs'));
    }

    public function faqStore(Request $request)
    {
        $validated = $request->validate([
            'category'  => 'required|string|max:100',
            'keywords'  => 'required|string|max:500',
            'question'  => 'required|string|max:500',
            'answer'    => 'required|string',
        ]);

        $validated['is_active']       = true;
        $validated['priority']        = 0;
        $validated['module']          = PelayananFaq::MODULE_PELAYANAN;
        $validated['last_updated_by'] = auth()->id();

        PelayananFaq::create($validated);

        return redirect()->route('kecamatan.pelayanan.faq.index')
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function faqUpdate(Request $request, $id)
    {
        $faq = PelayananFaq::findOrFail($id);

        $validated = $request->validate([
            'category'  => 'required|string|max:100',
            'keywords'  => 'required|string|max:500',
            'question'  => 'required|string|max:500',
            'answer'    => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $validated['last_updated_by'] = auth()->id();

        $faq->update($validated);

        return redirect()->route('kecamatan.pelayanan.faq.index')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    /**
     * Master Layanan (Self Service)
     */
    public function layananIndex()
    {
        $layanan = MasterLayanan::orderBy('urutan')->get();
        return view('kecamatan.pelayanan.layanan.index', compact('layanan'));
    }

    public function layananCreate()
    {
        return view('kecamatan.pelayanan.layanan.form');
    }

    public function layananStore(Request $request)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi_syarat' => 'required|string',
            'estimasi_waktu' => 'nullable|string|max:100',
            'ikon' => 'required|string|max:100',
            'warna_bg' => 'required|string|max:100',
            'warna_text' => 'required|string|max:100',
            'is_active' => 'required|boolean',
            'is_popular' => 'nullable|boolean',
            'link_type' => 'nullable|string|in:form,loker,umkm,external',
            'custom_link' => 'nullable|string|max:255',
            'urutan' => 'required|integer',
            'attachment_requirements' => 'nullable|array',
            'attachment_requirements.*' => 'required|string|max:255',
        ]);

        MasterLayanan::create($validated);
        return redirect()->route('kecamatan.pelayanan.layanan.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function layananEdit($id)
    {
        $layanan = MasterLayanan::findOrFail($id);
        return view('kecamatan.pelayanan.layanan.form', compact('layanan'));
    }

    public function layananUpdate(Request $request, $id)
    {
        $layanan = MasterLayanan::findOrFail($id);
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi_syarat' => 'required|string',
            'estimasi_waktu' => 'nullable|string|max:100',
            'ikon' => 'required|string|max:100',
            'warna_bg' => 'required|string|max:100',
            'warna_text' => 'required|string|max:100',
            'is_active' => 'required|boolean',
            'is_popular' => 'nullable|boolean',
            'link_type' => 'nullable|string|in:form,loker,umkm,external',
            'custom_link' => 'nullable|string|max:255',
            'urutan' => 'required|integer',
            'attachment_requirements' => 'nullable|array',
            'attachment_requirements.*' => 'required|string|max:255',
        ]);

        $layanan->update($validated);
        return redirect()->route('kecamatan.pelayanan.layanan.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function layananDestroy($id)
    {
        $layanan = MasterLayanan::findOrFail($id);
        $layanan->delete();
        return redirect()->route('kecamatan.pelayanan.layanan.index')->with('success', 'Layanan berhasil dihapus.');
    }

    public function pengaduanIndex()
    {
        // Filter all complaints (unlimited by source)
        $pengaduans = PublicService::with(['desa', 'handler'])
            ->where('category', PublicService::CATEGORY_PENGADUAN)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Statistik pengaduan (all sources)
        $stats = [
            'total' => PublicService::where('category', PublicService::CATEGORY_PENGADUAN)
                ->count(),
            'menunggu' => PublicService::where('category', PublicService::CATEGORY_PENGADUAN)
                ->where('status', PublicService::STATUS_MENUNGGU)
                ->count(),
            'diproses' => PublicService::where('category', PublicService::CATEGORY_PENGADUAN)
                ->where('status', PublicService::STATUS_DIPROSES)
                ->count(),
            'selesai' => PublicService::where('category', PublicService::CATEGORY_PENGADUAN)
                ->where('status', PublicService::STATUS_SELESAI)
                ->count(),
        ];

        return view('kecamatan.pelayanan.pengaduan.index', compact('pengaduans', 'stats'));
    }

    public function pengaduanShow($id)
    {
        $pengaduan = PublicService::with(['desa', 'handler', 'attachments'])
            ->where('category', PublicService::CATEGORY_PENGADUAN)
            ->findOrFail($id);

        $desas = \App\Models\Desa::orderBy('nama_desa')->get();

        return view('kecamatan.pelayanan.pengaduan.show', compact('pengaduan', 'desas'));
    }

    /**
     * Update Data Pelapor (Verifikasi Sender Info)
     */
    public function pengaduanUpdateSender(Request $request, $id)
    {
        $request->validate([
            'nama_pemohon' => 'nullable|string|max:255',
            'nik' => 'nullable|digits:16',
            'whatsapp' => 'required|string|max:20',
            'desa_id' => 'nullable|exists:desa,id',
        ]);

        $pengaduan = PublicService::findOrFail($id);
        $pengaduan->update([
            'nama_pemohon' => $request->nama_pemohon,
            'nik' => $request->nik,
            'whatsapp' => $request->whatsapp,
            'desa_id' => $request->desa_id,
        ]);

        return redirect()->back()->with('success', 'Informasi pengirim berhasil diperbarui.');
    }

    /**
     * Update Status Pengaduan WhatsApp dengan notifikasi
     */
    public function pengaduanUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'internal_notes' => 'nullable|string',
            'public_response' => 'nullable|string',
            'send_whatsapp' => 'nullable|boolean',
        ]);

        $pengaduan = PublicService::findOrFail($id);
        $oldStatus = $pengaduan->status;

        $updateData = [
            'status' => $request->status,
            'internal_notes' => $request->internal_notes,
            'public_response' => $request->public_response,
            'handled_by' => auth()->id(),
            'handled_at' => now(),
        ];

        if ($request->filled('public_response')) {
            $updateData['responded_at'] = now();
        }

        $pengaduan->update($updateData);

        // Kirim notifikasi WhatsApp jika diminta dan status berubah
        if ($request->boolean('send_whatsapp', true) && $pengaduan->whatsapp && $oldStatus !== $request->status) {
            $this->sendWaNotification($pengaduan, 'status_update');
        }

        return redirect()->back()->with('success', 'Status pengaduan berhasil diperbarui dan notifikasi WhatsApp terkirim.');
    }
}
