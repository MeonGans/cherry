<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photo_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vote_id');
            $table->unsignedBigInteger('vote_photo_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('source', 20)->default('user');
            $table->integer('points')->default(1);
            $table->timestamps();

            $table->foreign('vote_id')->references('id')->on('votes')->onDelete('cascade');
            $table->foreign('vote_photo_id')->references('id')->on('vote_photos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['vote_id', 'user_id', 'vote_photo_id']);
            $table->index(['vote_id', 'vote_photo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_votes');
    }
};
