<?php

namespace App\Repositories;

use App\Models\Submission;
use App\Models\AuditLog;
use App\Repositories\Interfaces\SubmissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubmissionRepository implements SubmissionRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Submission::with(['desa', 'menu', 'aspek', 'submittedBy']);

        if (!empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['menu_id'])) {
            $query->where('menu_id', $filters['menu_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findById(int $id): ?Submission
    {
        return Submission::with(['desa', 'menu', 'aspek', 'jawabanIndikator', 'buktiDukung', 'verifikasi'])->find($id);
    }

    public function findByUuid(string $uuid): ?Submission
    {
        return Submission::with(['desa', 'menu', 'aspek', 'jawabanIndikator', 'buktiDukung', 'verifikasi'])->where('uuid', $uuid)->first();
    }

    public function create(array $data): Submission
    {
        return DB::transaction(function () use ($data) {
            $data['uuid'] = Str::uuid();
            $submission = Submission::create($data);

            // Log Audit
            AuditLog::create([
                'user_id' => $data['submitted_by'],
                'domain' => auth()->user()->desa_id !== null ? 'desa' : 'kecamatan',
                'action' => 'create',
                'table_name' => 'submission',
                'record_id' => $submission->id,
                'new_values' => $submission->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $submission;
        });
    }

    public function update(int $id, array $data): Submission
    {
        $submission = $this->findById($id);
        $oldValues = $submission->toArray();

        DB::transaction(function () use ($submission, $data, $oldValues) {
            $submission->update($data);

            AuditLog::create([
                'user_id' => auth()->id() ?? $submission->submitted_by, // Fallback if CLI/Seeder
                'domain' => auth()->user()?->desa_id !== null ? 'desa' : 'kecamatan',
                'action' => 'update',
                'table_name' => 'submission',
                'record_id' => $submission->id,
                'old_values' => $oldValues,
                'new_values' => $submission->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        return $submission;
    }

    public function delete(int $id): bool
    {
        $submission = $this->findById($id);
        return $submission->delete();
    }

    public function getByDesa(int $desaId, array $filters = []): LengthAwarePaginator
    {
        $query = Submission::where('desa_id', $desaId)->with(['menu', 'aspek']);

        if (!empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        return $query->latest()->paginate(10);
    }

    public function changeStatus(int $id, string $status, ?string $note = null, ?int $userId = null): Submission
    {
        $submission = $this->findById($id);
        $oldStatus = $submission->status;

        $updateData = ['status' => $status];

        // Specific field updates based on transition
        if ($status === Submission::STATUS_SUBMITTED) {
            $updateData['submitted_at'] = now();
        } elseif ($status === Submission::STATUS_REVIEWED || $status === Submission::STATUS_RETURNED) {
            $updateData['reviewed_by'] = $userId;
            $updateData['reviewed_at'] = now();
            $updateData['catatan_review'] = $note;
        } elseif ($status === Submission::STATUS_APPROVED || $status === Submission::STATUS_REJECTED) {
            $updateData['approved_by'] = $userId;
            $updateData['approved_at'] = now();
            $updateData['catatan_approval'] = $note;
        }

        DB::transaction(function () use ($submission, $updateData, $status, $oldStatus, $userId, $note) {
            $submission->update($updateData);

            // Audit-safe log into verifikasi table (Append-only)
            \App\Models\Verifikasi::create([
                'submission_id' => $submission->id,
                'verifikator_id' => $userId ?? auth()->id(),
                'from_status' => $oldStatus,
                'to_status' => $status,
                'catatan' => $note,
                'role' => auth()->user() ? auth()->user()->roles->first()?->name : null, // Record actor role
                'tipe_verifikasi' => $this->mapStatusToType($status),
            ]);
        });

        return $submission->fresh();
    }

    protected function mapStatusToType(string $status): string
    {
        return match ($status) {
            Submission::STATUS_REVIEWED => 'kasi',
            Submission::STATUS_APPROVED, Submission::STATUS_REJECTED => 'camat',
            default => 'kasi' // Fallback
        };
    }

    public function approveBackdate(int $id, int $userId): Submission
    {
        $submission = $this->findById($id);

        $updateData = [
            'approved_backdate_by' => $userId,
            'approved_backdate_at' => now(),
            // Additional logic: maybe automatically submit if backdate approved?
            // For now just mark approval.
        ];

        return $this->update($id, $updateData);
    }
}
