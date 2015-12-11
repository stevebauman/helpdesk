<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGuideTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->boolean('published')->default(false);
            $table->timestamp('published_on')->nullable();
            $table->string('slug');
            $table->string('title');
            $table->text('description')->nullable();

            $table->unique(['title', 'slug']);
        });

        Schema::create('guide_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('guide_id')->unsigned();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('position')->default(1);

            $table->foreign('guide_id')->references('id')->on('guides');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guide_steps');
        Schema::dropIfExists('guides');
    }
}
