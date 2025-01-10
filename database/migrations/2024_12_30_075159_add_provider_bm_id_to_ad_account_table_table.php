<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('ad_accounts', 'provider_bm_id')) {
            Schema::table('ad_accounts', function (Blueprint $table) {
                $table->string('provider_bm_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ad_accounts', function (Blueprint $table) {
            //
        });
    }
};
