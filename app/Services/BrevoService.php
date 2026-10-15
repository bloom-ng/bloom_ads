<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    protected $baseUrl;
    protected $apiKey;
    protected $smsSender;

    public function __construct()
    {
        $this->baseUrl = 'https://api.brevo.com/v3';
        $this->apiKey = config('services.brevo.api_key');
        $this->smsSender = config('services.brevo.sms_sender');
    }

    /**
     * Send a transactional SMS. $recipient is E.164 ("+234...").
     */
    public function sendSms(string $recipient, string $content, string $tag = 'phone-verification'): bool
    {
        // Local development without Brevo credentials: write the message to the log instead
        if (!$this->apiKey && app()->environment('local')) {
            Log::info('Brevo SMS (not sent, no BREVO_API_KEY)', ['to' => $recipient, 'content' => $content]);
            return true;
        }

        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'accept' => 'application/json',
            ])->post($this->baseUrl . '/transactionalSMS/send', [
                'sender' => $this->smsSender,
                'recipient' => ltrim($recipient, '+'),
                'content' => $content,
                'type' => 'transactional',
                'tag' => $tag,
            ]);

            if (!$response->successful()) {
                Log::error('Brevo SMS failed', [
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Brevo SMS failed: ' . $e->getMessage());
            return false;
        }
    }
}
