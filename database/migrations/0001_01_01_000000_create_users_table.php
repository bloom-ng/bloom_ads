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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->enum('user_type', ['direct_advertiser', 'agency', 'partner']);
            $table->string('business_name')->nullable();
            $table->string('phone_country_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('weblink')->nullable();
            $table->string('country')->nullable();
            $table->string('oauth_id')->nullable();
            $table->string('oauth_provider')->nullable();
            $table->foreignId('current_organization_id')->nullable()->constrained('organizations')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();

            // Indexes for better query performance
            $table->index('email');
            $table->index(['oauth_id', 'oauth_provider']);
            $table->index('user_type');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};