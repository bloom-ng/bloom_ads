<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $accessToken;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->baseUrl = config('services.paypal.mode') === 'live'
            ? 'https://api.paypal.com'
            : 'https://api.sandbox.paypal.com';
    }

    /**
     * Get PayPal access token
     */
    protected function getAccessToken()
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post($this->baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);

            $data = $response->json();
            $this->accessToken = $data['access_token'];
            return $this->accessToken;
        } catch (\Exception $e) {
            Log::error('PayPal authentication failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a PayPal order
     */
    public function createOrder(array $data)
    {
        try {
            if (!$this->accessToken) {
                $this->getAccessToken();
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $data['reference'],
                    'amount' => [
                        'currency_code' => $data['currency'],
                        'value' => number_format($data['amount'], 2, '.', ''),
                    ],
                    'custom_id' => json_encode([
                        'organization_id' => $data['organization_id'],
                        'wallet_id' => $data['wallet_id'] ?? null,
                    ]),
                ]],
                'application_context' => [
                    'return_url' => $data['success_url'],
                    'cancel_url' => $data['cancel_url'],
                ],
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('PayPal order creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Capture a PayPal order
     */
    public function captureOrder($orderId)
    {
        try {
            if (!$this->accessToken) {
                $this->getAccessToken();
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . "/v2/checkout/orders/{$orderId}/capture");

            return $response->json();
        } catch (\Exception $e) {
            Log::error('PayPal order capture failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($headers, $payload)
    {
        try {
            if (!$this->accessToken) {
                $this->getAccessToken();
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v1/notifications/verify-webhook-signature', [
                'auth_algo' => $headers['PAYPAL-AUTH-ALGO'],
                'cert_url' => $headers['PAYPAL-CERT-URL'],
                'transmission_id' => $headers['PAYPAL-TRANSMISSION-ID'],
                'transmission_sig' => $headers['PAYPAL-TRANSMISSION-SIG'],
                'transmission_time' => $headers['PAYPAL-TRANSMISSION-TIME'],
                'webhook_id' => config('services.paypal.webhook_id'),
                'webhook_event' => $payload,
            ]);

            return $response->json()['verification_status'] === 'SUCCESS';
        } catch (\Exception $e) {
            Log::error('PayPal webhook verification failed: ' . $e->getMessage());
            return false;
        }
    }
}
