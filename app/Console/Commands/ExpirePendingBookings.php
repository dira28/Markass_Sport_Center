<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExpirePendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-pending-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire bookings pending payment > 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get token from env
        $token = env('API_TOKEN');
        
        if (!$token) {
            $this->error('API_TOKEN not set in .env');
            return 1;
        }

        $this->info('Checking expired bookings...');

        try {
            // Get unpaid bookings candidates.
            // Your API appears to use both `pending` and `waiting_confirmation`/`menunggu_verifikasi`.
            // We must expire both so slots unlock.
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking', [
                    'status_pembayaran' => 'pending',
                ]);


            if ($response->failed()) {
                $this->error('Failed to fetch pending bookings: ' . $response->body());
                return 1;
            }

            $bookings = $response->json()['data'] ?? [];
            $expiredCount = 0;
            $errorCount = 0;

            $this->info("Found " . count($bookings) . " pending bookings");

            foreach ($bookings as $booking) {
                $paymentDeadline = Carbon::parse($booking['payment_deadline']);
                
                if ($paymentDeadline->isPast() && in_array($booking['status_pembayaran'] ?? null, ['pending','waiting_confirmation','menunggu_verifikasi'], true)) {
                    // Expire this booking
                    $patchRes = Http::withToken($token)
                        ->patch(env('API_URL') . "/api/booking/{$booking['id_booking']}", [
                            'status_pembayaran' => 'expired'
                        ]);

                    if ($patchRes->successful()) {
                        $expiredCount++;
                        $this->line("✓ Expired #{$booking['id_booking']} (deadline: {$booking['payment_deadline']})");
                    } else {
                        $errorCount++;
                        $this->error("✗ Failed #{$booking['id_booking']}: " . $patchRes->body());
                    }
                }
            }

            $this->info("Summary: {$expiredCount} expired, {$errorCount} errors");
            Log::info("Booking expire job complete", [
                'expired' => $expiredCount, 
                'errors' => $errorCount
            ]);

        } catch (\Exception $e) {
            $this->error('Job failed: ' . $e->getMessage());
            Log::error('Booking expire error', ['error' => $e->getMessage()]);
            return 1;
        }

        return 0;
    }
}
