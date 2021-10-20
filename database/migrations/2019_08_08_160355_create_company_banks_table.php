<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCompanyBanksTable.
 */
class CreateCompanyBanksTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_banks', function(Blueprint $table) {
            $table->bigIncrements('id')->comment('自動編號');
			$table->string('bank_name')->comment('銀行名稱');
			$table->string('bank_branch')->comment('分行');
			$table->string('bank_account_name')->comment('帳戶名稱');
			$table->string('bank_account_number')->comment('帳戶號碼');
			$table->string('bank_2fa_password')->comment('管理密碼')->nullable()->default(null);
			$table->enum('enable', ['on', 'off'])->default('on')->comment('啟用/停用');
			$table->timestamps();
			$table->softDeletes()->comment('軟刪除');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('company_banks');
	}
}
