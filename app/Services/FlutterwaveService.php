<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwaveService
{
    protected $baseUrl;
    protected $secretKey;
    protected $publicKey;
    protected $encryptionKey;

    public function __construct()
    {
        $this->baseUrl = 'https://api.flutterwave.com/v3';
        $this->secretKey = config('services.flutterwave.secret_key');
        $this->publicKey = config('services.flutterwave.public_key');
        $this->encryptionKey = config('services.flutterwave.encryption_key');
    }

    /**
     * Initialize a payment transaction
     */
    public function initializePayment(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payments', [
                'tx_ref' => $data['reference'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'redirect_url' => $data['callback_url'],
                'payment_options' => 'card,banktransfer',
                'customer' => [
                    'email' => $data['customer_email'],
                    'name' => $data['customer_name'],
                ],
                'meta' => [
                    'organization_id' => $data['organization_id'],
                    'wallet_id' => $data['wallet_id'] ?? null,
                ],
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave payment initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction($transactionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/transactions/' . $transactionId . '/verify');

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave transaction verification failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($signature, $payload)
    {
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $this->secretKey);
        return hash_equals($signature, $expectedSignature);
    }
}
