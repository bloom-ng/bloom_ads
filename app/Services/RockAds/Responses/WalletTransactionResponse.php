<?php

namespace App\Services\RockAds\Responses;

use App\Services\RockAds\Data\DataTransferObject;

class TransactionAdAccountData extends DataTransferObject
{
    public string $id;
    public string $account_id;
    public string $name;
    public string $alias_name;
}

class TransactionUserData extends DataTransferObject
{
    public string $name;
    public string $email;
}

class TransactionData extends DataTransferObject
{
    public string $id;
    public string $type;
    public string $kind;
    public ?TransactionAdAccountData $ad_account;
    public TransactionUserData $user;
    public string $amount;
    public string $currency_code;
    public string $commission_rate;
    public string $commission_amount;
    public string $card_fee_rate;
    public string $card_fee;
    public string $total_fee;
    public string $created_at;

    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
        $this->user = new TransactionUserData($parameters['user'] ?? []);
        $this->ad_account = isset($parameters['ad_account']) 
            ? new TransactionAdAccountData($parameters['ad_account']) 
            : null;
    }
}

class WalletTransactionsResponse extends DataTransferObject
{
    public int $total;
    /** @var TransactionData[] */
    public array $data;

    public function __construct(array $parameters = [])
    {
        $this->total = $parameters['total'];
        $this->data = array_map(
            fn (array $transaction) => new TransactionData($transaction),
            $parameters['data'] ?? []
        );
    }
} 