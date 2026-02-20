<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\PembangunanDesa;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PembangunanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $desa_id = $user->desa_id;

        $stats = [
            'fisik' => PembangunanDesa::where('desa_id', $desa_id)->where('jenis_kegiatan', 'Fisik')->count(),
            'non_fisik' => PembangunanDesa::where('desa_id', $desa_id)->where('jenis_kegiatan', '!=', 'Fisik')->count(),
        ];

        // Actually, let's just fetch all and group in view or filter by some field.
        // User's latest schema: id, desa_id, nama_kegiatan, lokasi, tahun_anggaran, bidang_apbdes, sumber_dana, status_kegiatan, progres_fisik, pagu_anggaran, realisasi_anggaran, rab_file, gambar_rencana_file, status_laporan, catatan_desa

        $pembangunan = PembangunanDesa::where('desa_id', $desa_id)
            ->latest()
            ->get();

        return view('desa.pembangunan.index', compact('pembangunan'));
    }

    public function fisikIndex()
    {
        $pembangunan = PembangunanDesa::where('desa_id', auth()->user()->desa_id)
            ->where('jenis_kegiatan', 'Fisik')
            ->latest()
            ->get();

        return view('desa.pembangunan.fisik.index', compact('pembangunan'));
    }

    public function fisikCreate()
    {
        return view('desa.pembangunan.fisik.create');
    }

    public function fisikStore(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tahun_anggaran' => 'required|integer',
            'bidang_apbdes' => 'required|string',
            'sub_bidang' => 'nullable|string|max:255',
            'sumber_dana' => 'required|string',
            'status_kegiatan' => 'required|string',
            'progres_fisik' => 'required|string',
            'pagu_anggaran' => 'required|numeric',
            'realisasi_anggaran' => 'required|numeric',
            'rab_file' => 'nullable|file|mimes:pdf|max:5120',
            'gambar_rencana_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'foto_sebelum_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'foto_progres_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'foto_selesai_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data = $validated;
        $data['desa_id'] = auth()->user()->desa_id;
        $data['status_laporan'] = 'Draft';

        if ($request->hasFile('rab_file')) {
            $data['rab_file'] = $request->file('rab_file')->store('pembangunan/rab', 'public');
        }

        if ($request->hasFile('gambar_rencana_file')) {
            $data['gambar_rencana_file'] = $request->file('gambar_rencana_file')->store('pembangunan/desain', 'public');
        }

        if ($request->hasFile('foto_sebelum_file')) {
            $data['foto_sebelum_file'] = $request->file('foto_sebelum_file')->store('pembangunan/foto', 'public');
        }

        if ($request->hasFile('foto_progres_file')) {
            $data['foto_progres_file'] = $request->file('foto_progres_file')->store('pembangunan/foto', 'public');
        }

        if ($request->hasFile('foto_selesai_file')) {
            $data['foto_selesai_file'] = $request->file('foto_selesai_file')->store('pembangunan/foto', 'public');
        }

        $data['foto_timestamp'] = now();

        $pembangunan = PembangunanDesa::create($data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => 'create',
            'table_name' => 'pembangunan_desa',
            'record_id' => $pembangunan->id,
            'new_values' => $pembangunan->toArray(),
            'domain' => 'desa'
        ]);

        return redirect()->route('desa.pembangunan.fisik.index')->with('success', 'Data pembangunan berhasil disimpan sebagai draft.');
    }

    public function nonFisikIndex()
    {
        $pembangunan = PembangunanDesa::where('desa_id', auth()->user()->desa_id)
            ->where('jenis_kegiatan', '!=', 'Fisik')
            ->latest()
            ->get();

        return view('desa.pembangunan.non-fisik.index', compact('pembangunan'));
    }

    public function nonFisikCreate()
    {
        return view('desa.pembangunan.non-fisik.create');
    }

    public function nonFisikStore(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'nullable|string',
            'komponen_belanja' => 'nullable|array',
            'lokasi' => 'required|string|max:255',
            'tahun_anggaran' => 'required|integer',
            'bidang_apbdes' => 'required|string',
            'sumber_dana' => 'required|string',
            'status_kegiatan' => 'required|string',
            'pagu_anggaran' => 'required|numeric',
            'realisasi_anggaran' => 'required|numeric',
            'tanggal_kegiatan' => 'nullable|date',
            'jumlah_peserta' => 'nullable|integer',
            'rab_file' => 'nullable|file|mimes:pdf|max:5120', // Used as attendance list
            'foto_progres_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120', // Used as activity photo
        ]);

        $data = $validated;
        $data['desa_id'] = auth()->user()->desa_id;
        $data['status_laporan'] = 'Draft';
        $data['progres_fisik'] = '100%';

        if ($request->hasFile('rab_file')) {
            $data['rab_file'] = $request->file('rab_file')->store('pembangunan/non-fisik/ba', 'public');
        }

        if ($request->hasFile('foto_progres_file')) {
            $data['foto_progres_file'] = $request->file('foto_progres_file')->store('pembangunan/non-fisik/foto', 'public');
        }

        $pembangunan = PembangunanDesa::create($data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => 'create',
            'table_name' => 'pembangunan_desa',
            'record_id' => $pembangunan->id,
            'new_values' => $pembangunan->toArray(),
            'domain' => 'desa'
        ]);

        return redirect()->route('desa.pembangunan.non-fisik.index')->with('success', 'Kegiatan non-fisik berhasil disimpan.');
    }

    public function administrasiIndex($id = null)
    {
        $user = auth()->user();
        $desa_id = $user->desa_id;

        $activity = null;
        $checklist = [];

        if ($id) {
            // Check both tables, or we can use a prefix/logic if they share IDs.
            // Better to check pembangunan_desa first.
            $activity = PembangunanDesa::where('desa_id', $desa_id)->find($id);
            if (!$activity) {
                // Check blt_desa - this is a bit tricky if IDs overlap, 
                // but for this demo/SAE we'll assume they are distinct or use a better strategy.
                // Actually, let's just use pembangunan_desa for now as per the user's focus on building the engine.
                $activity = \App\Models\BltDesa::where('desa_id', $desa_id)->find($id);
                if ($activity) {
                    $activity->jenis_kegiatan = 'Penyaluran BLT'; // Dynamic property
                }
            }

            if ($activity) {
                $checklist = \App\Helpers\SaeHelper::getChecklist($activity->jenis_kegiatan, $activity->komponen_belanja);
            }
        }

        $pembangunan = PembangunanDesa::where('desa_id', $desa_id)
            ->where('status_laporan', 'Draft')
            ->select('id', 'nama_kegiatan', 'jenis_kegiatan', 'pagu_anggaran', 'tahun_anggaran')
            ->latest()
            ->get();

        $blt = \App\Models\BltDesa::where('desa_id', $desa_id)
            ->where('status_laporan', 'Draft')
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->nama_kegiatan = "Penyaluran BLT Tahun " . $item->tahun_anggaran;
                $item->jenis_kegiatan = "Penyaluran BLT";
                $item->pagu_anggaran = $item->total_dana_tersalurkan; // or relevant field
                return $item;
            });

        $activities = $pembangunan->concat($blt);

        return view('desa.pembangunan.administrasi.index', compact('activity', 'checklist', 'activities'));
    }

    public function edit($id)
    {
        $item = PembangunanDesa::where('desa_id', auth()->user()->desa_id)->findOrFail($id);

        if ($item->status_laporan !== 'Draft' && $item->status_laporan !== 'Dikembalikan') {
            return back()->with('error', 'Laporan yang sudah dikirim tidak dapat diedit.');
        }

        return view('desa.pembangunan.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = PembangunanDesa::where('desa_id', auth()->user()->desa_id)->findOrFail($id);

        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tahun_anggaran' => 'required|integer',
            'bidang_apbdes' => 'required|string',
            'sub_bidang' => 'nullable|string|max:255',
            'sumber_dana' => 'required|string',
            'status_kegiatan' => 'required|string',
            'progres_fisik' => 'required|string',
            'pagu_anggaran' => 'required|numeric',
            'realisasi_anggaran' => 'required|numeric',
            'rab_file' => 'nullable|file|mimes:pdf|max:5120',
            'gambar_rencana_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'foto_sebelum_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'foto_progres_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'foto_selesai_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $oldValues = $item->toArray();
        $data = $validated;

        if ($request->hasFile('rab_file')) {
            if ($item->rab_file)
                Storage::disk('public')->delete($item->rab_file);
            $data['rab_file'] = $request->file('rab_file')->store('pembangunan/rab', 'public');
        }

        if ($request->hasFile('gambar_rencana_file')) {
            if ($item->gambar_rencana_file)
                Storage::disk('public')->delete($item->gambar_rencana_file);
            $data['gambar_rencana_file'] = $request->file('gambar_rencana_file')->store('pembangunan/desain', 'public');
        }

        if ($request->hasFile('foto_sebelum_file')) {
            if ($item->foto_sebelum_file)
                Storage::disk('public')->delete($item->foto_sebelum_file);
            $data['foto_sebelum_file'] = $request->file('foto_sebelum_file')->store('pembangunan/foto', 'public');
        }

        if ($request->hasFile('foto_progres_file')) {
            if ($item->foto_progres_file)
                Storage::disk('public')->delete($item->foto_progres_file);
            $data['foto_progres_file'] = $request->file('foto_progres_file')->store('pembangunan/foto', 'public');
        }

        if ($request->hasFile('foto_selesai_file')) {
            if ($item->foto_selesai_file)
                Storage::disk('public')->delete($item->foto_selesai_file);
            $data['foto_selesai_file'] = $request->file('foto_selesai_file')->store('pembangunan/foto', 'public');
        }

        $data['foto_timestamp'] = now();

        $item->update($data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => 'update',
            'table_name' => 'pembangunan_desa',
            'record_id' => $item->id,
            'old_values' => $oldValues,
            'new_values' => $item->fresh()->toArray(),
            'domain' => 'desa'
        ]);

        return back()->with('success', 'Data berhasil diperbarui.');
    }

    public function show($id)
    {
        $item = PembangunanDesa::where('desa_id', auth()->user()->desa_id)
            ->with([
                'logbooks' => function ($query) {
                    $query->latest();
                }
            ])
            ->findOrFail($id);

        return view('desa.pembangunan.show', compact('item'));
    }

    public function modernIndex()
    {
        $user = auth()->user();
        $desa_id = $user->desa_id;
        abort_unless($desa_id !== null, 403);

        $pembangunans = PembangunanDesa::where('desa_id', $desa_id)
            ->with(['masterKegiatan.subBidang.bidang', 'dokumenSpjs'])
            ->latest()
            ->get();

        return view('desa.pembangunan.modern_index', compact('pembangunans', 'desa_id'));
    }

    public function modernCreate()
    {
        $user = auth()->user();
        $desa_id = $user->desa_id;
        abort_unless($desa_id !== null, 403);

        return view('desa.pembangunan.modern_create', compact('desa_id'));
    }

    public function predictDocs(Request $request, \App\Services\SpjRuleEngine $engine)
    {
        $kegiatanData = $request->only(['nama_kegiatan', 'jenis_kegiatan', 'pagu_anggaran']);
        $components = $request->input('components', []);

        $docs = $engine->getRecommendedDocuments($kegiatanData, $components);

        return response()->json([
            'status' => 'success',
            'documents' => $docs,
            'message' => 'Sistem menemukan ' . count($docs) . ' dokumen yang disarankan.'
        ]);
    }

    public function estimateTax(Request $request, \App\Services\TaxAssistant $assistant)
    {
        $komponenId = $request->input('komponen_id');
        $nilai = $request->input('nilai', 0);

        $komponen = \App\Models\MasterKomponenBelanja::find($komponenId);
        if (!$komponen)
            return response()->json(['status' => 'error', 'message' => 'Komponen tidak ditemukan'], 404);

        $estimation = $assistant->getTaxEstimation($komponen, $nilai);

        return response()->json([
            'status' => 'success',
            'estimation' => $estimation
        ]);
    }
}
