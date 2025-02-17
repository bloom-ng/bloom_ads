<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\AdminSetting;

class FetchCurrencyRates extends Command
{
    protected $signature = 'currency:fetch-rates';
    protected $description = 'Fetch and store currency rates from CurrencyFreaks API';

    public function handle()
    {
        $apiKey = config('services.currency_freaks.api_key');
        
        try {
            // Fetch NGN rate
            $ngnResponse = Http::get("https://api.currencyfreaks.com/v2.0/rates/latest", [
                'apikey' => $apiKey,
                'symbols' => 'NGN'
            ]);
            
            // Fetch GBP rate
            $gbpResponse = Http::get("https://api.currencyfreaks.com/v2.0/rates/latest", [
                'apikey' => $apiKey,
                'symbols' => 'GBP'
            ]);

            if ($ngnResponse->successful() && $gbpResponse->successful()) {
                $ngnData = $ngnResponse->json();
                $gbpData = $gbpResponse->json();

                // Get the USD/NGN rate first (this is our base for conversion)
                $ngnRate = $ngnData['rates']['NGN'] ?? '0';
                $gbpRate = $gbpData['rates']['GBP'] ?? '0';

                // Convert GBP rate to NGN (if GBP/USD is 0.79, and USD/NGN is 1800, then GBP/NGN is 0.79 * 1800)
                $gbpToNgnRate = $gbpRate * $ngnRate;

                // Store NGN rate
                AdminSetting::updateOrCreate(
                    ['key' => 'ngn_rate'],
                    [
                        'name' => 'NGN RATE',
                        'value' => $ngnRate,
                    ]
                );

                // Store GBP rate
                AdminSetting::updateOrCreate(
                    ['key' => 'gbp_rate'],
                    [
                        'name' => 'GBP RATE',
                        'value' => $gbpToNgnRate,
                    ]
                );

                $this->info('Currency rates updated successfully.');
            } else {
                $this->error('Failed to fetch currency rates.');
            }
        } catch (\Exception $e) {
            $this->error('Error fetching currency rates: ' . $e->getMessage());
        }
    }
}
