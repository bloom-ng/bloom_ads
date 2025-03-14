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
        DB::table('admin_settings')->where('key', 'notification_email')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('admin_settings')->insert([
            'name' => 'Notification Email',
            'key' => 'notification_email',
            'value' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
