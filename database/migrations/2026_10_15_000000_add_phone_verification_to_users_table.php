<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // E.164 number, only set once verified. Unique so a number can back one account.
            $table->string('verified_phone', 20)->nullable()->unique()->after('phone');
            $table->timestamp('phone_verified_at')->nullable()->after('verified_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['verified_phone']);
            $table->dropColumn(['verified_phone', 'phone_verified_at']);
        });
    }
};
