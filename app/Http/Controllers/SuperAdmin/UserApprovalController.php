<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserApprovalController extends Controller
{
    // Tampilkan semua user pending
    public function index()
    {
        $pendingUsers = User::where('status', 'pending')->get();
        return view('superadmin.users.index', compact('pendingUsers'));
    }

    // Setujui user
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'approved';
        $user->save();

        // Kirim notif Telegram ke user
        $this->sendTelegramNotification($user, '✅ Akun kamu telah disetujui oleh Super Admin. Sekarang kamu bisa login.');

        return back()->with('success', 'User berhasil disetujui.');
    }

    // Tolak user
    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'rejected';
        $user->save();

        // Kirim notif Telegram ke user
        $this->sendTelegramNotification($user, '❌ Pendaftaran kamu ditolak oleh Super Admin. Silakan hubungi admin untuk informasi lebih lanjut.');

        return back()->with('error', 'User ditolak.');
    }

    // Fungsi kirim notif Telegram
    private function sendTelegramNotification($user, $message)
    {
        try {
            $botToken = env('TELEGRAM_BOT_TOKEN');
            if (!$botToken || !$user->telegram_username) {
                Log::warning('Telegram notification skipped for user: ' . $user->username);
                return;
            }

            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            $payload = [
                'chat_id' => '@' . $user->telegram_username,
                'text' => $message,
            ];

            Http::post($url, $payload);
            Log::info('Telegram message sent to user: ' . $user->username);
        } catch (\Exception $e) {
            Log::error('Telegram send failed: ' . $e->getMessage());
        }
    }
}
