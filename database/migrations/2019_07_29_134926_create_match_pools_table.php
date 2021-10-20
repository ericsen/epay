<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateMatchPoolsTable.
 */
class CreateMatchPoolsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('match_pools', function(Blueprint $table) {
			$table->bigIncrements('id')->comment('自動編號');
			$table->string('pool_name')->comment('撮合池編號');
			$table->string('pool_display_name')->comment('撮合池名稱');
			$table->enum('enable', ['on', 'off'])->default('on')->comment('啟用/停用');
			$table->text('note')->nullable()->default(null)->comment('備註');
			$table->timestamps();
			$table->softDeletes()->comment('軟刪除');
		});

		Schema::create('trader_pool', function (Blueprint $table) {
            $table->unsignedBigInteger('match_pool_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('match_pool_id')->references('id')->on('match_pools')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['match_pool_id', 'user_id']);
		});
		
		Schema::create('customer_pool', function (Blueprint $table) {
            $table->unsignedBigInteger('match_pool_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('match_pool_id')->references('id')->on('match_pools')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['match_pool_id', 'user_id']);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('match_pools');
		Schema::dropIfExists('trader_pool');
		Schema::dropIfExists('customer_pool');
	}
}
