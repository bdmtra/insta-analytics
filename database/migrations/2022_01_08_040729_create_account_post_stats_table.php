<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPostStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_post_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('account_post_id')->references('id')->on('account_posts');
            $table->integer('likes_count');
            $table->integer('comments_count');
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
        Schema::dropIfExists('account_post_stats');
    }
}
