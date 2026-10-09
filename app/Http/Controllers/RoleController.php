<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:role.view')->only('index');
        $this->middleware('permission:role.create')->only(['create', 'store']);
        $this->middleware('permission:role.edit')->only(['edit', 'update']);
        $this->middleware('permission:role.delete')->only('destroy');
    }

    public function index()
    {
        // Untuk dashboard klinik: HANYA tampilkan role admin dan operator
        // Role saas_admin adalah hak akses tertinggi dan jangan pernah ditampilkan di dashboard klinik
        $roles = Role::with('permissions')
            ->where('guard_name', 'web')
            ->whereIn('name', ['admin', 'operator'])
            ->get();

        return view('admin.pages.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', 'web')->get();
        return view('admin.pages.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
        ]);

        if (in_array(strtolower($request->name), ['saas_admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Nama role tersebut dilindungi dan tidak dapat dibuat.');
        }

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', 'Role berhasil dibuat');
    }

    public function edit(Role $role)
    {
        // Lindungi role saas_admin agar tidak bisa diubah dari dashboard klinik
        if ($role->name === 'saas_admin') {
            abort(403, 'Role Administrator SaaS dilindungi dan tidak dapat diubah dari dashboard klinik.');
        }

        $permissions = Permission::where('guard_name', 'web')->get();
        return view('admin.pages.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'saas_admin') {
            abort(403, 'Role Administrator SaaS dilindungi dan tidak dapat diubah dari dashboard klinik.');
        }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui');
    }

    public function destroy(Role $role)
    {
        // Role saas_admin, admin, dan operator adalah role inti sistem dan tidak boleh dihapus
        if (in_array($role->name, ['saas_admin', 'admin', 'operator'])) {
            return redirect()->back()->with('error', "Role {$role->name} adalah role utama sistem dan tidak dapat dihapus.");
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus');
    }
}
