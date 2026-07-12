<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vote_photos', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('vote_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('original_image_path')->nullable()->after('image_path');
            $table->boolean('is_finalist')->default(true)->after('original_image_path');
            $table->timestamp('finalist_selected_at')->nullable()->after('is_finalist');

            $table->unique(['vote_id', 'user_id']);
            $table->index(['vote_id', 'is_finalist']);
        });
    }

    public function down(): void
    {
        Schema::table('vote_photos', function (Blueprint $table) {
            $table->dropUnique(['vote_id', 'user_id']);
            $table->dropIndex(['vote_id', 'is_finalist']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn([
                'original_image_path',
                'is_finalist',
                'finalist_selected_at',
            ]);
        });
    }
};
