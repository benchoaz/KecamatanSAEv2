<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Repositories\Interfaces\SubmissionRepositoryInterface;
use App\Services\MasterDataService;
use Illuminate\Http\Request;

class EkbangController extends Controller
{
    protected $submissionRepo;
    protected $masterData;

    public function __construct(
        SubmissionRepositoryInterface $submissionRepo,
        MasterDataService $masterData
    ) {
        $this->submissionRepo = $submissionRepo;
        $this->masterData = $masterData;
    }

    public function index()
    {
        $user = auth()->user();
        abort_unless($user->desa_id !== null, 403);

        $desa_id = $user->desa_id;

        // Health Check Calculations
        $healthMetrics = $this->calculateHealth($desa_id);

        // Recent Submissions for Ekbang
        $recentSubmissions = Submission::where('desa_id', $desa_id)
            ->whereHas('menu', function ($q) {
                $q->where('kode_menu', 'ekbang');
            })
            ->with(['menu', 'aspek'])
            ->latest()
            ->take(5)
            ->get();

        return view('kecamatan.ekbang.index', compact('healthMetrics', 'desa_id', 'recentSubmissions'));
    }

    public function danaDesa()
    {
        $user = auth()->user();
        $desa_id = $user->desa_id;
        abort_unless($desa_id !== null, 403);

        $danaDesa = Submission::where('desa_id', $desa_id)
            ->whereHas('aspek', function ($q) {
                $q->where('kode_aspek', 'ekb_monev');
            })
            ->latest()
            ->get();

        $isOperator = true;

        return view('kecamatan.ekbang.dana-desa.index', compact('danaDesa', 'desa_id', 'isOperator'));
    }

    public function fisik()
    {
        $user = auth()->user();
        $desa_id = $user->desa_id;
        abort_unless($desa_id !== null, 403);

        $pembangunans = \App\Models\PembangunanDesa::where('desa_id', $desa_id)
            ->with(['masterKegiatan.subBidang.bidang', 'dokumenSpjs'])
            ->latest()
            ->get();

        return view('desa.pembangunan.modern_index', compact('pembangunans', 'desa_id', 'isOperator'));
    }

    public function realisasi()
    {
        $user = auth()->user();
        $desa_id = $user->desa_id;
        abort_unless($desa_id !== null, 403);

        $realisasi = Submission::where('desa_id', $desa_id)
            ->whereHas('aspek', function ($q) {
                $q->where('kode_aspek', 'ekb_realisasi');
            })
            ->latest()
            ->get();

        $isOperator = true;

        return view('kecamatan.ekbang.realisasi.index', compact('realisasi', 'desa_id', 'isOperator'));
    }

    public function kepatuhan()
    {
        $user = auth()->user();
        $desa_id = $user->desa_id;
        abort_unless($desa_id !== null, 403);

        $kepatuhan = Submission::where('desa_id', $desa_id)
            ->whereHas('aspek', function ($q) {
                $q->where('kode_aspek', 'ekb_kepatuhan');
            })
            ->latest()
            ->get();

        $isOperator = true;

        return view('kecamatan.ekbang.kepatuhan.index', compact('kepatuhan', 'desa_id', 'isOperator'));
    }

    public function audit()
    {
        $user = auth()->user();
        $desa_id = $user->desa_id;
        abort_unless($desa_id !== null, 403);

        $auditLogs = Submission::where('desa_id', $desa_id)
            ->whereHas('aspek', function ($q) {
                $q->where('kode_aspek', 'ekb_audit');
            })
            ->latest()
            ->get();

        $isOperator = true;

        return view('kecamatan.ekbang.audit.index', compact('auditLogs', 'desa_id', 'isOperator'));
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

    public function create()
    {
        $user = auth()->user();
        $desa_id = $user->desa_id;
        abort_unless($desa_id !== null, 403);

        return view('desa.pembangunan.modern_create', compact('desa_id'));
    }

    protected function calculateHealth($desa_id)
    {
        $hasRealisasi = Submission::where('desa_id', $desa_id)
            ->whereHas('aspek', function ($q) {
                $q->where('kode_aspek', 'ekb_realisasi');
            })
            ->where('tahun', date('Y'))
            ->exists();

        $hasMonev = Submission::where('desa_id', $desa_id)
            ->whereHas('aspek', function ($q) {
                $q->where('kode_aspek', 'ekb_monev');
            })
            ->where('tahun', date('Y'))
            ->exists();

        return [
            'realisasi' => $hasRealisasi,
            'monev' => $hasMonev,
            'status' => ($hasRealisasi && $hasMonev) ? 'Sehat' : 'Perlu Perhatian',
        ];
    }
}
