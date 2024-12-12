<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use App\Services\RockAds\Responses\AdPlatformsResponse;
use App\Services\RockAds\Responses\TimezonesResponse;
use App\Services\RockAds\Requests\CreateAdAccountRequest;
use App\Services\RockAds\Requests\UpdateAdAccountRequest;
use App\Services\RockAds\Responses\AdAccountsResponse;
use App\Services\RockAds\Responses\SingleAdAccountResponse;
use App\Services\RockAds\Responses\WalletsResponse;
use App\Services\RockAds\Responses\SingleWalletResponse;
use App\Services\RockAds\Requests\AdAccountPaymentRequest;
use App\Services\RockAds\Responses\WalletTransactionsResponse;

class RockAds
{
    private string $apiKey;
    private string $apiSecret;
    private string $baseUrl = 'https://b2b-api.rockads.com/v1/';

    public function __construct()
    {
        $this->apiKey = config('services.rockads.api_key');
        $this->apiSecret = config('services.rockads.api_secret');
    }

    /**
     * Get a pre-configured HTTP client instance
     */
    private function client(): PendingRequest
    {
        $apiKey = config('services.rockads.api_key');
        $apiSecret = config('services.rockads.api_secret');

        if (!$apiKey || !$apiSecret) {
            throw new \Exception('RockAds API credentials not configured');
        }

        return Http::withHeaders([
            'X-Api-Key' => $apiKey,
            'X-Api-Secret' => $apiSecret,
            'Accept' => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    /**
     * Make a GET request to RockAds API
     */
    protected function get(string $endpoint, array $params = [])
    {
        $response = $this->client()->get($endpoint, $params)->throw()->json();
        \Log::info('API GET Response:', [
            'endpoint' => $endpoint,
            'response' => $response
        ]);
        return $response;
    }

    /**
     * Make a POST request to RockAds API
     */
    protected function post(string $endpoint, array $data = [])
    {
        return $this->client()->post($endpoint, $data)->throw()->json();
    }

    /**
     * Get list of supported advertising platforms
     */
    public function getAdPlatforms(): AdPlatformsResponse
    {
        try {
            $response = $this->get('ad-platforms');
            \Log::info('RockAds API Raw Response:', ['response' => $response]);
            
            // Pass the entire response array to the AdPlatformsResponse constructor
            return new AdPlatformsResponse([
                'response' => $response
            ]);
        } catch (\Exception $e) {
            \Log::error('RockAds API Error in getAdPlatforms:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get list of supported timezones
     */
    public function getTimezones(): TimezonesResponse
    {
        try {
            $response = $this->get('timezones');
            
            // Add detailed debug logging
            \Log::info('RockAds Timezones Raw Response:', [
                'raw' => $response,
                'structure' => json_encode($response, JSON_PRETTY_PRINT)
            ]);
            
            // Pass the entire response array to the TimezonesResponse constructor
            return new TimezonesResponse([
                'response' => $response
            ]);
        } catch (\Exception $e) {
            \Log::error('RockAds API Error in getTimezones:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get list of ad accounts
     */
    public function getAdAccounts(): AdAccountsResponse
    {
        $response = $this->get('ad-accounts');
        return new AdAccountsResponse($response);
    }

    /**
     * Get ad account details
     */
    public function getAdAccount(string $id): SingleAdAccountResponse
    {
        $response = $this->get("ad-accounts/{$id}");
        return new SingleAdAccountResponse($response);
    }

    /**
     * Create a new ad account
     * 
     * @return string The ID of the created ad account
     */
    public function createAdAccount(CreateAdAccountRequest $request): string
    {
        $response = $this->post('ad-accounts', $request->toArray());
        return $response['id'];
    }

    /**
     * Update an ad account
     */
    public function updateAdAccount(string $id, UpdateAdAccountRequest $request): void
    {
        $this->client()->put("ad-accounts/{$id}", $request->toArray())->throw();
    }

    /**
     * Get list of wallets
     */
    public function getWallets(): WalletsResponse
    {
        $response = $this->get('wallets');
        return new WalletsResponse($response);
    }

    /**
     * Get wallet details
     */
    public function getWallet(string $id): SingleWalletResponse
    {
        $response = $this->get("wallets/{$id}");
        return new SingleWalletResponse($response);
    }

    /**
     * Get wallet transactions
     */
    public function getWalletTransactions(string $id): WalletTransactionsResponse
    {
        $response = $this->get("wallets/{$id}/transactions");
        return new WalletTransactionsResponse($response);
    }

    /**
     * Deposit funds from wallet to ad account
     * 
     * Note: Transaction might not complete due to ad account status or platform errors.
     * Handle the response carefully.
     */
    public function depositToAdAccount(string $adAccountId, AdAccountPaymentRequest $request): array
    {
        return $this->post(
            "ad-accounts/{$adAccountId}/deposit",
            $request->toArray()
        );
    }

    /**
     * Withdraw funds from ad account to wallet
     * 
     * Note: Success depends on multiple conditions:
     * - Ad account status
     * - Active campaigns
     * - Available balance
     * Handle the response carefully.
     */
    public function withdrawFromAdAccount(string $adAccountId, AdAccountPaymentRequest $request): array
    {
        return $this->post(
            "ad-accounts/{$adAccountId}/withdraw",
            $request->toArray()
        );
    }
}
