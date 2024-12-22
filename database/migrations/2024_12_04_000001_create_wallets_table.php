<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->enum('currency', ['NGN', 'USD', 'GBP']);
            $table->timestamps();

            // Each organization can only have one wallet per currency
            $table->unique(['organization_id', 'currency']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallets');
    }
};
