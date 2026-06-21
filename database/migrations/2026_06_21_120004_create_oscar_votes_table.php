<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oscar_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vote_id');
            $table->string('nomination', 40);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('nominee_user_id');
            $table->integer('points')->default(1);
            $table->timestamps();

            $table->foreign('vote_id')->references('id')->on('votes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('nominee_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['vote_id', 'nomination', 'user_id', 'nominee_user_id'], 'oscar_unique_vote');
            $table->index(['vote_id', 'nomination']);
            $table->index(['vote_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oscar_votes');
    }
};
