<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPointsToUserVotesTable extends Migration
{
    public function up()
    {
        Schema::table('user_votes', function (Blueprint $table) {
            $table->integer('points')->default(0);
        });
    }

    public function down()
    {
        Schema::table('user_votes', function (Blueprint $table) {
            $table->dropColumn('points');
        });
    }
}
