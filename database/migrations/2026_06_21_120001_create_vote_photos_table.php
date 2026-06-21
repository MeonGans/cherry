<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vote_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vote_id');
            $table->string('title')->nullable();
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('vote_id')->references('id')->on('votes')->onDelete('cascade');
            $table->index(['vote_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_photos');
    }
};
