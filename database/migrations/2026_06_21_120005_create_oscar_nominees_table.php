<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oscar_nominees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vote_id');
            $table->string('nomination', 40);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('vote_id')->references('id')->on('votes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['vote_id', 'nomination', 'user_id']);
            $table->index(['vote_id', 'nomination']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oscar_nominees');
    }
};
