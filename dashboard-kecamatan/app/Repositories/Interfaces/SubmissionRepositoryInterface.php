<?php

namespace App\Repositories\Interfaces;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SubmissionRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 10): LengthAwarePaginator;
    public function findById(int $id): ?Submission;
    public function findByUuid(string $uuid): ?Submission;
    public function create(array $data): Submission;
    public function update(int $id, array $data): Submission;
    public function delete(int $id): bool;

    // Domain specific
    public function getByDesa(int $desaId, array $filters = []): LengthAwarePaginator;
    public function changeStatus(int $id, string $status, ?string $note = null, ?int $userId = null): Submission;
    public function approveBackdate(int $id, int $userId): Submission;
}
