<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUseForColumnToRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->enum('use_for', ['B', 'T', 'A', 'C'])->default('B')->after('name')->comment('帳號身份(B：後端帳號，T：交易員帳號，A：代理商帳號，C：商戶帳號)');

            $table->index('use_for');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('use_for');
            $table->index('roles_use_for_index');
        });
    }
}
