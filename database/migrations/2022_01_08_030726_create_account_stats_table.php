<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->references('id')->on('accounts');
            $table->integer('following_count');
            $table->integer('followers_count');
            $table->integer('uploads_count');
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
        Schema::dropIfExists('account_stats');
    }
}
