<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): void
    {
        $serverKey = config('services.fcm.server_key');

        if (!$serverKey) {
            Log::warning('FCM server key belum diatur. Notifikasi dilewati.');
            return;
        }

        $chunks = array_chunk(array_values(array_unique($tokens)), 500);
        foreach ($chunks as $chunk) {
            $payload = [
                'registration_ids' => $chunk,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
                'priority' => 'high',
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            if (!$response->successful()) {
                Log::warning('FCM request gagal', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        }
    }
}
