<?php

namespace App\Policies;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BeritaPolicy
{
    /**
     * Determine whether the user can view any models.
     * (Internal List)
     */
    public function viewAny(User $user): bool
    {
        // Hanya Operator Kecamatan & Super Admin yang boleh lihat list internal/draft
        return $user->hasRole('Operator Kecamatan') || $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can view the model.
     * (Internal Detail)
     */
    public function view(User $user, Berita $berita): bool
    {
        // Operator Kecamatan bisa lihat semua status (draft/published)
        return $user->hasRole('Operator Kecamatan') || $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Operator Kecamatan') || $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Berita $berita): bool
    {
        return $user->hasRole('Operator Kecamatan') || $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Berita $berita): bool
    {
        return $user->hasRole('Operator Kecamatan') || $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Berita $berita): bool
    {
        return $user->hasRole('Super Admin'); // Hanya Super Admin yang boleh restore
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Berita $berita): bool
    {
        return $user->hasRole('Super Admin'); // Hanya Super Admin
    }
}
