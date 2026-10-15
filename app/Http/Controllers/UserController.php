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
        $isSaasAdmin = auth()->user()->isSaasAdmin();

        if ($isSaasAdmin && !session()->has('active_clinic_id')) {
            // SaaS Admin Panel: tampilkan semua user dikelompokkan platform vs klinik
            $platformUsers = User::with('roles')
                ->whereNull('clinic_id')
                ->orderBy('name')
                ->get();

            $clinicUsers = User::with(['roles', 'clinic'])
                ->whereNotNull('clinic_id')
                ->orderBy('clinic_id')
                ->orderBy('name')
                ->get()
                ->groupBy(fn($u) => $u->clinic?->name ?? 'Klinik #' . $u->clinic_id);

            return view('admin.pages.users.index', compact('platformUsers', 'clinicUsers', 'isSaasAdmin'));
        }

        // Dashboard klinik: tampilkan hanya user klinik yang sedang aktif
        $clinicId = session('active_clinic_id') ?: auth()->user()->clinic_id;
        $users = User::with('roles')
            ->where('clinic_id', $clinicId)
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'saas_admin'))
            ->orderBy('name')
            ->get();

        $platformUsers = collect();
        $clinicUsers   = $users->groupBy(fn($u) => 'Klinik Saya');

        return view('admin.pages.users.index', compact('platformUsers', 'clinicUsers', 'isSaasAdmin'));
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6',
            'roles'    => 'nullable|array',
            'roles.*'  => 'in:admin,operator',
        ]);

        $clinicId = auth()->user()->clinic_id ?: session('active_clinic_id');

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
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
        $authUser       = auth()->user();
        $isPlatformUser = is_null($user->clinic_id);

        // Hanya SaaS Admin yang boleh edit user platform
        if ($isPlatformUser && !$authUser->isSaasAdmin()) {
            abort(403, 'Akses ditolak. Hanya Administrator Platform yang dapat mengelola user platform.');
        }

        // Admin klinik hanya dapat mengedit user di kliniknya sendiri
        if (!$authUser->isSaasAdmin() && !$isPlatformUser) {
            if ((int) $user->clinic_id !== (int) $authUser->clinic_id) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola pengguna klinik ini.');
            }
        }

        if ($isPlatformUser) {
            // Role untuk user platform: saas_admin atau operator
            $availableRoles = Role::whereIn('name', ['saas_admin', 'operator'])->get();
            $userRoles      = $user->roles->pluck('name')->intersect(['saas_admin', 'operator'])->values()->toArray();
        } else {
            // Role untuk user klinik: admin atau operator
            $availableRoles = Role::whereIn('name', ['admin', 'operator'])->get();
            $userRoles      = $user->roles->pluck('name')->intersect(['admin', 'operator'])->values()->toArray();
            if (empty($userRoles)) {
                $userRoles = ['operator'];
            }
        }

        return view('admin.pages.users.edit', compact('user', 'availableRoles', 'userRoles', 'isPlatformUser'));
    }

    public function update(Request $request, User $user)
    {
        $authUser       = auth()->user();
        $isPlatformUser = is_null($user->clinic_id);

        // Hanya SaaS Admin yang boleh update user platform
        if ($isPlatformUser && !$authUser->isSaasAdmin()) {
            abort(403, 'Akses ditolak. Hanya Administrator Platform yang dapat mengelola user platform.');
        }

        if (!$authUser->isSaasAdmin() && !$isPlatformUser) {
            if ((int) $user->clinic_id !== (int) $authUser->clinic_id) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola pengguna klinik ini.');
            }
        }

        // Tentukan allowed roles berdasarkan tipe user
        $allowedRoleValues = $isPlatformUser ? ['saas_admin', 'operator'] : ['admin', 'operator'];

        // Fallback roles jika kosong atau tidak dicentang
        $roles = $request->roles;
        if (empty($roles)) {
            $existingRoles = $user->roles->pluck('name')->intersect($allowedRoleValues)->toArray();
            $roles         = !empty($existingRoles) ? $existingRoles : ['operator'];
        }
        if (is_string($roles)) {
            $roles = [$roles];
        }
        $request->merge(['roles' => $roles]);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6',
            'roles'    => 'nullable|array',
            'roles.*'  => Rule::in($allowedRoleValues),
        ]);

        $updateData = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        $allowedRoles = array_intersect($roles, $allowedRoleValues);
        if (empty($allowedRoles)) {
            $allowedRoles = ['operator'];
        }
        $user->syncRoles($allowedRoles);

        return redirect()->route('users.index')->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        // Larang menghapus Administrator SaaS
        if ($user->isSaasAdmin()) {
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
