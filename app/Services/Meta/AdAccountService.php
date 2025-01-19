<?php

namespace App\Services\Meta;

use App\Services\Meta\Data\CreateAdAccountDTO;
use App\Services\Meta\Data\ClaimAdAccountDTO;
use App\Services\Meta\Data\AssignUserDTO;
use App\Services\Meta\Data\PaginatedResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class AdAccountService
{
    private string $businessId;
    private string $accessToken;
    private string $baseUrl = 'https://graph.facebook.com/v21.0';
    
    public function __construct($businessId = null, $accessToken = null)
    {
        $this->businessId = $businessId ?? config('services.meta.business_id');
        $this->accessToken = $accessToken ?? config('services.meta.access_token');
    }

    public function createAdAccount(CreateAdAccountDTO $dto): array
    {
        $endpoint = "{$this->baseUrl}/{$this->businessId}/adaccount";
        
        $response = Http::post($endpoint, [
            'name' => $dto->name,
            'currency' => $dto->currency,
            'timezone_id' => $dto->timezoneId,
            'end_advertiser' => $dto->endAdvertiser,
            'media_agency' => $dto->mediaAgency,
            'partner' => $dto->partner,
            'invoice' => $dto->invoice,
            'access_token' => $this->accessToken,
        ]);

        return $this->handleResponse($response);
    }

    public function claimAdAccount(ClaimAdAccountDTO $dto): array
    {
        $endpoint = "{$this->baseUrl}/{$this->businessId}/owned_ad_accounts";
        
        $response = Http::post($endpoint, [
            'adaccount_id' => $dto->adAccountId,
            'access_token' => $this->accessToken,
        ]);

        return $this->handleResponse($response);
    }

    public function getAdAccount(string $adaccountId)
    {
        $endpoint = "{$this->baseUrl}/act_{$adaccountId}";
        
        $response = Http::get($endpoint, [
            'access_token' => $this->accessToken,
            'fields' => 'account_status,amount_spent,spend_cap,balance,business,currency,name,owner,id',
        ]);

        return $this->handleResponse($response);
    }

    public function getOwnedAdAccounts(?string $before = null, ?string $after = null, int $limit = 10): PaginatedResult
    {
        $endpoint = "{$this->baseUrl}/{$this->businessId}/owned_ad_accounts";
        
        $params = [
            'fields' => 'account_status,amount_spent,balance,business,currency,name,owner,id',
            'access_token' => $this->accessToken,
            'limit' => $limit
        ];

        if ($before) {
            $params['before'] = $before;
        } elseif ($after) {
            $params['after'] = $after;
        }
        
        $response = Http::get($endpoint, $params);
        $data = $this->handleResponse($response);

        return PaginatedResult::fromMetaResponse($data);
    }

    public function getPendingAdAccounts(): Collection
    {
        $endpoint = "{$this->baseUrl}/{$this->businessId}/pending_owned_ad_accounts";
        
        $response = Http::get($endpoint, [
            'access_token' => $this->accessToken,
        ]);

        return collect($this->handleResponse($response)['data'] ?? []);
    }

    public function getClientAdAccounts(?string $before = null, ?string $after = null, int $limit = 10): PaginatedResult
    {
        $endpoint = "{$this->baseUrl}/{$this->businessId}/client_ad_accounts";
        
        $params = [
            'fields' => 'account_status,amount_spent,balance,business,currency,name,owner,id',
            'access_token' => $this->accessToken,
            'limit' => $limit
        ];

        if ($before) {
            $params['before'] = $before;
        } elseif ($after) {
            $params['after'] = $after;
        }
        
        $response = Http::get($endpoint, $params);
        $data = $this->handleResponse($response);

        return PaginatedResult::fromMetaResponse($data);
    }

    public function getPendingClientAdAccounts(): Collection
    {
        $endpoint = "{$this->baseUrl}/{$this->businessId}/pending_client_ad_accounts";
        
        $response = Http::get($endpoint, [
            'access_token' => $this->accessToken,
        ]);

        return collect($this->handleResponse($response)['data'] ?? []);
    }

    public function assignUserToAccount(AssignUserDTO $dto): array
    {
        $endpoint = "{$this->baseUrl}/act_{$dto->adAccountId}/assigned_users";
        
        $response = Http::post($endpoint, [
            'user' => $dto->userId,
            'tasks' => json_encode($dto->tasks),
            'access_token' => $this->accessToken,
        ]);

        return $this->handleResponse($response);
    }

    public function removeUserFromAccount(string $adAccountId, string $userId): bool
    {
        $endpoint = "{$this->baseUrl}/act_{$adAccountId}/assigned_users";
        
        $response = Http::delete($endpoint, [
            'user' => $userId,
            'access_token' => $this->accessToken,
        ]);

        return $this->handleResponse($response)['success'] ?? false;
    }

    /**
     * Update the spend cap for an ad account
     * 
     * @param string $adAccountId The ad account ID
     * @param float $spendCap The new spend cap value
     * @return array The updated ad account data
     */
    public function updateSpendCap(string $adAccountId, float $spendCap)
    {
        $endpoint = "{$this->baseUrl}/act_{$adAccountId}";
        
        $response = Http::post($endpoint, [
            'spend_cap' => $spendCap,
            'access_token' => $this->accessToken,
            'fields' => 'account_status,balance,currency,name,business_name,amount_spent,spend_cap'
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Fund an ad account by updating its spend cap
     * 
     * @param string $adAccountId The ad account ID
     * @param float $amount The amount to fund
     * @return array The updated ad account data
     * @throws \Exception If account is not approved or has no provider ID
     */
    public function fundAccount(string $adAccountId, float $amount)
    {
        $account = $this->getAdAccount($adAccountId);
        
        if ($account['account_status'] !== 1) { // 1 is ACTIVE status in Meta
            throw new \Exception('Ad account must be approved to fund');
        }

        $currentSpendCap = $account['spend_cap'] ?? 0;
        $newSpendCap = $currentSpendCap + $amount;
        
        return $this->updateSpendCap($adAccountId, $newSpendCap);
    }

    /**
     * Withdraw funds from an ad account by updating its spend cap
     * 
     * @param string $adAccountId The ad account ID
     * @param float $amount The amount to withdraw
     * @return array The updated ad account data
     * @throws \Exception If withdrawal amount exceeds available funds
     */
    public function withdrawFunds(string $adAccountId, float $amount)
    {
        $account = $this->getAdAccount($adAccountId);
        
        $currentSpendCap = $account['spend_cap'] ?? 0;
        $amountSpent = $account['amount_spent'] ?? 0;
        
        $availableToWithdraw = $currentSpendCap - $amountSpent;
        
        if ($amount > $availableToWithdraw) {
            throw new \Exception('Withdrawal amount exceeds available funds');
        }
        
        $newSpendCap = $currentSpendCap - $amount;
        
        return $this->updateSpendCap($adAccountId, $newSpendCap);
    }

    private function handleResponse(Response $response): array
    {
        if (!$response->successful()) {
            throw new \Exception('Meta API Error: ' . $response->body());
        }

        return $response->json();
    }

}
