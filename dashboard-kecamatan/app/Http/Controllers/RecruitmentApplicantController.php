<?php

namespace App\Http\Controllers;

use App\Services\RecruitmentService;
use App\Repositories\Interfaces\RecruitmentRepositoryInterface;
use Illuminate\Http\Request;
use Exception;

class RecruitmentApplicantController extends Controller
{
    protected $recruitmentService;
    protected $recruitmentRepo;

    public function __construct(RecruitmentService $recruitmentService, RecruitmentRepositoryInterface $recruitmentRepo)
    {
        $this->recruitmentService = $recruitmentService;
        $this->recruitmentRepo    = $recruitmentRepo;
    }

    /** Daftar pendaftar untuk vacancy tertentu (Desa/Panitia) */
    public function index(int $vacancyId)
    {
        $vacancy    = $this->recruitmentRepo->findVacancyById($vacancyId);
        $applicants = $vacancy->applicants()->with('score')->orderByDesc('score_total')->get();
        return view('recruitment.desa.applicant.index', compact('vacancy', 'applicants'));
    }

    /** Verifikasi berkas pendaftar */
    public function verify(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected,exam_qualified',
            'notes'  => 'nullable|string',
        ]);

        try {
            $this->recruitmentService->verifyApplicant($id, $request->status, auth()->id());
            return back()->with('success', 'Status pendaftar berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /** Input nilai ujian tertulis & wawancara */
    public function inputScore(Request $request, int $id)
    {
        $applicant = \App\Models\RecruitmentApplicant::with('score')->findOrFail($id);
        $isBuktiRequired = $applicant->score?->bukti_ujian_path ? 'nullable' : 'required';

        $request->validate([
            'nilai_tertulis'   => 'required|numeric|min:0|max:100',
            'nilai_wawancara'  => 'required|numeric|min:0|max:100',
            'catatan'          => 'nullable|string',
            'bukti_ujian_file' => "$isBuktiRequired|file|mimes:pdf|max:5120",
        ]);

        try {
            $buktiPath = $applicant->score?->bukti_ujian_path;
            
            if ($request->hasFile('bukti_ujian_file')) {
                $buktiPath = $request->file('bukti_ujian_file')->store('recruitment/bukti_ujian', 'public');
            }

            $this->recruitmentService->inputScore(
                $id,
                (float) $request->nilai_tertulis,
                (float) $request->nilai_wawancara,
                auth()->id(),
                $request->catatan,
                $buktiPath
            );
            return back()->with('success', 'Nilai dan bukti ujian berhasil disimpan.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /** Set pendaftar sebagai 'selected' */
    public function select(Request $request, int $vacancyId, int $applicantId)
    {
        try {
            $this->recruitmentRepo->updateApplicantStatus($applicantId, 'selected', auth()->id());
            // Set semua lainnya jadi not_selected
            \App\Models\RecruitmentApplicant::where('recruitment_vacancy_id', $vacancyId)
                ->where('id', '!=', $applicantId)
                ->where('status', 'verified')
                ->update(['status' => 'not_selected']);
            return back()->with('success', 'Pendaftar berhasil ditetapkan sebagai terpilih.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
