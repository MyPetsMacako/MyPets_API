<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->boolean('isBanned');
            $table->string('fullName');
            $table->string('nickname');
            $table->string('email');
            $table->string('password');
            $table->string('tel_number')->nullable();
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
