<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TrantibumKejadian;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrantibumPolicy
{
    use HandlesAuthorization;

    /**
     * Allowed roles for Trantibum module access
     */
    protected array $allowedRoles = ['trantibum_admin', 'Super Admin', 'Operator Kecamatan'];

    /**
     * Determine if user can view any Trantibum content
     */
    public function viewAny(User $user): bool
    {
        return $this->hasAllowedRole($user);
    }

    /**
     * Determine if user can view specific Trantibum content
     */
    public function view(User $user, TrantibumKejadian $kejadian): bool
    {
        return $this->hasAllowedRole($user);
    }

    /**
     * Determine if user can create Trantibum content
     */
    public function create(User $user): bool
    {
        return $this->hasAllowedRole($user);
    }

    /**
     * Determine if user can update Trantibum content
     */
    public function update(User $user, TrantibumKejadian $kejadian): bool
    {
        return $this->hasAllowedRole($user);
    }

    /**
     * Determine if user can delete Trantibum content
     */
    public function delete(User $user, TrantibumKejadian $kejadian): bool
    {
        // Only Super Admin and trantibum_admin can delete
        return in_array($user->role->nama_role ?? null, ['trantibum_admin', 'Super Admin']);
    }

    /**
     * Determine if user can restore Trantibum content
     */
    public function restore(User $user, TrantibumKejadian $kejadian): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if user can permanently delete Trantibum content
     */
    public function forceDelete(User $user, TrantibumKejadian $kejadian): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if user can export Trantibum data
     */
    public function export(User $user): bool
    {
        return $this->hasAllowedRole($user);
    }

    /**
     * Determine if user can manage Trantibum settings
     */
    public function manageSettings(User $user): bool
    {
        return in_array($user->role->nama_role ?? null, ['trantibum_admin', 'Super Admin']);
    }

    /**
     * Check if user has allowed role
     */
    protected function hasAllowedRole(User $user): bool
    {
        $userRole = $user->role->nama_role ?? null;
        return in_array($userRole, $this->allowedRoles);
    }
}
