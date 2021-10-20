<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletesAndEnableToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('enable', ['on','off'])->default('off')->after('remember_token')->comment('帳號啟用狀態');
            $table->text('note')->nullable()->default(null)->after('enable')->comment('帳號說明');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('enable');
            $table->dropColumn('note');
            $table->dropColumn('deleted_at');
        });
    }
}
