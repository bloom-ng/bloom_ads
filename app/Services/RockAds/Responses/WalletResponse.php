<?php

namespace App\Services\RockAds\Responses;

use App\Services\RockAds\Data\DataTransferObject;

class WalletData extends DataTransferObject
{
    public string $id;
    public string $code;
    public string $name;
    public string $balance;
    public string $currency_code;
    public bool $auto_payment;
    public string $auto_payment_threshold;
    public string $created_at;
}

class WalletsResponse extends DataTransferObject
{
    public int $total;
    /** @var WalletData[] */
    public array $data;

    public function __construct(array $parameters = [])
    {
        $this->total = $parameters['total'];
        $this->data = array_map(
            fn (array $wallet) => new WalletData($wallet),
            $parameters['data'] ?? []
        );
    }
}

class SingleWalletResponse extends DataTransferObject
{
    public WalletData $data;

    public function __construct(array $parameters = [])
    {
        $this->data = new WalletData($parameters['data'] ?? []);
    }
} 