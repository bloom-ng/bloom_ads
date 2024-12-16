<?php

namespace App\Services\RockAds\Responses;

use App\Services\RockAds\Data\DataTransferObject;

class PlatformData extends DataTransferObject
{
    public ?int $id = null;
    public ?string $name = null;

    public function __construct(array $parameters = [])
    {
        // \Log::info('Creating PlatformData with parameters:', $parameters);

        $this->id = $parameters['id'] ?? null;
        $this->name = $parameters['name'] ?? null;
    }

    public function getCode(): string
    {
        $platformCodes = [
            1 => 'meta',
            2 => 'snapchat',
            3 => 'tiktok',
            4 => 'google'
        ];

        $code = $platformCodes[$this->id] ?? strtolower($this->name);

        // \Log::info('Platform Code:', [
        //     'id' => $this->id,
        //     'name' => $this->name,
        //     'code' => $code
        // ]);

        return $code;
    }
}

class AdPlatformsResponse extends DataTransferObject
{
    /** @var PlatformData[] */
    public array $platforms = [];

    public function __construct(array $parameters = [])
    {
        // \Log::info('AdPlatformsResponse constructor parameters:', $parameters);

        // The data structure is different - it's directly in 'response.ad_platforms'
        $platformsData = $parameters['response']['ad_platforms'] ?? [];

        // \Log::info('Extracted platforms data:', ['data' => $platformsData]);

        // Map the data to PlatformData objects
        $this->platforms = array_map(
            function (array $platform) {
                return new PlatformData([
                    'id' => $platform['id'],
                    'name' => $platform['name']
                ]);
            },
            $platformsData
        );

        // \Log::info('Final platforms array:', [
        //     'count' => count($this->platforms),
        //     'platforms' => array_map(function ($p) {
        //         return [
        //             'id' => $p->id,
        //             'name' => $p->name,
        //             'code' => $p->getCode()
        //         ];
        //     }, $this->platforms)
        // ]);
    }
}
