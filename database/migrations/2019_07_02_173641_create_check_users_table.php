<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned()->nullable()->default(null)->comment('帳號編號');
            $table->enum('type', ['weixin', 'alipay'])->default('weixin')->comment('帳號驗證方式');
            $table->text('qrcode_data')->comment('qrcode 掃碼資料');
            $table->text('qrcode_nickname')->comment('qrcode 掃碼資料上的暱稱');
            $table->enum('is_checked', ['yes', 'no'])->default('no')->comment('是否通過驗證');
            $table->integer('inspector_id')->unsigned()->nullable()->default(null)->comment('後端審核人員編號');
            $table->dateTime('checked_at')->nullable()->default(null)->comment('審核通過時間');
            $table->timestamps();

            $table->index('user_id');
            $table->index('inspector_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_users');
    }
}
