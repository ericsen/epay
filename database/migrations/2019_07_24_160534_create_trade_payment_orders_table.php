<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateTradePaymentOrdersTable.
 */
class CreateTradePaymentOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_payment_orders', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自動編號');
            $table->bigInteger('user_id')->unsigned()->comment('users.id');
            $table->string('user_name')->comment('付款帳號');
            $table->string('user_nickname')->comment('付款帳號暱稱');
            $table->bigInteger('trader_id')->unsigned()->comment('traders.id');
            $table->string('payment_order_sn')->comment('平台交易編號');
            $table->integer('amount')->default(0)->comment('儲值金額');
            $table->string('bank_name')->comment('付款銀行名稱');
            $table->string('bank_branch')->comment('付款銀行支行');
            $table->string('bank_account_name')->comment('付款持卡人');
            $table->string('bank_account_number')->comment('付款銀行帳號');
            $table->string('to_bank_name')->comment('收款銀行名稱');
            $table->string('to_bank_branch')->comment('收款銀行支行');
            $table->string('to_bank_account_name')->comment('收款持卡人');
            $table->string('to_bank_account_number')->comment('收款銀行帳號');
            $table->string('bank_order_sn')->comment('銀行匯款交易單號');
            $table->string('bank_slip')->comment('銀行匯款單收據(上傳圖片)');
            $table->bigInteger('inspector_id')->unsigned()->nullable()->default(null)->comment('後台交易驗證人員 users.id');
            $table->enum('status', ['success', 'pending', 'fail'])->default('pending')->comment('儲值單狀態');
            $table->dateTime('checked_at')->nullable()->default(null)->comment('審核通過時間');
            $table->text('note')->nullable()->default(null)->comment('交易備註');
            $table->timestamps();

            $table->index('user_id');
            $table->index('trader_id');
            $table->index('payment_order_sn');
            $table->index('inspector_id');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('trader_id')->references('id')->on('traders');
            $table->foreign('inspector_id')->references('id')->on('users');
        });
        DB::statement("ALTER TABLE trade_payment_orders COMMENT '交易員儲值記錄'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trade_payment_orders');
    }
}
