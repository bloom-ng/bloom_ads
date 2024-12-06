<?php

namespace App\Services\RockAds\Responses;

use App\Services\RockAds\Data\DataTransferObject;

class AdPlatformResponse extends DataTransferObject
{
    public int $id;
    public string $name;
}

class AdPlatformsResponse extends DataTransferObject
{
    /** @var AdPlatformResponse[] */
    public array $ad_platforms;

    public function __construct(array $parameters = [])
    {
        $this->ad_platforms = array_map(
            fn (array $platform) => new AdPlatformResponse($platform),
            $parameters['ad_platforms'] ?? []
        );
    }
} 