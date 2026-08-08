<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->timestamp('stopped_at')->nullable()->after('session_id');
        });

        $inactiveSessionIds = DB::table('sessions')
            ->where('active', false)
            ->pluck('id');

        if ($inactiveSessionIds->isNotEmpty()) {
            DB::table('votes')
                ->whereIn('session_id', $inactiveSessionIds)
                ->whereNull('stopped_at')
                ->update(['stopped_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropColumn('stopped_at');
        });
    }
};
