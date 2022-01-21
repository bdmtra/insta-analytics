<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_posts', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id');
            $table->string('shortcode');
            $table->string('type')->nullable();
            $table->string('link')->nullable();
            $table->json('mentions')->nullable();
            $table->json('hashtags')->nullable();
            $table->string('caption')->nullable();
            $table->dateTime('external_created_at')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_posts');
    }
}
