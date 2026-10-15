<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // MODIFY ... ENUM is MySQL-only; skip on the SQLite test database
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Drop the existing enum constraint
            DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM('credit', 'debit', 'processing_fee', 'vat', 'service_charge')");
        });
    }

    public function down()
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Revert back to original enum values
            DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM('credit', 'debit')");
        });
    }
};
