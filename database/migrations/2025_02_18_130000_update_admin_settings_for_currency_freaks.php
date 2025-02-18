<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateAdminSettingsForCurrencyFreaks extends Migration
{
    public function up()
    {
        // Remove any existing manual rate settings
        DB::table('admin_settings')
            ->whereIn('key', ['usd_rate', 'gbp_rate', 'ngn_rate'])
            ->delete();

        // Add the currency margin setting if it doesn't exist
        if (!DB::table('admin_settings')->where('key', 'currency_margin')->exists()) {
            DB::table('admin_settings')->insert([
                'key' => 'currency_margin',
                'name' => 'Currency Conversion Margin (NGN)',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        // Remove the currency margin setting
        DB::table('admin_settings')
            ->where('key', 'currency_margin')
            ->delete();

        // Re-add default rate settings
        DB::table('admin_settings')->insert([
            [
                'key' => 'usd_rate',
                'name' => 'USD RATE',
                'value' => '1530',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'gbp_rate',
                'name' => 'GBP RATE',
                'value' => '1930',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'ngn_rate',
                'name' => 'NGN RATE',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
