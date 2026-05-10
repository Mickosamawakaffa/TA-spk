<?php

namespace App\Console\Commands;

use App\Models\AdminDeviceToken;
use App\Models\Kontrakan;
use App\Services\FcmService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendKontrakanAvailabilityReminder extends Command
{
    protected $signature = 'kontrakan:weekly-availability-reminder';
    protected $description = 'Kirim notifikasi mingguan ke admin untuk konfirmasi ketersediaan kontrakan';

    public function handle(FcmService $fcm): int
    {
        $threshold = now()->subDays(7);

        $needsCheckCount = Kontrakan::where('status', 'available')
            ->where(function ($query) use ($threshold) {
                $query->whereNull('availability_confirmed_at')
                    ->orWhere('availability_confirmed_at', '<=', $threshold);
            })
            ->count();

        if ($needsCheckCount === 0) {
            $this->info('Tidak ada kontrakan yang perlu konfirmasi.');
            return self::SUCCESS;
        }

        $tokens = AdminDeviceToken::pluck('token')->toArray();

        if (empty($tokens)) {
            $this->warn('Tidak ada device token admin.');
            return self::SUCCESS;
        }

        $title = 'Cek ketersediaan kontrakan';
        $body = "Ada {$needsCheckCount} kontrakan yang perlu dikonfirmasi minggu ini.";

        $fcm->sendToTokens($tokens, $title, $body, [
            'type' => 'availability_reminder',
            'count' => (string) $needsCheckCount,
        ]);

        Log::info('Weekly availability reminder sent', [
            'count' => $needsCheckCount,
            'tokens' => count($tokens),
        ]);

        $this->info('Notifikasi terkirim.');
        return self::SUCCESS;
    }
}
