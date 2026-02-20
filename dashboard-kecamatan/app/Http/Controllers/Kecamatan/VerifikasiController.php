<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Submission;
use App\Services\MasterDataService;
use App\Services\SubmissionService;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    protected $submissionService;
    protected $masterData;

    public function __construct(SubmissionService $submissionService, MasterDataService $masterData)
    {
        $this->submissionService = $submissionService;
        $this->masterData = $masterData;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->isOperatorKecamatan()) {
            abort(403, 'Akses Terbatas: Anda tidak memiliki izin untuk modul Verifikasi.');
        }

        $query = Submission::with(['desa', 'menu', 'aspek', 'submittedBy']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            if ($user->isSuperAdmin()) {
                $query->whereIn('status', [Submission::STATUS_REVIEWED, Submission::STATUS_APPROVED]);
            } else {
                $query->whereIn('status', [Submission::STATUS_SUBMITTED, Submission::STATUS_REVIEWED]);
            }
        }

        if ($request->desa_id)
            $query->where('desa_id', $request->desa_id);
        if ($request->menu_id)
            $query->where('menu_id', $request->menu_id);
        if ($request->tahun)
            $query->where('tahun', $request->tahun);

        $submissions = $query->latest()->paginate(10);

        $desas = Desa::all();
        $menus = $this->masterData->getAllMenus();

        return view('kecamatan.verifikasi.index', compact('submissions', 'desas', 'menus'));
    }

    public function show($uuid)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->isOperatorKecamatan()) {
            abort(403, 'Akses Terbatas: Anda tidak memiliki izin untuk melihat detail Verifikasi.');
        }

        $submission = Submission::with([
            'desa',
            'menu',
            'aspek',
            'submittedBy',
            'jawabanIndikator.indikator',
            'buktiDukung',
            'verifikasi.verifikator'
        ])->where('uuid', $uuid)->firstOrFail();

        return view('kecamatan.verifikasi.show', compact('submission'));
    }

    public function process(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
            'catatan' => 'nullable|string'
        ]);

        try {
            $this->submissionService->processApproval(
                $id,
                $request->status,
                $request->catatan,
                auth()->id()
            );

            return redirect()->route('kecamatan.verifikasi.index')->with('success', 'Laporan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }
}
