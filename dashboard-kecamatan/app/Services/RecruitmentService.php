<?php

namespace App\Services;

use App\Repositories\Interfaces\RecruitmentRepositoryInterface;
use App\Models\RecruitmentVacancy;
use App\Models\RecruitmentApplicant;
use App\Models\RecruitmentCommittee;
use App\Models\RecruitmentScore;
use App\Models\Desa;
use Exception;
use Illuminate\Support\Facades\Storage;

class RecruitmentService
{
    protected $recruitmentRepo;
    protected $notificationService;
    protected $reportService;

    public function __construct(
        RecruitmentRepositoryInterface $recruitmentRepo,
        \App\Services\Recruitment\RecruitmentNotificationService $notificationService,
        \App\Services\Recruitment\RecruitmentReportService $reportService
    ) {
        $this->recruitmentRepo = $recruitmentRepo;
        $this->notificationService = $notificationService;
        $this->reportService = $reportService;
    }

    // ─── Vacancy Management ────────────────────────────────────────────────────

    public function createVacancy(array $data, int $userId): RecruitmentVacancy
    {
        $data['created_by'] = $userId;
        $data['status'] = $data['status'] ?? 'draft';

        // Auto-resolve kabupaten_id via kecamatan chain
        if (!empty($data['kecamatan_id'])) {
            $kecamatan = \App\Models\Kecamatan::find($data['kecamatan_id']);
            $data['kabupaten_id'] = $kecamatan?->kabupaten_id ?? null;
        }

        // Set klasifikasi_desa from Desa record
        if (!isset($data['klasifikasi_desa']) && !empty($data['desa_id'])) {
            $desa = Desa::find($data['desa_id']);
            $data['klasifikasi_desa'] = strtolower($desa?->klasifikasi_desa ?? 'swadaya');
        }

        // Validate SOTK before creating
        $this->validateSotk(
            $data['klasifikasi_desa'] ?? ($data['klasifikasi_saat_ini'] ?? 'swadaya'),
            (int) ($data['jumlah_perangkat_eksisting'] ?? $data['jumlah_perangkat_saat_ini'] ?? 0),
            $data['jabatan']
        );

        $vacancy = $this->recruitmentRepo->createVacancy($data);

        if (in_array($vacancy->status, ['reported_to_camat'])) {
            try {
                $this->notificationService->notifyVacancyToCamat($vacancy);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Notifikasi Camat gagal: ' . $e->getMessage());
            }
        }

        return $vacancy;
    }

    public function updateStatus(int $id, string $status, ?string $notes = null, int $userId = 0, ?string $documentPath = null): bool
    {
        $vacancy = $this->recruitmentRepo->findVacancyById($id);
        if (!$vacancy) throw new Exception("Lowongan tidak ditemukan.");

        $this->validateStatusTransition($vacancy->status, $status);

        $result = $this->recruitmentRepo->changeVacancyStatus($id, $status, $notes, $userId, $documentPath);

        if ($result) {
            $vacancy->refresh();

            // Toggle akses pendaftaran
            if ($status === 'open_registration') {
                $vacancy->update(['is_access_opened' => true]);
            } elseif (in_array($status, ['admin_verification', 'rejected_by_bupati', 'draft', 'rejected'])) {
                $vacancy->update(['is_access_opened' => false]);
            }

            // Record approver info
            if (in_array($status, ['approved_by_camat', 'camat_review'])) {
                $vacancy->update([
                    'approved_by_kecamatan' => $userId,
                    'approved_at_kecamatan' => now(),
                ]);
            }

            if (in_array($status, ['approved_by_bupati', 'rejected_by_bupati'])) {
                $vacancy->update([
                    'approved_by_kabupaten' => $userId,
                    'approved_at_kabupaten' => now(),
                ]);
            }

            // Generate SK jika status sk_generated
            if ($status === 'sk_generated') {
                $selected = $vacancy->applicants()->where('status', 'selected')->first();
                if ($selected instanceof RecruitmentApplicant) {
                    try {
                        $this->reportService->generateSKPengangkatan($vacancy->id, $selected->id);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('Generate SK gagal: ' . $e->getMessage());
                    }
                }
            }

            // Auto generate BA Kekosongan
            if ($status === 'reported_to_camat') {
                try {
                    $this->reportService->generateBAKekosongan($vacancy->id);
                    $this->notificationService->notifyVacancyToCamat($vacancy);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Generate BA gagal: ' . $e->getMessage());
                }
            }

            if ($status === 'approved_by_bupati') {
                try {
                    $this->notificationService->notifyApprovalKabupaten($vacancy, true, $notes);
                } catch (\Exception $e) {}
            }

            if ($status === 'rejected_by_bupati') {
                try {
                    $this->notificationService->notifyApprovalKabupaten($vacancy, false, $notes);
                } catch (\Exception $e) {}
            }
        }

        return $result;
    }

    // ─── Committee Management ─────────────────────────────────────────────────

    public function addCommittee(int $vacancyId, array $data, int $userId): RecruitmentCommittee
    {
        $vacancy = $this->recruitmentRepo->findVacancyById($vacancyId);
        if (!$vacancy) throw new Exception("Lowongan tidak ditemukan.");

        if (!in_array($vacancy->status, ['approved_by_camat', 'committee_formed', 'approved_by_bupati'])) {
            throw new Exception("Panitia hanya dapat dibentuk setelah usulan disetujui.");
        }

        $data['recruitment_vacancy_id'] = $vacancyId;
        $committee = RecruitmentCommittee::create($data);

        // Auto-advance status ke committee_formed jika belum
        if ($vacancy->status === 'approved_by_camat') {
            $this->recruitmentRepo->changeVacancyStatus($vacancyId, 'committee_formed', 'Panitia seleksi dibentuk.', $userId);
        }

        return $committee;
    }

    // ─── Applicant Management ─────────────────────────────────────────────────

    public function registerApplicant(array $data): RecruitmentApplicant
    {
        $vacancy = RecruitmentVacancy::findOrFail($data['recruitment_vacancy_id']);

        if (!$vacancy->is_access_opened) {
            throw new Exception("Pendaftaran belum dibuka atau sudah ditutup.");
        }

        // Cek duplikat NIK
        $existing = RecruitmentApplicant::where('recruitment_vacancy_id', $vacancy->id)
            ->where('nik', $data['nik'])
            ->first();
        if ($existing) {
            throw new Exception("NIK {$data['nik']} sudah terdaftar pada seleksi ini.");
        }

        $applicant = $this->recruitmentRepo->registerApplicant($data);

        try {
            $this->notificationService->notifyApplicantRegistered($applicant);
        } catch (\Exception $e) {}

        return $applicant;
    }

    public function verifyApplicant(int $id, string $status, int $userId): bool
    {
        $result = $this->recruitmentRepo->updateApplicantStatus($id, $status, $userId);

        if ($result && $status === 'verified') {
            $applicant = $this->recruitmentRepo->findApplicantById($id);
            try {
                $this->notificationService->notifyApplicantVerified($applicant);
            } catch (\Exception $e) {}
        }

        return $result;
    }

    // ─── Scoring & Ranking ─────────────────────────────────────────────────────

    public function inputScore(int $applicantId, float $nilaiTertulis, float $nilaiWawancara, int $scorerId, ?string $catatan = null, ?string $buktiUjianPath = null): RecruitmentScore
    {
        // Validasi range 0-100
        if ($nilaiTertulis < 0 || $nilaiTertulis > 100) throw new Exception("Nilai tertulis harus antara 0-100.");
        if ($nilaiWawancara < 0 || $nilaiWawancara > 100) throw new Exception("Nilai wawancara harus antara 0-100.");

        $nilaiTotal = RecruitmentScore::hitungNilaiTotal($nilaiTertulis, $nilaiWawancara);

        $score = RecruitmentScore::updateOrCreate(
            ['applicant_id' => $applicantId],
            [
                'nilai_tertulis'   => $nilaiTertulis,
                'nilai_wawancara'  => $nilaiWawancara,
                'nilai_total'      => $nilaiTotal,
                'catatan_penilai'  => $catatan,
                'bukti_ujian_path' => $buktiUjianPath,
                'scored_by'        => $scorerId,
                'scored_at'        => now(),
            ]
        );

        // Update nilai di tabel applicants juga (denormalized for quick access)
        RecruitmentApplicant::where('id', $applicantId)->update([
            'score_written'   => $nilaiTertulis,
            'score_interview' => $nilaiWawancara,
            'score_total'     => $nilaiTotal,
        ]);

        return $score;
    }

    public function generateRanking(int $vacancyId, int $userId): void
    {
        $applicants = RecruitmentApplicant::where('recruitment_vacancy_id', $vacancyId)
            ->where('status', 'verified')
            ->orderByDesc('score_total')
            ->get();

        foreach ($applicants as $rank => $applicant) {
            RecruitmentScore::where('applicant_id', $applicant->id)
                ->update(['ranking' => $rank + 1]);
        }

        $this->recruitmentRepo->changeVacancyStatus($vacancyId, 'ranking', 'Pemeringkatan selesai.', $userId);
    }

    // ─── SOTK Validation ──────────────────────────────────────────────────────

    public function validateSotk(string $classification, int $currentCount, string $targetJabatan): bool
    {
        $limits = getSotkLimits($classification);

        // Jika menambah 1 perangkat melebihi batas max
        if ($currentCount >= $limits['total_maks']) {
            throw new Exception(
                "Jumlah perangkat desa sudah mencapai batas maksimal untuk Desa {$classification} "
                . "(Maksimal {$limits['total_maks']} perangkat sesuai Permendagri 84/2016)."
            );
        }

        return true;
    }

    // ─── Status Transition Validation ─────────────────────────────────────────

    protected function validateStatusTransition(string $current, string $target): void
    {
        if (auth()->check() && auth()->user()->hasRole(['Super Admin'])) {
            return; // Super Admin bypass
        }

        $workflow = [
            'draft'                => ['reported_to_camat'],
            'reported_to_camat'    => ['approved_by_camat', 'draft'],
            'approved_by_camat'    => ['submitted_to_bupati', 'committee_formed'],
            'submitted_to_bupati'  => ['approved_by_bupati', 'rejected_by_bupati'],
            'approved_by_bupati'   => ['committee_formed'],
            'committee_formed'     => ['open_registration'],
            'open_registration'    => ['extension_1', 'admin_verification'],
            'extension_1'          => ['extension_2', 'admin_verification'],
            'extension_2'          => ['admin_verification'],
            'admin_verification'   => ['exam_process'],
            'exam_process'         => ['ranking'],
            'ranking'              => ['submitted_to_camat'],
            'submitted_to_camat'   => ['camat_review'],
            'camat_review'         => ['submitted_to_bupati', 'ranking'],
            'sk_generated'         => ['completed'],
        ];

        if (isset($workflow[$current]) && !in_array($target, $workflow[$current])) {
            throw new Exception("Transisi status dari '{$current}' ke '{$target}' tidak valid.");
        }
    }
}
