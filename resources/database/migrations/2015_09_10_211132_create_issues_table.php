<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('user_id')->unsigned();
            $table->boolean('closed')->default(false);
            $table->string('title');
            $table->text('description');

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('issue_labels', function (Blueprint $table) {
            $table->integer('issue_id')->unsigned();
            $table->integer('label_id')->unsigned();

            $table->foreign('issue_id')->references('id')->on('issues');
            $table->foreign('label_id')->references('id')->on('labels');
        });

        Schema::create('issue_comments', function (Blueprint $table) {
            $table->integer('issue_id')->unsigned();
            $table->integer('comment_id')->unsigned();
            $table->boolean('resolution')->default(false);

            $table->foreign('issue_id')->references('id')->on('issues');
            $table->foreign('comment_id')->references('id')->on('comments');

            // Make sure only one resolution can be made per issue
            $table->unique(['issue_id', 'comment_id', 'resolution']);
        });

        Schema::create('issue_users', function (Blueprint $table) {
            $table->integer('issue_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('issue_id')->references('id')->on('issues');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('issue_users');
        Schema::dropIfExists('issue_comments');
        Schema::dropIfExists('issue_labels');
        Schema::dropIfExists('issues');
    }
}
