<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rock_ads_timezones', function (Blueprint $table) {
            $table->id();
            $table->string('timezone_id')->unique(); // the 'key' from RockAds
            $table->string('name');
            $table->string('offset_str');
            $table->integer('offset');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rock_ads_timezones');
    }
};
