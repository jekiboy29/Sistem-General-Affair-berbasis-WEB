<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramHelper
{
    /**
     * 🔧 Fungsi umum untuk kirim pesan ke Telegram
     */
    public static function sendMessage($chatId, $threadId, $message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');

        if (!$botToken) {
            Log::warning('Telegram bot token kosong.');
            return false;
        }

        try {
            $payload = [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ];

            if ($threadId) {
                $payload['message_thread_id'] = $threadId;
            }

            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            Http::post($url, $payload);

            return true;
        } catch (\Exception $e) {
            Log::error('Gagal kirim Telegram: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🔹 1. Notifikasi Registrasi (Forum SAI)
     */
    public static function sendRegisterNotification($user)
    {
        try {
            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId   = env('SUPER_ADMIN_CHAT_ID');
            $topicId  = env('SUPER_ADMIN_TOPIC_ID');

            if (!$botToken || !$chatId) {
                Log::warning('⚠️ Telegram env belum lengkap');
                return;
            }

            $text = "📋 *Pendaftaran Baru*\n\n"
                . "👤 Nama: {$user->name}\n"
                . "🪪 Username: {$user->username}\n"
                . "💬 Telegram: @{$user->telegram_username}\n"
                . "🎯 Role: {$user->role}\n"
                . "─────────────────────────────────\n"
                . "🕓 Status: *Menunggu persetujuan Super Admin*";

            $payload = [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ];

            if ($topicId) {
                $payload['message_thread_id'] = $topicId;
            }

            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", $payload);

            Log::info('✅ Notif register terkirim via TelegramHelper!');
        } catch (\Exception $e) {
            Log::error('❌ TelegramHelper Error: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 2. Notifikasi Pengembalian Tidak Sesuai (Grup PKL Sarastya)
     */
    public static function sendPengembalianNotification($userName, $barangName)
    {
        $chatId = env('TELEGRAM_GROUP_PENGEMBALIAN_ID');
        $threadId = env('TELEGRAM_PENGEMBALIAN_TOPIC_ID');

        $message = "<b>⚠️ Pengembalian Tidak Sesuai</b>\n";
        $message .= "👤 User: <b>{$userName}</b>\n";
        $message .= "📦 Barang: <b>{$barangName}</b>\n\n";
        $message .= "Barang yang dikembalikan tidak sesuai.\n";
        $message .= "Mohon <b>user segera konfirmasi</b> ke admin Sarpras 🙏";

        return self::sendMessage($chatId, $threadId, $message);
    }
}
