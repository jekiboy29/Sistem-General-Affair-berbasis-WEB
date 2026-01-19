<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct()
    {
        // ✅ Hanya user yang sudah login yang bisa akses
        $this->middleware('auth');
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // ✅ Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'telegram_username' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'cropped_image' => 'nullable|string', // base64 dari cropper
        ]);

        // ✅ Jika ada gambar baru (upload atau hasil crop)
        if ($request->hasFile('profile_picture') || $request->cropped_image) {
            // Hapus foto lama jika ada
            if ($user->profile_picture && Storage::exists('public/profile_pictures/'.$user->profile_picture)) {
                Storage::delete('public/profile_pictures/'.$user->profile_picture);
            }

            // 🖼️ 1. Jika hasil crop (base64)
            if ($request->cropped_image) {
                try {
                    $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $request->cropped_image);
                    $imageData = str_replace(' ', '+', $imageData);

                    $fileName = time().'_'.Str::random(6).'.jpg';
                    Storage::put('public/profile_pictures/'.$fileName, base64_decode($imageData));

                    $user->profile_picture = $fileName;
                } catch (\Exception $e) {
                    // fallback kalau base64 gagal diproses
                    if ($request->hasFile('profile_picture')) {
                        $fileName = time().'.'.$request->file('profile_picture')->extension();
                        $request->file('profile_picture')->storeAs('public/profile_pictures', $fileName);
                        $user->profile_picture = $fileName;
                    }
                }
            }
            // 📁 2. Jika tidak ada crop, tapi user upload file biasa
            elseif ($request->hasFile('profile_picture')) {
                $fileName = time().'.'.$request->file('profile_picture')->extension();
                $request->file('profile_picture')->storeAs('public/profile_pictures', $fileName);
                $user->profile_picture = $fileName;
            }
        }

        // ✅ Update data profil lainnya
        $user->name = $request->name;
        $user->username = $request->username;
        $user->telegram_username = $request->telegram_username;
        $user->save();

        // 🔁 Redirect sesuai role
        switch ($user->role) {
            case 'super_admin':
                $redirectRoute = 'superadmin.dashboard';
                break;
            case 'admin':
                $redirectRoute = 'admin.dashboard';
                break;
            case 'user':
                $redirectRoute = 'user.peminjaman.dashboard';
                break;
            default:
                $redirectRoute = 'dashboard';
                break;
        }

        return redirect()->route($redirectRoute)->with('status', 'Profil berhasil diperbarui!');
    }

        public function uploadCropped(Request $request)
    {
        if ($request->hasFile('cropped_image') || $request->cropped_image) {
            $user = auth()->user();

            $image = $request->file('cropped_image');
            if (!$image) {
                // dari blob (AJAX)
                $imageData = file_get_contents($request->cropped_image);
                $imageName = 'profile_' . time() . '.jpg';
                $path = 'profile_pictures/' . $imageName;
                Storage::disk('public')->put($path, $imageData);
            } else {
                // fallback manual upload
                $imageName = 'profile_' . time() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('profile_pictures', $imageName, 'public');
            }

            $user->profile_picture = basename($path);
            $user->save();

            return response()->json([
                'success' => true,
                'url' => asset('storage/'.$path)
            ]);
        }

        return response()->json(['success' => false], 400);
    }

}
