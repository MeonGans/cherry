<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDesiredTeamIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('desired_team_id')->nullable()->after('team_id');
            $table->foreign('desired_team_id')->references('id')->on('teams')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['desired_team_id']);
            $table->dropColumn('desired_team_id');
        });
    }
}
