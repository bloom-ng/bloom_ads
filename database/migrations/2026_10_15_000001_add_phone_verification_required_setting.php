<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Off by default: switch to 1 in Admin Settings once Brevo SMS delivery is confirmed
        DB::table('admin_settings')->updateOrInsert(
            ['key' => 'phone_verification_required'],
            [
                'name' => 'Phone Verification Required (1/0)',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down()
    {
        DB::table('admin_settings')
            ->where('key', 'phone_verification_required')
            ->delete();
    }
};
