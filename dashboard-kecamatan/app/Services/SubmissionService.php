<?php

namespace App\Services;

use App\Repositories\Interfaces\SubmissionRepositoryInterface;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Exception;

class SubmissionService
{
    protected $submissionRepo;

    public function __construct(SubmissionRepositoryInterface $submissionRepo)
    {
        $this->submissionRepo = $submissionRepo;
    }

    public function createSubmission($data, $userId)
    {
        // Business Logic: Check if submission already exists
        // (Handled by Unique Constraints in DB, but good to check here for nice error)

        $data['submitted_by'] = $userId;

        // Handle Backdate Logic
        if (!empty($data['is_backdate']) && $data['is_backdate'] == true) {
            // Check if user is allowed to backdate or if logic requires approval
            // For now, allow creation but status might need to be specific or flagged
            if (empty($data['alasan_backdate'])) {
                throw new Exception("Alasan backdate wajib diisi.");
            }
        }

        return $this->submissionRepo->create($data);
    }

    public function processApproval($id, $status, $note, $userId, $role = null)
    {
        $submission = $this->submissionRepo->findById($id);
        if (!$submission)
            throw new Exception("Submission tidak ditemukan.");

        $user = auth()->user();

        // 1. Kasi Role Logic (Verify/Review)
        if ($user->can('submission.verify')) {
            // Can move from 'submitted' to 'reviewed' or 'returned'
            if ($submission->status !== Submission::STATUS_SUBMITTED) {
                throw new Exception("Hanya submission dengan status 'submitted' yang dapat diverifikasi.");
            }

            if (!in_array($status, [Submission::STATUS_REVIEWED, Submission::STATUS_RETURNED])) {
                throw new Exception("Status tujuan tidak valid untuk verifikasi Kasi.");
            }

            if ($status === Submission::STATUS_RETURNED && empty($note)) {
                throw new Exception("Catatan wajib diisi saat dikembalikan untuk perbaikan.");
            }

            return $this->submissionRepo->changeStatus($id, $status, $note, $userId);
        }

        // 2. Camat Role Logic (Approve/Reject)
        if ($user->can('submission.approve')) {
            // Can move from 'reviewed' to 'approved' or 'rejected'
            if ($submission->status !== Submission::STATUS_REVIEWED) {
                throw new Exception("Hanya submission dengan status 'reviewed' yang dapat diproses Camat.");
            }

            if (!in_array($status, [Submission::STATUS_APPROVED, Submission::STATUS_REJECTED])) {
                throw new Exception("Status tujuan tidak valid untuk approval Camat.");
            }

            if ($status === Submission::STATUS_REJECTED && empty($note)) {
                throw new Exception("Catatan wajib diisi saat menolak laporan.");
            }

            return $this->submissionRepo->changeStatus($id, $status, $note, $userId);
        }

        throw new Exception("Anda tidak memiliki izin untuk memproses submission ini.");
    }
}
