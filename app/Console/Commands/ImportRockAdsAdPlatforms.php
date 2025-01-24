<?php

namespace App\Console\Commands;

use App\Models\RockAdsAdPlatform;
use App\Services\RockAds;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportRockAdsAdPlatforms extends Command
{
    protected $signature = 'rockads:import-platforms';
    protected $description = 'Import ad platforms from RockAds API and store them in the database';

    public function handle(RockAds $rockAds)
    {
        try {
            $this->info('Checking RockAds API configuration...');
            Log::info('Starting RockAds ad platforms import');
            
            // Verify API credentials are configured
            if (empty(config('services.rockads.api_key')) || empty(config('services.rockads.api_secret'))) {
                $error = 'RockAds API credentials are not configured. Please check your .env file.';
                $this->error($error);
                Log::error($error);
                return 1;
            }

            $this->info('Fetching ad platforms from RockAds API...');
            $response = $rockAds->getAdPlatformsFromApi();
            
            if (empty($response->platforms)) {
                $this->warn('No ad platforms returned from API');
                Log::warning('RockAds API returned empty platforms list');
                return 1;
            }

            Log::info('Retrieved ad platforms from API', [
                'count' => count($response->platforms)
            ]);

            $this->info('Storing ad platforms in database...');
            $count = 0;
            foreach ($response->platforms as $platform) {
                Log::debug('Processing platform', [
                    'id' => $platform->id,
                    'name' => $platform->name
                ]);
                
                RockAdsAdPlatform::updateOrCreate(
                    ['platform_id' => $platform->id],
                    [
                        'name' => $platform->name,
                        'code' => $platform->getCode(),
                    ]
                );
                $count++;
            }

            $this->info("Successfully imported {$count} ad platforms");
            Log::info('Ad platforms import completed', ['count' => $count]);
            
        } catch (\Exception $e) {
            Log::error('Failed to import RockAds ad platforms', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Failed to import ad platforms: ' . $e->getMessage());
            return 1;
        }
    }
}
