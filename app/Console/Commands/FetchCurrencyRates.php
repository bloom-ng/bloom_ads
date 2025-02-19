<?php

namespace App\Console\Commands;

use App\Models\AdminSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCurrencyRates extends Command
{
    protected $signature = 'currency:fetch-rates';
    protected $description = 'Fetch currency rates from CurrencyFreaks API';

    private function log($message, $type = 'info')
    {
        // Log to Laravel log
        Log::$type($message);
        
        // If command output is available, use it
        if ($this->output) {
            if ($type === 'error') {
                $this->error($message);
            } else {
                $this->info($message);
            }
        }
    }

    public function handle()
    {
        try {
            $apiKey = config('services.currency_freaks.api_key');
            $margin = AdminSetting::where('key', 'currency_margin')->first()?->value ?? 0;

            $response = Http::get("https://api.currencyfreaks.com/v2.0/rates/latest", [
                'apikey' => $apiKey,
                'symbols' => 'USD,GBP,NGN'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $usdRate = $data['rates']['USD'] ?? '1';
                $gbpRate = $data['rates']['GBP'] ?? '0';
                $ngnRate = $data['rates']['NGN'] ?? '0';
                
                // If NGN rate is 0 or not present, use default rate
                if (empty($ngnRate) || $ngnRate == '0') {
                    $ngnRate = '1800'; // Default NGN rate when API fails
                    $this->log('Using default NGN rate: 1800');
                }

                // If GBP rate is 0 or not present, use a default rate of 1 GBP = 2300 NGN
                if (empty($gbpRate) || $gbpRate == '0') {
                    $gbpRate = '0.7826'; // This gives approximately 2300 NGN when NGN is 1800
                    $this->log('Using default GBP rate: 0.7826 (approx 2300 NGN)');
                }
                
                $usdToNgnRate = $ngnRate + $margin;
                $gbpToNgnRate = ($ngnRate / $gbpRate) + $margin;
                
                AdminSetting::updateOrCreate(
                    ['key' => 'usd_rate'],
                    ['name' => 'USD RATE', 'value' => $usdToNgnRate]
                );

                AdminSetting::updateOrCreate(
                    ['key' => 'gbp_rate'],
                    ['name' => 'GBP RATE', 'value' => $gbpToNgnRate]
                );

                $this->log('Currency rates updated successfully!');
                $this->log("Base Currency: NGN");
                $this->log("Fixed Margin: {$margin} NGN");
                $this->log("USD Rate (with margin): 1 USD = {$usdToNgnRate} NGN");
                $this->log("GBP Rate (with margin): 1 GBP = {$gbpToNgnRate} NGN");
                
                $this->log("\nOriginal API Response Rates (USD base):");
                $this->log("1 USD = {$ngnRate} NGN");
                $this->log("1 USD = {$gbpRate} GBP");
            } else {
                // If API call fails, set default rates
                $defaultNgnRate = 1800;
                $defaultGbpNgnRate = 2300;
                $margin = AdminSetting::where('key', 'currency_margin')->first()?->value ?? 0;

                AdminSetting::updateOrCreate(
                    ['key' => 'usd_rate'],
                    ['name' => 'USD RATE', 'value' => $defaultNgnRate + $margin]
                );

                AdminSetting::updateOrCreate(
                    ['key' => 'gbp_rate'],
                    ['name' => 'GBP RATE', 'value' => $defaultGbpNgnRate + $margin]
                );

                $this->log('API call failed. Using default rates:', 'error');
                $this->log("USD Rate: 1 USD = " . ($defaultNgnRate + $margin) . " NGN");
                $this->log("GBP Rate: 1 GBP = " . ($defaultGbpNgnRate + $margin) . " NGN");
            }
        } catch (\Exception $e) {
            // If any error occurs, set default rates
            $defaultNgnRate = 1800;
            $defaultGbpNgnRate = 2300;
            $margin = AdminSetting::where('key', 'currency_margin')->first()?->value ?? 0;

            AdminSetting::updateOrCreate(
                ['key' => 'usd_rate'],
                ['name' => 'USD RATE', 'value' => $defaultNgnRate + $margin]
            );

            AdminSetting::updateOrCreate(
                ['key' => 'gbp_rate'],
                ['name' => 'GBP RATE', 'value' => $defaultGbpNgnRate + $margin]
            );

            $this->log('Error fetching rates: ' . $e->getMessage(), 'error');
            $this->log('Using default rates:');
            $this->log("USD Rate: 1 USD = " . ($defaultNgnRate + $margin) . " NGN");
            $this->log("GBP Rate: 1 GBP = " . ($defaultGbpNgnRate + $margin) . " NGN");
        }
    }
}
