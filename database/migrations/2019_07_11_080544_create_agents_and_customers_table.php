<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentsAndCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unique()->unsigned()->comment('帳號編號');
            $table->string('contact_person')->nullable()->default(null)->comment('連絡人');
            $table->string('contact_phone')->nullable()->default(null)->comment('連絡電話');
            $table->text('note')->nullable()->default(null)->comment('註解');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade');
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unique()->unsigned()->comment('帳號編號');
            $table->string('contact_person')->nullable()->default(null)->comment('連絡人');
            $table->string('contact_phone')->nullable()->default(null)->comment('連絡電話');
            $table->string('company_name')->nullable()->default(null)->comment('公司名稱');
            $table->integer('total_amount_limit')->default(0)->comment('商戶每日交易最大上限，0為不限制');
            $table->text('note')->nullable()->default(null)->comment('註解');
            $table->text('api_secret_key')->nullable()->default(null)->comment('API 介接 Secret Key');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agents');
        Schema::dropIfExists('customers');
    }
}
