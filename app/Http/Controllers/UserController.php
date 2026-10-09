<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:user.view')->only('index');
        $this->middleware('permission:user.create')->only(['create', 'store']);
        $this->middleware('permission:user.edit')->only(['edit', 'update']);
        $this->middleware('permission:user.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = User::with('roles')->orderBy('created_at', 'desc');

        // Jika diakses dari dashboard klinik (atau staf/admin klinik sedang login)
        // JANGAN tampilkan user SaaS Admin karena merupakan akses tertinggi platform
        if (auth()->user()->clinic_id || session()->has('active_clinic_id')) {
            $clinicId = session('active_clinic_id') ?: auth()->user()->clinic_id;
            $query->where('clinic_id', $clinicId)
                  ->whereDoesntHave('roles', function ($q) {
                      $q->where('name', 'saas_admin');
                  });
        } elseif (auth()->user()->isSaasAdmin()) {
            // Jika SaaS Admin sedang berada di luar mode impersonasi (di panel SaaS)
            if ($request->filled('clinic_id')) {
                $query->where('clinic_id', $request->clinic_id);
            }
        }

        $users = $query->paginate(15);

        return view('admin.pages.users.index', compact('users'));
    }

    public function create()
    {
        // Akses klinik hanya menyediakan 2 role: admin dan operator
        $roles = Role::whereIn('name', ['admin', 'operator'])->get();

        return view('admin.pages.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Default role ke operator jika tidak dicentang
        $roles = $request->roles ?: ['operator'];
        if (is_string($roles)) {
            $roles = [$roles];
        }
        $request->merge(['roles' => $roles]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6',
            'roles' => 'nullable|array',
            'roles.*' => 'in:admin,operator',
        ]);

        $clinicId = auth()->user()->clinic_id ?: session('active_clinic_id');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'clinic_id' => $clinicId,
        ]);

        // Hanya sinkronkan role admin atau operator
        $allowedRoles = array_intersect($roles, ['admin', 'operator']);
        if (empty($allowedRoles)) {
            $allowedRoles = ['operator'];
        }
        $user->syncRoles($allowedRoles);

        return redirect()->route('users.index')->with('success', 'User staf klinik berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        // Tolak akses jika mencoba mengedit akun Administrator SaaS
        if ($user->isSaasAdmin() || $user->clinic_id === null) {
            abort(403, 'Akses ditolak. Administrator SaaS memiliki hak akses tertinggi dan tidak dapat diubah dari dashboard klinik.');
        }

        // Pastikan admin klinik hanya dapat mengedit user di kliniknya sendiri
        if (!auth()->user()->isSaasAdmin()) {
            $authClinicId = auth()->user()->clinic_id;
            if ((int) $user->clinic_id !== (int) $authClinicId) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola pengguna klinik ini.');
            }
        }

        // Hanya sediakan role admin dan operator
        $roles = Role::whereIn('name', ['admin', 'operator'])->get();
        $userRoles = $user->roles->pluck('name')->intersect(['admin', 'operator'])->values()->toArray();
        if (empty($userRoles)) {
            $userRoles = ['operator'];
        }

        return view('admin.pages.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        // Tolak akses jika mencoba mengubah akun Administrator SaaS
        if ($user->isSaasAdmin() || $user->clinic_id === null) {
            abort(403, 'Akses ditolak. Administrator SaaS memiliki hak akses tertinggi dan tidak dapat diubah dari dashboard klinik.');
        }

        if (!auth()->user()->isSaasAdmin()) {
            $authClinicId = auth()->user()->clinic_id;
            if ((int) $user->clinic_id !== (int) $authClinicId) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola pengguna klinik ini.');
            }
        }

        // Fallback roles jika kosong atau tidak dicentang: pertahankan role yang ada atau default operator
        $roles = $request->roles;
        if (empty($roles)) {
            $existingRoles = $user->roles->pluck('name')->intersect(['admin', 'operator'])->toArray();
            $roles = !empty($existingRoles) ? $existingRoles : ['operator'];
        }
        if (is_string($roles)) {
            $roles = [$roles];
        }
        $request->merge(['roles' => $roles]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6',
            'roles' => 'nullable|array',
            'roles.*' => 'in:admin,operator',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Hanya ubah password jika user mengisinya
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        $allowedRoles = array_intersect($roles, ['admin', 'operator']);
        if (empty($allowedRoles)) {
            $allowedRoles = ['operator'];
        }
        $user->syncRoles($allowedRoles);

        return redirect()->route('users.index')->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        // Larang menghapus Administrator SaaS
        if ($user->isSaasAdmin() || $user->clinic_id === null) {
            abort(403, 'Akses ditolak. Administrator SaaS tidak dapat dihapus.');
        }

        // Larang menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        if (!auth()->user()->isSaasAdmin()) {
            $authClinicId = auth()->user()->clinic_id;
            if ((int) $user->clinic_id !== (int) $authClinicId) {
                abort(403, 'Akses ditolak.');
            }
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')->with('success', "User {$userName} berhasil dihapus.");
    }
}
