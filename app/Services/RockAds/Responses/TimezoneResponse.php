<?php

namespace App\Services\RockAds\Responses;

use App\Services\RockAds\Data\DataTransferObject;

class TimezoneData extends DataTransferObject
{
    public string $key;
    public string $name;
    public int $offset;
    public string $offset_str;
}

class TimezonesResponse extends DataTransferObject
{
    public int $total;
    /** @var TimezoneData[] */
    public array $data;

    public function __construct(array $parameters = [])
    {
        $this->total = $parameters['total'];
        $this->data = array_map(
            fn (array $timezone) => new TimezoneData($timezone),
            $parameters['data'] ?? []
        );
    }
} 