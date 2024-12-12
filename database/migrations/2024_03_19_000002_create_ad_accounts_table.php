<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('timezone');
            $table->string('currency');
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->enum('status', ['processing', 'pending', 'approved', 'banned', 'deleted', 'rejected'])
                ->default('processing');
            $table->string('business_manager_id', 32)->nullable();
            $table->string('landing_page')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_accounts');
    }
}; 