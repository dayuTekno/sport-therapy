<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $artikels = [
            [
                'id' => 1,
                'admin_id' => 1,
                'slug' => 'peluncuran-aplikasi-perizinan',
                'title' => 'Peluncuran Aplikasi Perizinan Digital',
                'tags' => 'perizinan,aplikasi,digital',
                'description' => 'Pemerintah resmi meluncurkan aplikasi perizinan digital untuk mempercepat layanan kepada masyarakat.',
                'thumbnail' => '/storage/news/tes.jpg',
                'publish_st' => 'publish',
                'created_at' => '2026-02-01 10:00:00',
                'updated_at' => '2026-02-01 10:00:00',
            ],
            [
                'id' => 2,
                'admin_id' => 1,
                'slug' => 'jadwal-pemeliharaan-sistem',
                'title' => 'Jadwal Pemeliharaan Sistem',
                'tags' => 'maintenance,sistem',
                'description' => 'Akan dilakukan pemeliharaan sistem pada akhir pekan untuk meningkatkan performa dan keamanan.',
                'thumbnail' => '/storage/news/tes.jpg',
                'publish_st' => 'draft',
                'created_at' => '2026-02-03 14:30:00',
                'updated_at' => '2026-02-03 14:30:00',
            ],
            [
                'id' => 3,
                'admin_id' => 2,
                'slug' => 'update-regulasi-perizinan',
                'title' => 'Update Regulasi Perizinan Terbaru',
                'tags' => 'regulasi,perizinan',
                'description' => 'Terdapat pembaruan regulasi perizinan yang perlu diketahui oleh seluruh pemangku kepentingan.',
                'thumbnail' => '/storage/news/tes.jpg',
                'publish_st' => 'publish',
                'created_at' => '2026-02-05 09:15:00',
                'updated_at' => '2026-02-05 09:15:00',
            ],
        ];

        return view('admin.pages.news.index', compact('artikels'));
    }

    public function create()
    {
        return view('admin.pages.news.create');
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|email|unique:users',
        //     'password' => 'required|min:6',
        //     'roles' => 'nullable|array',
        // ]);

        // $user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => bcrypt($request->password),
        // ]);

        // if ($request->roles) {
        //     $user->syncRoles($request->roles);
        // }

        // return redirect()->route('news.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        return view('admin.pages.news.edit');
    }

    public function update(Request $request, User $user)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|email|unique:users,email,' . $user->id,
        //     'password' => 'nullable|min:6',
        //     'roles' => 'nullable|array',
        // ]);

        // $user->update([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => $request->password
        //         ? bcrypt($request->password)
        //         : $user->password,
        // ]);

        // $user->syncRoles($request->roles ?? []);

        // return redirect()->route('news.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        return redirect()->route('news.index')->with('success', 'User deleted successfully');
    }
}
