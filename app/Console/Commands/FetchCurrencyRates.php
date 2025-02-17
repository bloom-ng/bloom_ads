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
            // Get the margin from admin settings (default to 0 if not set)
            // This will be a fixed amount in NGN to add to the rates
            $margin = AdminSetting::where('key', 'currency_margin')
                ->first()?->value ?? 0;
            
            // Fetch all three rates in a single API call
            $response = Http::get("https://api.currencyfreaks.com/v2.0/rates/latest", [
                'apikey' => $apiKey,
                'symbols' => 'USD,GBP,NGN'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Get the rates (USD is base in API response)
                $usdRate = $data['rates']['USD'] ?? '1';  // Will be 1 since USD is base
                $gbpRate = $data['rates']['GBP'] ?? '0';
                $ngnRate = $data['rates']['NGN'] ?? '0';
                
                // Add fixed margin to NGN rates
                $usdToNgnRate = $ngnRate + $margin;
                
                // Calculate GBP rate in NGN using:
                // If 1 USD = 0.793 GBP and 1 USD = 1530 NGN
                // Then 1 GBP = 1530/0.793 NGN
                // Then add margin
                $gbpToNgnRate = ($ngnRate / $gbpRate) + $margin;
                
                // Store USD rate (in NGN)
                AdminSetting::updateOrCreate(
                    ['key' => 'usd_rate'],
                    [
                        'name' => 'USD RATE',
                        'value' => $usdToNgnRate,
                    ]
                );

                // Store GBP rate (in NGN)
                AdminSetting::updateOrCreate(
                    ['key' => 'gbp_rate'],
                    [
                        'name' => 'GBP RATE',
                        'value' => $gbpToNgnRate,
                    ]
                );

                $this->info('Currency rates updated successfully.');
                $this->info("Base Currency: NGN");
                $this->info("Fixed Margin: {$margin} NGN");
                $this->info("USD Rate (with margin): 1 USD = {$usdToNgnRate} NGN");
                $this->info("GBP Rate (with margin): 1 GBP = {$gbpToNgnRate} NGN");
                
                // For verification
                $this->info("\nOriginal API Response Rates (USD base):");
                $this->info("1 USD = {$ngnRate} NGN");
                $this->info("1 USD = {$gbpRate} GBP");
            } else {
                $this->error('Failed to fetch currency rates.');
            }
        } catch (\Exception $e) {
            $this->error('Error fetching currency rates: ' . $e->getMessage());
        }
    }
}
