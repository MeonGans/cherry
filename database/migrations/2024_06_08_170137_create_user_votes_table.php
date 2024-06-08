<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserVotesTable extends Migration
{
    public function up()
    {
        Schema::create('user_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vote_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('team_id');
            $table->timestamps();

            $table->foreign('vote_id')->references('id')->on('votes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');

            $table->unique(['vote_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_votes');
    }
}
