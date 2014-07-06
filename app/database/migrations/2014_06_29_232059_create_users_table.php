<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('users', function($table) {
            $table->create();
            $table->increments('id');
            $table->string('pid');
            $table->string('uid');
            $table->string('reddit_username')->nullable();
            $table->boolean('active')->default(false);
            $table->string('current_subreddit')->default('home');
            $table->string('theme');
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('remember_token')->nullable();
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
		Schema::drop('users');
	}

}
