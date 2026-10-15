<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reusing an invite link used to attach the same user again, so a pair can have several rows.
        // Keep the earliest row (lowest id) and delete the rest. The later rows only came from
        // replaying an invite, and lookups like OrganizationPolicy's ->first() already read the
        // earliest row, so effective roles don't change. Picking the highest role instead could
        // promote someone whose replayed invite carried a bigger role than their real membership.
        $duplicates = DB::table('organization_users')
            ->select('organization_id', 'user_id', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as row_count'))
            ->groupBy('organization_id', 'user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $pair) {
            DB::table('organization_users')
                ->where('organization_id', $pair->organization_id)
                ->where('user_id', $pair->user_id)
                ->where('id', '!=', $pair->keep_id)
                ->delete();
        }

        if ($duplicates->isNotEmpty()) {
            Log::info('Removed duplicate organization_users rows', [
                'pairs' => $duplicates->count(),
                'rows' => $duplicates->sum('row_count') - $duplicates->count(),
            ]);
        }

        Schema::table('organization_users', function (Blueprint $table) {
            $table->unique(['organization_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations. Deleted duplicate rows are not restored.
     */
    public function down(): void
    {
        $isMysql = in_array(DB::getDriverName(), ['mysql', 'mariadb']);

        Schema::table('organization_users', function (Blueprint $table) use ($isMysql) {
            // MySQL drops the foreign key's own index once the unique index covers organization_id,
            // so put one back or it refuses to drop the unique index
            if ($isMysql && ! Schema::hasIndex('organization_users', ['organization_id'])) {
                $table->index('organization_id', 'organization_users_organization_id_foreign');
            }

            $table->dropUnique(['organization_id', 'user_id']);
        });
    }
};
