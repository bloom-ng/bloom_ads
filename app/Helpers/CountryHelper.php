<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CountryHelper
{
    public static function getCountryCodes()
    {
        return [
            ['code' => '+1', 'name' => 'USA/Canada'],
            ['code' => '+44', 'name' => 'UK'],
            ['code' => '+234', 'name' => 'Nigeria'],
            ['code' => '+233', 'name' => 'Ghana'],
            ['code' => '+254', 'name' => 'Kenya'],
            ['code' => '+27', 'name' => 'South Africa'],
            ['code' => '+91', 'name' => 'India'],
            // Add more country codes as needed
        ];
    }

    public static function getCountries()
    {
        try {
            $response = Http::get('https://restcountries.com/v3.1/all?fields=name,flags');
            if ($response->successful()) {
                $countries = $response->json();
                usort($countries, function ($a, $b) {
                    return $a['name']['common'] <=> $b['name']['common'];
                });
                return $countries;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching countries: ' . $e->getMessage());
        }

        // Fallback countries
        return [
            ['name' => ['common' => 'United States'], 'flags' => ['png' => '']],
            ['name' => ['common' => 'United Kingdom'], 'flags' => ['png' => '']],
            ['name' => ['common' => 'Nigeria'], 'flags' => ['png' => '']],
            ['name' => ['common' => 'Ghana'], 'flags' => ['png' => '']],
            ['name' => ['common' => 'Kenya'], 'flags' => ['png' => '']],
        ];
    }
}
