<?php

namespace App\Services\RockAds\Responses;

use App\Services\RockAds\Data\DataTransferObject;

class CommissionData extends DataTransferObject
{
    public string $rate;
    public string $category_name;
}

class AdAccountData extends DataTransferObject
{
    public string $id;
    public string $account_id;
    public string $account_name;
    public string $wallet_id;
    public string $alias_name;
    public string $balance;
    public string $currency_code;
    public string $status;
    public string $platform_id;
    public string $timezone;
    public string $created_at;
    public ?string $ads_manager_url;
    public CommissionData $commission;

    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
        $this->commission = new CommissionData($parameters['commission'] ?? []);
    }
}

class AdAccountsResponse extends DataTransferObject
{
    public int $total;
    /** @var AdAccountData[] */
    public array $data;

    public function __construct(array $parameters = [])
    {
        $this->total = $parameters['total'];
        $this->data = array_map(
            fn (array $account) => new AdAccountData($account),
            $parameters['data'] ?? []
        );
    }
}

class SingleAdAccountResponse extends DataTransferObject
{
    public AdAccountData $data;

    public function __construct(array $parameters = [])
    {
        $this->data = new AdAccountData($parameters['data'] ?? []);
    }
} 