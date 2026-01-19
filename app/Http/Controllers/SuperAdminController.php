<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\TelegramHelper; // 🔹 pastiin Helper-mu di sini

class SuperAdminController extends Controller
{
    public function index()
    {
        // 🔹 Pisahin data sesuai status
        $pendingUsers = User::select('id', 'name', 'username', 'telegram_username', 'role', 'status', 'created_at')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedUsers = User::select('id', 'name', 'username', 'telegram_username', 'role', 'status')
            ->where('status', 'approved')
            ->orderBy('name')
            ->get();

        // 🔹 Hitung statistik
        $counts = [
            'total' => User::count(),
            'pending' => User::where('status', 'pending')->count(),
            'approved' => User::where('status', 'approved')->count(),
            'rejected' => User::where('status', 'rejected')->count(),
        ];

        return view('superadmin.dashboard', compact('pendingUsers', 'approvedUsers', 'counts'));
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'approved']);

        // 🔹 Kirim notifikasi Telegram via Helper
        TelegramHelper::sendMessage(
            "@{$user->telegram_username}",
            null,
            "✅ Akun Anda telah *Disetujui* oleh Super Admin. Sekarang Anda bisa login ke sistem sarpras."
        );

        return redirect()->back()->with('status', "Akun {$user->username} berhasil disetujui!");
    }

    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'rejected']);

        TelegramHelper::sendMessage(
            "@{$user->telegram_username}",
            null,
            "❌ Mohon maaf, akun Anda *Ditolak* oleh Super Admin."
        );

        return redirect()->back()->with('status', "Akun {$user->username} telah ditolak.");
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|in:user,admin,super_admin']);

        $user = User::findOrFail($id);
        $oldRole = $user->role;

        $user->update(['role' => $request->role]);
        $user->syncRoles([$request->role]);

        if (auth()->id() === $user->id) {
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
        }

        TelegramHelper::sendMessage(
            "@{$user->telegram_username}",
            null,
            "⚙️ Role akun Anda telah diubah dari *{$oldRole}* menjadi *{$user->role}* oleh Super Admin."
        );

        return redirect()->back()->with('status', 'Role berhasil diperbarui dan notifikasi dikirim.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('status', "Akun {$user->username} berhasil dihapus.");
    }

    public function profile()
    {
        $user = auth()->user();
        return view('superadmin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telegram_username' => 'nullable|string|max:100',
        ]);

        $user = auth()->user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'telegram_username' => ltrim($request->telegram_username ?? '', '@'),
        ]);

        return redirect()->back()->with('status', 'Profil berhasil diperbarui!');
    }
}
