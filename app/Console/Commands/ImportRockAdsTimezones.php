<?php

namespace App\Console\Commands;

use App\Models\RockAdsTimezone;
use App\Services\RockAds;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportRockAdsTimezones extends Command
{
    protected $signature = 'rockads:import-timezones';
    protected $description = 'Import timezones from RockAds API and store them in the database';

    public function handle(RockAds $rockAds)
    {
        try {
            $this->info('Checking RockAds API configuration...');
            Log::info('Starting RockAds timezone import');
            
            // Verify API credentials are configured
            if (empty(config('services.rockads.api_key')) || empty(config('services.rockads.api_secret'))) {
                $error = 'RockAds API credentials are not configured. Please check your .env file.';
                $this->error($error);
                Log::error($error);
                return 1;
            }

            $this->info('Fetching timezones from RockAds API...');
            $response = $rockAds->getTimezonesFromApi();
            
            if (empty($response->timezones)) {
                $this->warn('No timezones returned from API');
                Log::warning('RockAds API returned empty timezone list');
                return 1;
            }

            Log::info('Retrieved timezones from API', [
                'count' => count($response->timezones)
            ]);

            $this->info('Storing timezones in database...');
            $count = 0;
            foreach ($response->timezones as $timezone) {
                Log::debug('Processing timezone', [
                    'id' => $timezone->id,
                    'name' => $timezone->name
                ]);
                
                RockAdsTimezone::updateOrCreate(
                    ['timezone_id' => $timezone->id],
                    [
                        'name' => $timezone->name,
                        'offset_str' => $timezone->offset_str,
                        'offset' => $timezone->offset,
                    ]
                );
                $count++;
            }

            $this->info("Successfully imported {$count} timezones");
            Log::info('Timezone import completed', ['count' => $count]);
            
        } catch (\Exception $e) {
            Log::error('Failed to import RockAds timezones', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Failed to import timezones: ' . $e->getMessage());
            return 1;
        }
    }
}
