<?php

namespace App\Console\Commands;

use App\Models\AdminSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCurrencyRates extends Command
{
    protected $signature = 'currency:fetch-rates {--recalculate : Only recalculate rates with current margin without fetching from API}';
    protected $description = 'Fetch currency rates from API and calculate Bloom rates with margin';

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
            $margin = AdminSetting::where('key', 'currency_margin')->first()?->value ?? 0;

            if (!$this->option('recalculate')) {
                // Fetch new rates from API
                $apiKey = config('services.currency_freaks.api_key');
                
                $response = Http::get("https://api.currencyfreaks.com/v2.0/rates/latest", [
                    'apikey' => $apiKey,
                    'symbols' => 'USD,GBP,NGN'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    $usdRate = $data['rates']['USD'] ?? '1';
                    $gbpRate = $data['rates']['GBP'] ?? '0';
                    $ngnRate = $data['rates']['NGN'] ?? '0';
                    
                    // Get current API rates as fallback
                    $currentUsdApiRate = AdminSetting::where('key', 'usd_api_rate')->first()?->value;
                    $currentGbpApiRate = AdminSetting::where('key', 'gbp_api_rate')->first()?->value;
                    
                    // If NGN rate is 0 or not present, keep using current rate
                    if (empty($ngnRate) || $ngnRate == '0') {
                        if ($currentUsdApiRate) {
                            $ngnRate = $currentUsdApiRate;
                            $this->log('API returned 0 for NGN. Using current USD API rate: ' . $currentUsdApiRate);
                        } else {
                            $ngnRate = '1800';
                            $this->log('No current rate available. Using default NGN rate: 1800');
                        }
                    }

                    // If GBP rate is 0 or not present, calculate from current GBP API rate
                    if (empty($gbpRate) || $gbpRate == '0') {
                        if ($currentGbpApiRate && $currentUsdApiRate) {
                            $gbpRate = $currentUsdApiRate / $currentGbpApiRate;
                            $this->log('API returned 0 for GBP. Using rate calculated from current rates: ' . $gbpRate);
                        } else {
                            $gbpRate = '0.7826';
                            $this->log('No current rate available. Using default GBP rate: 0.7826');
                        }
                    }

                    // Calculate raw API rates in NGN
                    $usdToNgnApiRate = $ngnRate;
                    $gbpToNgnApiRate = $ngnRate / $gbpRate;

                    // Store raw API rates (without margin)
                    AdminSetting::updateOrCreate(
                        ['key' => 'usd_api_rate'],
                        ['name' => 'USD API RATE', 'value' => $usdToNgnApiRate]
                    );
                    $usdRate->touch();

                    AdminSetting::updateOrCreate(
                        ['key' => 'gbp_api_rate'],
                        ['name' => 'GBP API RATE', 'value' => $gbpToNgnApiRate]
                    );
                    $gbpRate->touch();

                    $this->log('New API rates fetched successfully!');
                } else {
                    $this->log('API call failed. Using existing API rates.', 'error');
                }
            }

            // Get current raw API rates from database (without margin)
            $usdApiRate = AdminSetting::where('key', 'usd_api_rate')->first()?->value ?? 1800;
            $gbpApiRate = AdminSetting::where('key', 'gbp_api_rate')->first()?->value ?? 2300;

            // Calculate Bloom rates by adding margin to raw API rates
            $usdBloomRate = $usdApiRate + $margin;
            $gbpBloomRate = $gbpApiRate + $margin;

            // Store Bloom rates (API rate + margin)
            AdminSetting::updateOrCreate(
                ['key' => 'usd_rate'],
                ['name' => 'USD RATE', 'value' => $usdBloomRate]
            );

            AdminSetting::updateOrCreate(
                ['key' => 'gbp_rate'],
                ['name' => 'GBP RATE', 'value' => $gbpBloomRate]
            );

            $this->log("\nCurrent Settings:");
            $this->log("Base Currency: NGN");
            $this->log("Fixed Margin: {$margin} NGN");
            
            $this->log("\nRaw API Rates (without margin):");
            $this->log("1 USD = {$usdApiRate} NGN");
            $this->log("1 GBP = {$gbpApiRate} NGN");
            
            $this->log("\nBloom Rates (API rate + margin):");
            $this->log("1 USD = {$usdBloomRate} NGN");
            $this->log("1 GBP = {$gbpBloomRate} NGN");

        } catch (\Exception $e) {
            $this->log('Error: ' . $e->getMessage(), 'error');
        }
    }
}
