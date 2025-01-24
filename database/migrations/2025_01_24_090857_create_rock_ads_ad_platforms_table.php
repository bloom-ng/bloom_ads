<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rock_ads_ad_platforms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('platform_id')->unique(); // the 'id' from RockAds
            $table->string('name');
            $table->string('code'); // meta, snapchat, tiktok, google
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rock_ads_ad_platforms');
    }
};
