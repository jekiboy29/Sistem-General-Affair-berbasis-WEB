<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use app\Helpers\TelegramHelper;

class FrontendAuthController extends Controller
{
    /**
     * Tampilkan halaman register
     */
    public function showRegister()
    {
        return view('register');
    }

    /**
     * Proses register user baru
     */
    public function register(Request $request)
    {
        Log::info('Register method triggered', $request->all());

        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:50', 'unique:users,username'],
                'telegram_username' => ['required', 'string', 'max:255'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'role' => ['required', 'in:user,admin'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        try {
            // 🔹 buat user baru
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'telegram_username' => ltrim($data['telegram_username'], '@'),
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'status' => 'pending',
            ]);

            // 🔥 otomatis assign role ke user (SPATIE)
            $user->assignRole($data['role']);
        } catch (\Exception $e) {
            return back()->withErrors(['register' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }

    // 🔔 Kirim notifikasi Telegram via Helper
    \App\Helpers\TelegramHelper::sendRegisterNotification($user);

        return redirect()->route('login')->with('status', 'Registrasi berhasil! Tunggu persetujuan Super Admin.');
    }

    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        return view('login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

            // ✅ Cek kredensial khusus stokopname
        if ($request->username === 'stokopname' && $request->password === 'maustokopnamekak') {
            // Simpan session sederhana biar halaman event/dashboard bisa diakses
            session(['stokopname_logged_in' => true]);
            return redirect('/event/dashboard');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // 🔥 Force refresh roles dari database (biar gak pakai cache lama)
            $user->load('roles');
            app()->forgetInstance('spatie.permission.cache');

            if ($user->hasRole('super_admin')) {
                return redirect()->route('superadmin.dashboard');
            } elseif ($user->hasRole('admin')) {
                return redirect()->route('admin.barang.dashboard');
            } elseif ($user->hasRole('user')) {
                return redirect()->intended(\App\Providers\RouteServiceProvider::HOME);
            }

            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak dikenali.');
        }

        return back()->withErrors(['username' => 'Username atau password salah.']);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('status', 'Anda telah logout.');
    }
}
