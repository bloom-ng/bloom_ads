<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ad_account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 20, 2);
            $table->enum('type', ['deposit', 'withdrawal']);
            $table->decimal('vat', 8, 2);
            $table->decimal('service_fee', 8, 2);
            $table->decimal('total_amount', 20, 2); // amount + vat + service_fee
            $table->string('reference')->unique();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('description')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['ad_account_id', 'created_at']);
            $table->index('type');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_account_transactions');
    }
};
