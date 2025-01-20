<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Wallet;

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
     */
    public function initiateTransfer(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transfers', [
                'account_bank' => $data['bank_code'],
                'account_number' => $data['account_number'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'narration' => $data['narration'] ?? 'Withdrawal from wallet',
                'reference' => $data['reference'],
                'callback_url' => $data['callback_url'],
                'debit_currency' => $data['currency']
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave transfer initiation failed: ' . $e->getMessage());
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

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave account verification failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
