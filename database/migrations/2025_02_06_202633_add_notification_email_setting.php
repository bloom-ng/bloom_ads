<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert the notification email setting
        DB::table('admin_settings')->insert([
            'name' => 'Notification Email',
            'key' => 'notification_email',
            'value' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the notification email setting
        DB::table('admin_settings')->where('key', 'notification_email')->delete();
    }
};
