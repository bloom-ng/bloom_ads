<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 20, 2);
            $table->enum('currency', ['NGN', 'USD', 'GBP']);
            $table->enum('type', ['credit', 'debit']);
            $table->string('description')->nullable();
            $table->string('reference')->unique();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['wallet_id', 'created_at']);
            $table->index('type');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
