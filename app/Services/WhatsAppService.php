<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $token;
    private bool   $enabled;
    private string $apiUrl;
    private int    $timeout;

    public function __construct()
    {
        // Gunakan config() bukan env() agar aman saat config:cache (production)
        $this->token   = (string) config('whatsapp.token', '');
        $this->enabled = (bool)   config('whatsapp.enabled', false);
        $this->apiUrl  = (string) config('whatsapp.api_url', 'https://api.fonnte.com/send');
        $this->timeout = (int)    config('whatsapp.timeout', 10);
    }

    public function send(string $phone, string $message): bool
    {
        if (!$this->enabled || empty($this->token) || empty($phone)) return false;

        try {
            $response = Http::withHeaders(['Authorization' => $this->token])
                ->timeout($this->timeout)
                ->post($this->apiUrl, [
                    'target'  => $phone,
                    'message' => $message,
                    'delay'   => '0',
                ]);

            if ($response->successful()) {
                Log::info('[WhatsApp] Sent to ' . $phone);
                return true;
            }

            Log::warning('[WhatsApp] Failed', ['status' => $response->status()]);
            return false;

        } catch (\Throwable $e) {
            Log::error('[WhatsApp] Exception: ' . $e->getMessage());
            return false;
        }
    }
}
