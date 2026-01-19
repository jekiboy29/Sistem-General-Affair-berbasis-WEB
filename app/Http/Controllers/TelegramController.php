<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    public static function sendToChat($chatId, $text, $topicId = null)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        if ($topicId) {
            $data['message_thread_id'] = $topicId;
        }

        return \Illuminate\Support\Facades\Http::post($url, $data);
    }

}
