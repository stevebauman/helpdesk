<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Passwords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_folders', function(Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->boolean('locked')->default(false);
            $table->string('uuid');
            $table->string('pin');

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('passwords', function(Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->integer('folder_id')->unsigned();
            $table->string('name');
            $table->string('password')->nullable();

            $table->foreign('folder_id')->references('id')->on('password_folders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('passwords');
        Schema::dropIfExists('password_folders');
    }
}
