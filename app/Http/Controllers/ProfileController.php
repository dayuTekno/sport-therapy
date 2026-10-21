<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan halaman edit profil & foto profil
     */
    public function edit()
    {
        $user = auth()->user();

        return view('admin.pages.profile.edit', compact('user'));
    }

    /**
     * Perbarui foto profil, informasi akun, dan kata sandi
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'avatar.image' => 'File foto profil harus berupa gambar.',
            'avatar.mimes' => 'Format foto yang didukung: JPEG, PNG, JPG, atau WEBP.',
            'avatar.max' => 'Ukuran file foto maksimal adalah 2MB.',
            'current_password.current_password' => 'Password saat ini tidak cocok dengan kata sandi akun Anda.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal harus 6 karakter.',
        ]);

        // 1. Tangani Unggah Foto Profil Baru
        if ($request->hasFile('avatar')) {
            $oldAvatar = $user->getRawOriginal('avatar');
            if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
                Storage::disk('public')->delete($oldAvatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // 2. Tangani Hapus Foto Profil jika diminta
        if ($request->boolean('remove_avatar') && !$request->hasFile('avatar')) {
            $oldAvatar = $user->getRawOriginal('avatar');
            if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
                Storage::disk('public')->delete($oldAvatar);
            }
            $user->avatar = null;
        }

        // 3. Update Identitas Nama & Email
        $user->name = $request->name;
        $user->email = $request->email;

        // 4. Update Password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Foto profil dan informasi akun Anda berhasil diperbarui!');
    }

    /**
     * Hapus foto profil dan kembalikan ke avatar default
     */
    public function destroyAvatar()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $oldAvatar = $user->getRawOriginal('avatar');
        if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
            Storage::disk('public')->delete($oldAvatar);
        }

        $user->avatar = null;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil dihapus dan dikembalikan ke avatar default.');
    }
}
