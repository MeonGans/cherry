<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('session_id')->constrained('sessions'); // Assuming the related table is named 'sessions'
            $table->string('phone_number');
            $table->date('date_of_birth');
            $table->foreignId('liceum_id')->constrained('liceums'); // Assuming the related table is named 'liceums'
            $table->foreignId('team_id')->nullable()->constrained('teams'); // Assuming the related table is named 'teams'
            $table->enum('gender', ['male', 'female']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
