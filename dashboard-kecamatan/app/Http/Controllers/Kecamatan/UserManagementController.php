<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'desa']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        $users = $query->latest()->paginate(10);
        $roles = Role::all();

        return view('kecamatan.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $villages = Desa::active()->get();
        return view('kecamatan.users.create', compact('roles', 'villages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'username' => 'required|string|unique:users,username|max:50',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'desa_id' => 'nullable|exists:desa,id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $role = Role::find($request->role_id);

        // Validation Logic: Operator Desa MUST have desa_id
        if ($role->nama_role === 'Operator Desa' && empty($request->desa_id)) {
            return back()->withErrors(['desa_id' => 'Operator Desa wajib memilih Desa.'])->withInput();
        }

        // Validation Logic: Kecamatan/Admin MUST NOT have desa_id
        if ($role->nama_role !== 'Operator Desa') {
            $validated['desa_id'] = null;
        }

        $validated['password'] = Hash::make($request->password);
        User::create($validated);

        return redirect()->route('kecamatan.users.index')
            ->with('success', 'User berhasil ditambahkan ke sistem.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $villages = Desa::active()->get();
        return view('kecamatan.users.edit', compact('user', 'roles', 'villages'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'role_id' => 'required|exists:roles,id',
            'desa_id' => 'nullable|exists:desa,id',
            'status' => 'required|in:aktif,nonaktif',
            'password' => 'nullable|string|min:6',
        ]);

        $role = Role::find($request->role_id);

        // Refine rules
        if ($role->nama_role === 'Operator Desa' && empty($request->desa_id)) {
            return back()->withErrors(['desa_id' => 'Operator Desa wajib memilih Desa.'])->withInput();
        }

        if ($role->nama_role !== 'Operator Desa') {
            $validated['desa_id'] = null;
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        // Username is immutable (not included in validated/update)
        $user->update($validated);

        return redirect()->route('kecamatan.users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Absolute rule: Logical deactivation
        $user->update(['status' => 'nonaktif']);

        return back()->with('success', 'Akses user telah dinonaktifkan.');
    }
}
