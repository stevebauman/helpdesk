<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class Passwords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_folders', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->boolean('locked')->default(true);
            $table->string('uuid');
            $table->string('pin');

            $table->foreign('user_id')->references('id')->on('users');

            // Only allow one password folder per user.
            $table->unique(['user_id']);
        });

        Schema::create('passwords', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('folder_id')->unsigned();
            $table->string('title');
            $table->string('website')->nullable();
            $table->string('username')->nullable();
            $table->string('password');
            $table->text('notes')->nullable();

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
