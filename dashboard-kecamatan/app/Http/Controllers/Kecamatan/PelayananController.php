<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\PublicService;
use App\Models\PelayananFaq;
use App\Models\PengunjungKecamatan;
use App\Models\MasterLayanan;
use App\Models\Desa;
use App\Models\Umkm;
use App\Models\Loker;
use App\Models\WorkDirectory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelayananController extends Controller
{
    /**
     * Inbox Pengaduan & Pelayanan
     */
    public function inbox(Request $request)
    {
        $category = $request->query('category', 'pelayanan');

        $query = PublicService::with('desa');

        // Mapping logic for strict separation
        if ($category === 'pelayanan') {
            $query->where('category', PublicService::CATEGORY_PELAYANAN);
        } else {
            $query->where('category', $category);
        }

        $complaints = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('kecamatan.pelayanan.inbox', compact('complaints', 'category'));
    }

    /**
     * Detail Pengaduan
     */
    public function show($id)
    {
        $complaint = PublicService::with(['desa', 'handler'])->findOrFail($id);

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
            'completion_type' => 'nullable|in:digital,physical',
            'result_file' => 'nullable|file|mimes:pdf|max:5120',
            'ready_at' => 'nullable|date',
            'pickup_person' => 'nullable|string|max:255',
            'pickup_notes' => 'nullable|string',
            'send_whatsapp_notification' => 'nullable|boolean',
        ]);

        $complaint = PublicService::findOrFail($id);
        $oldStatus = $complaint->status;

        $updateData = [
            'status' => $request->status,
            'internal_notes' => $request->internal_notes,
            'public_response' => $request->public_response,
            'handled_by' => auth()->id(),
            'handled_at' => now(),
            'completion_type' => $request->completion_type,
            'ready_at' => $request->ready_at,
            'pickup_person' => $request->pickup_person,
            'pickup_notes' => $request->pickup_notes,
        ];

        // Handle PDF upload for digital completion
        if ($request->hasFile('result_file')) {
            $path = $request->file('result_file')->store('public_services/results', 'public');
            $updateData['result_file_path'] = $path;
        }

        if ($request->filled('public_response')) {
            $updateData['responded_at'] = now();
        }

        $complaint->update($updateData);

        // Send WhatsApp notification if status changed and notification is enabled
        $shouldNotify = $request->boolean('send_whatsapp_notification', true);
        if ($shouldNotify && $complaint->whatsapp && $complaint->source === 'whatsapp' && $oldStatus !== $request->status) {
            $this->sendWhatsAppNotification($complaint, $request->status);
        }

        return redirect()->back()->with('success', 'Tindak lanjut pengaduan berhasil diperbarui.');
    }

    /**
     * Send WhatsApp notification for status update
     * 
     * @param \App\Models\PublicService $complaint
     * @param string $newStatus
     * @return void
     */
    private function sendWhatsAppNotification($complaint, $newStatus)
    {
        try {
            $message = $this->buildStatusMessage($complaint, $newStatus);

            \Illuminate\Support\Facades\Http::post(
                config('services.n8n.reply_webhook_url', env('N8N_REPLY_WEBHOOK_URL')),
                [
                    'phone' => $complaint->whatsapp,
                    'message' => $message,
                    'type' => 'status_update',
                    'service_id' => $complaint->id,
                    'uuid' => $complaint->uuid
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp notification', [
                'error' => $e->getMessage(),
                'service_id' => $complaint->id,
                'uuid' => $complaint->uuid
            ]);
        }
    }

    /**
     * Build status message for WhatsApp notification
     * 
     * @param \App\Models\PublicService $complaint
     * @param string $newStatus
     * @return string
     */
    private function buildStatusMessage($complaint, $newStatus)
    {
        $statusLabel = $complaint->status_label;
        $statusEmoji = match ($newStatus) {
            PublicService::STATUS_MENUNGGU => '⏳',
            PublicService::STATUS_DIPROSES => '🔄',
            PublicService::STATUS_SELESAI => '✅',
            PublicService::STATUS_DITOLAK => '❌',
            default => '📋'
        };

        $message = "{$statusEmoji} *Update Status Laporan*\n\n";
        $message .= "🆔 ID: `{$complaint->uuid}`\n";
        $message .= "📂 Layanan: {$complaint->jenis_layanan}\n";
        $message .= "📊 Status: *{$statusLabel}*\n";
        $message .= "📅 Update: " . now()->format('d M Y, H:i') . "\n\n";

        if ($complaint->public_response) {
            $message .= "📝 *Respon Petugas:*\n{$complaint->public_response}\n\n";
        }

        if ($newStatus === PublicService::STATUS_SELESAI) {
            if ($complaint->completion_type === 'digital' && $complaint->result_file_path) {
                $message .= "📎 *Dokumen Tersedia:*\n";
                $message .= asset('storage/' . $complaint->result_file_path) . "\n\n";
            } elseif ($complaint->completion_type === 'physical') {
                $message .= "📍 *Dokumen Siap Diambil:*\n";
                if ($complaint->ready_at) {
                    $message .= "⏰ Waktu: {$complaint->ready_at->format('d M Y, H:i')}\n";
                }
                if ($complaint->pickup_person) {
                    $message .= "👤 Pengambil: {$complaint->pickup_person}\n";
                }
                if ($complaint->pickup_notes) {
                    $message .= "📝 Catatan: {$complaint->pickup_notes}\n";
                }
                $message .= "\n";
            }
        }

        $message .= "💡 Ketik `/status {$complaint->uuid}` untuk cek status kapan saja.";

        return $message;
    }

    /**
     * FAQ Management
     */
    public function faqIndex()
    {
        $faqs = PelayananFaq::orderBy('category')->get();
        return view('kecamatan.pelayanan.faq.index', compact('faqs'));
    }

    public function faqStore(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string',
            'keywords' => 'required|string',
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        $data['module'] = 'pelayanan';
        PelayananFaq::create($data);

        return redirect()->back()->with('success', 'FAQ Administrasi berhasil ditambahkan.');
    }

    public function faqUpdate(Request $request, $id)
    {
        $data = $request->validate([
            'category' => 'required|string',
            'keywords' => 'required|string',
            'question' => 'required|string',
            'answer' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $data['module'] = 'pelayanan';
        $faq = PelayananFaq::findOrFail($id);
        $faq->update($data);

        return redirect()->back()->with('success', 'FAQ Administrasi berhasil diperbarui.');
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
            'loker_total' => Loker::count(),
            'loker_active' => Loker::where('status', Loker::STATUS_AKTIF)->count(),

            // Skilled workers directory
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

        return view('kecamatan.pelayanan.pengaduan.show', compact('pengaduan'));
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
            $this->sendWhatsAppNotification($pengaduan, $request->status);
        }

        return redirect()->back()->with('success', 'Status pengaduan berhasil diperbarui dan notifikasi WhatsApp terkirim.');
    }
}
