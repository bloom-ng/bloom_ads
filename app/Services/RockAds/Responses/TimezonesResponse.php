<?php

namespace App\Services\RockAds\Responses;

use App\Services\RockAds\Data\DataTransferObject;
use Illuminate\Support\Facades\Log;

class TimezoneData extends DataTransferObject
{
    public ?string $id = null;
    public ?string $name = null;
    public ?string $offset_str = null;
    public ?int $offset = null;

    public function __construct(array $parameters = [])
    {
        Log::info('Creating TimezoneData with parameters:', $parameters);

        $this->id = $parameters['key'] ?? null;
        $this->name = $parameters['name'] ?? null;
        $this->offset_str = $parameters['offset_str'] ?? null;
        $this->offset = $parameters['offset'] ?? null;
    }
}

class TimezonesResponse extends DataTransferObject
{
    /** @var TimezoneData[] */
    public array $timezones = [];

    public function __construct(array $parameters = [])
    {
        Log::info('TimezonesResponse constructor parameters:', $parameters);

        // The data structure is different - it's in 'response.data'
        $timezonesData = $parameters['response']['data'] ?? [];

        Log::info('Extracted timezones data:', ['data' => $timezonesData]);

        // Map the data to TimezoneData objects
        $this->timezones = array_map(
            function (array $timezone) {
                return new TimezoneData([
                    'key' => $timezone['key'],
                    'name' => $timezone['name'],
                    'offset' => $timezone['offset'],
                    'offset_str' => $timezone['offset_str']
                ]);
            },
            $timezonesData
        );

        Log::info('Final timezones array:', [
            'count' => count($this->timezones),
            'timezones' => array_map(function ($tz) {
                return [
                    'id' => $tz->id,
                    'name' => $tz->name,
                    'offset_str' => $tz->offset_str
                ];
            }, $this->timezones)
        ]);
    }
}
