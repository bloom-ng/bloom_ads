<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add USD API rate
        DB::table('admin_settings')->updateOrInsert(
            ['key' => 'usd_api_rate'],
            [
                'name' => 'USD API RATE',
                'value' => '1498.96', // Current API rate we saw earlier
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Add GBP API rate
        DB::table('admin_settings')->updateOrInsert(
            ['key' => 'gbp_api_rate'],
            [
                'name' => 'GBP API RATE',
                'value' => '1899.18', // Current API rate we saw earlier
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down()
    {
        DB::table('admin_settings')
            ->whereIn('key', ['usd_api_rate', 'gbp_api_rate'])
            ->delete();
    }
};
