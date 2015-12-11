<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateComputerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('software', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('version')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('operating_systems', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('version')->nullable();
            $table->string('service_pack')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('computer_types', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('name');

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('computers', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('type_id')->unsigned()->nullable();
            $table->integer('os_id')->unsigned()->nullable();
            $table->string('dn')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('model')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('type_id')->references('id')->on('computer_types');
            $table->foreign('os_id')->references('id')->on('operating_systems');
        });

        Schema::create('computer_access', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('computer_id')->unsigned()->nullable();
            $table->boolean('active_directory')->default(false);
            $table->boolean('wmi')->default(false);
            $table->string('wmi_username')->nullable();
            $table->string('wmi_password')->nullable();

            $table->foreign('computer_id')->references('id')->on('computers');
        });

        Schema::create('computer_users', function (Blueprint $table) {
            $table->integer('computer_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('computer_id')->references('id')->on('computers');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('computer_software', function (Blueprint $table) {
            $table->integer('computer_id')->unsigned();
            $table->integer('software_id')->unsigned();

            $table->foreign('computer_id')->references('id')->on('computers');
            $table->foreign('software_id')->references('id')->on('software');
        });

        Schema::create('computer_processors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('computer_id')->unsigned();
            $table->string('name');
            $table->string('family')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('speed')->nullable();

            $table->foreign('computer_id')->references('id')->on('computers');
        });

        Schema::create('computer_processor_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('processor_id')->unsigned();
            $table->integer('load');
            $table->string('status')->nullable();

            $table->foreign('processor_id')->references('id')->on('computer_processors');
        });

        Schema::create('computer_hard_disks', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('computer_id')->unsigned();
            $table->string('name');
            $table->double('size');
            $table->dateTime('installed')->nullable();
            $table->string('description')->nullable();

            $table->foreign('computer_id')->references('id')->on('computers');
        });

        Schema::create('computer_hard_disk_records', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('disk_id')->unsigned();
            $table->double('free');
            $table->string('status')->nullable();

            $table->foreign('disk_id')->references('id')->on('computer_hard_disks');
        });

        Schema::create('computer_status_records', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('computer_id')->unsigned();
            $table->boolean('online')->default(false);
            $table->integer('latency')->nullable();

            $table->foreign('computer_id')->references('id')->on('computers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('computer_status_records');
        Schema::dropIfExists('computer_hard_disk_records');
        Schema::dropIfExists('computer_hard_disks');
        Schema::dropIfExists('computer_processor_records');
        Schema::dropIfExists('computer_processors');
        Schema::dropIfExists('computer_users');
        Schema::dropIfExists('computer_software');
        Schema::dropIfExists('computer_access');
        Schema::dropIfExists('computers');
        Schema::dropIfExists('computer_types');
        Schema::dropIfExists('operating_systems');
        Schema::dropIfExists('software');
    }
}
