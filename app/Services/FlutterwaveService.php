<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Wallet;
use Illuminate\Support\Str;

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
            // Get current rate if needed for conversion
            if ($data['wallet_currency'] !== 'NGN') {
                $rate = Wallet::getRate(strtolower($data['wallet_currency']));
                $data['converted_amount'] = $data['amount'] * (1 / $rate);
            }

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
                    'converted_amount' => $data['converted_amount'],
                    'wallet_currency' => $data['wallet_currency'],
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

    /**
     * Initiate a transfer to a bank account
     * 
     * @param array $data Transfer details including account_number, bank_code, amount, and currency
     * @return array Response from Flutterwave
     * @throws \Exception
     */
    public function initiateTransfer(array $data)
    {
        try {  
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transfers', [
                'account_number' => $data['account_number'],
                'account_bank' => $data['bank_code'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'narration' => $data['narration'] ?? 'Transfer from Bloom Ads',
                'reference' => $data['reference'] ?? Str::random(16),
                'callback_url' => $data['callback_url'] ?? null
            ]);
            Log::info('Flutterwave transfer response:', [
                'status_code' => $response->status(),
                'body' => json_encode($response->json())
            ]);

            if (!$response->successful()) {
                throw new \Exception($response->json()['message'] ?? 'Transfer failed1');
            }

            $responseData = $response->json();
            if ($responseData['status'] !== 'success') {
                throw new \Exception($responseData['message'] ?? 'Transfer failed2');
            }

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Transfer failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            Log::info('Headers sent to Flutterwave:', [
                'Authorization' => 'Bearer ' . $this->secretKey,
            ]);
            throw $e;
        }
    }

    /**
     * Get list of supported banks
     */
    public function getBanks($country = 'NG')
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/banks/' . $country);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave get banks failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify bank account details
     */
    public function verifyBankAccount($accountNumber, $bankCode)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/accounts/resolve', [
                'account_number' => $accountNumber,
                'account_bank' => $bankCode
            ]);
            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return [
                        'status' => 'success',
                        'data' => [
                            'account_name' => $data['data']['account_name'],
                            'account_number' => $data['data']['account_number']
                        ]
                    ];
                }
            }

            // Log the error response for debugging
            Log::error('Flutterwave account verification failed. Response: ' . json_encode($response->json()));

            return [
                'status' => 'error',
                'message' => $response->json()['message'] ?? 'Could not verify account'
            ];
        } catch (\Exception $e) {
            Log::error('Flutterwave bank account verification failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to verify account'
            ];
        }
    }
}
