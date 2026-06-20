<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wednesday_quest_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('victim_code')->unique();
            $table->text('hint_2');
            $table->text('hint_3');
            $table->text('hint_4');
            $table->text('hint_5');
            $table->text('hint_6');
            $table->text('hint_7');
            $table->text('hint_8');
            $table->text('hint_9');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wednesday_quest_routes');
    }
};
