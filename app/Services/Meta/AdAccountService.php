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
            'fields' => 'account_status,amount_spent,balance,business,currency,name,owner,id',
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

    private function handleResponse(Response $response): array
    {
        if (!$response->successful()) {
            throw new \Exception('Meta API Error: ' . $response->body());
        }

        return $response->json();
    }

}
