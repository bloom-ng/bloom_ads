<?php

namespace App\Services\RockAds\Responses;

use App\Services\RockAds\Data\DataTransferObject;

class AdAccountResponse extends DataTransferObject
{
    public int $total;
    /** @var AdAccountData[] */
    public array $data;

    public function __construct(array $parameters = [])
    {
        $this->total = $parameters['total'] ?? 0;
        $this->data = array_map(
            fn (array $account) => new AdAccountData($account),
            $parameters['data'] ?? []
        );
    }
}

class AdAccountData extends DataTransferObject
{
    public string $account_id;
    public string $account_name;
    public string $platform_id;
    public float $balance;
    public string $currency_code;
    public string $status;
    public ?string $ads_manager_url;

    public function __construct(array $parameters = [])
    {
        $this->account_id = $parameters['account_id'] ?? '';
        $this->account_name = $parameters['account_name'] ?? '';
        $this->platform_id = $parameters['platform_id'] ?? '';
        $this->balance = $parameters['balance'] ?? 0.0;
        $this->currency_code = $parameters['currency_code'] ?? '';
        $this->status = $parameters['status'] ?? '';
        $this->ads_manager_url = $parameters['ads_manager_url'] ?? null;
    }
} 