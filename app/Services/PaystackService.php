<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected $baseUrl;
    protected $secretKey;
    protected $publicKey;

    public function __construct()
    {
        $this->baseUrl = 'https://api.paystack.co';
        $this->secretKey = config('services.paystack.secret_key');
        $this->publicKey = config('services.paystack.public_key');
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
            ])->post($this->baseUrl . '/transaction/initialize', [
                'email' => $data['email'],
                'amount' => $data['amount'] * 100, // Paystack expects amount in kobo
                'currency' => $data['currency'],
                'reference' => $data['reference'],
                'callback_url' => $data['callback_url'],
                'metadata' => [
                    'organization_id' => $data['metadata']['organization_id'],
                    'wallet_id' => $data['metadata']['wallet_id'] ?? null,
                    'converted_amount' => $data['metadata']['converted_amount'],
                    'wallet_currency' => $data['metadata']['wallet_currency'],
                    'custom_fields' => [
                        [
                            'display_name' => 'Customer Name',
                            'variable_name' => 'customer_name',
                            'value' => $data['name'],
                        ],
                    ],
                ],
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack payment initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack transaction verification failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * List transactions
     */
    public function listTransactions(array $params = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/transaction', $params);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack list transactions failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($signature, $payload)
    {
        $expectedSignature = hash_hmac('sha512', json_encode($payload), $this->secretKey);
        return hash_equals($signature, $expectedSignature);
    }

    /**
     * Get payment channels
     */
    public function getPaymentChannels()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/payment-channels');

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack get payment channels failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a transfer recipient
     */
    public function createTransferRecipient(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transferrecipient', [
                'type' => $data['type'],
                'name' => $data['name'],
                'account_number' => $data['account_number'],
                'bank_code' => $data['bank_code'],
                'currency' => $data['currency'] ?? 'NGN',
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack create transfer recipient failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Initiate a transfer
     */
    public function initiateTransfer(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transfer', [
                'source' => 'balance',
                'amount' => $data['amount'] * 100,
                'recipient' => $data['recipient_code'],
                'reason' => $data['reason'] ?? 'Transfer',
                'reference' => $data['reference'],
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack initiate transfer failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
