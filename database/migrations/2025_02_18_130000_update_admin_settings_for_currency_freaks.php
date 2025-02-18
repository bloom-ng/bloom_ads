<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateAdminSettingsForCurrencyFreaks extends Migration
{
    public function up()
    {
        // Add or update the currency rate settings with default values
        DB::table('admin_settings')->updateOrInsert(
            ['key' => 'usd_rate'],
            [
                'name' => 'USD RATE',
                'value' => '1800',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('admin_settings')->updateOrInsert(
            ['key' => 'gbp_rate'],
            [
                'name' => 'GBP RATE',
                'value' => '2300',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Add the currency margin setting if it doesn't exist
        DB::table('admin_settings')->updateOrInsert(
            ['key' => 'currency_margin'],
            [
                'name' => 'Currency Conversion Margin (NGN)',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down()
    {
        // Remove the currency margin setting
        DB::table('admin_settings')
            ->where('key', 'currency_margin')
            ->delete();

        // Reset rate settings to their original values
        DB::table('admin_settings')->updateOrInsert(
            ['key' => 'usd_rate'],
            [
                'name' => 'USD RATE',
                'value' => '1800',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('admin_settings')->updateOrInsert(
            ['key' => 'gbp_rate'],
            [
                'name' => 'GBP RATE',
                'value' => '2300',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
