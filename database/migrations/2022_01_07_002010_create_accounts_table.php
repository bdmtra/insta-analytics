<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\Account;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('username', 30);
            $table->string('fullname')->nullable();
            $table->text('biography')->nullable();
            $table->string('profile_pic_filename', 512)->nullable();
            $table->boolean('is_verified')->nullable();
            $table->integer('data_capture_status')->nullable()->default(Account::DATA_CAPTURE_STATUS_INACTIVE);
            $table->dateTime('data_captured_at')->nullable();
            $table->integer('posts_data_capture_status')->nullable()->default(Account::DATA_CAPTURE_STATUS_INACTIVE);
            $table->dateTime('posts_data_captured_at')->nullable();
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
        Schema::dropIfExists('accounts');
    }
}
