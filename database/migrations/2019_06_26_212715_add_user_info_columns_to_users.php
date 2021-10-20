<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserInfoColumnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->after('remember_token')->comment('帳號暱稱');
            $table->enum('identity', ['B', 'T', 'A', 'C'])->default('T')->after('nickname')->comment('帳號身份(B：後端帳號，T：交易員帳號，A：代理商帳號，C：商戶帳號)');
            $table->float('total_deposit', 10, 2)->default(0)->after('enable')->comment('個人倉庫-存點數');
            $table->float('total_brokerage', 10, 2)->default(0)->after('total_deposit')->comment('個人錢包-存傭金');
            $table->integer('parent_id')->default(0)->unsigned()->after('total_brokerage')->comment('上線帳號id');
            $table->text('hierarchical_path')->nullable()->default(null)->after('parent_id')->comment('上線帳號id序列-族譜');
            $table->integer('hierarchical_level')->default(1)->after('hierarchical_path')->comment('階層階數(第幾代)');
            $table->integer('inspector_id')->unsigned()->nullable()->default(null)->after('hierarchical_level')->comment('後端審核人員編號，或後端帳號建檔人員');
            $table->dateTime('passed_at')->nullable()->default(null)->after('inspector_id')->comment('審核通過時間');

            $table->index('parent_id');
            $table->index('identity');
            $table->index('hierarchical_level');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nickname');
            $table->dropColumn('identity');
            $table->dropColumn('total_deposit');
            $table->dropColumn('total_brokerage');
            $table->dropColumn('parent_id');
            $table->dropColumn('hierarchical_path');
            $table->dropColumn('hierarchical_level');
            $table->dropColumn('inspector_id');
            $table->dropColumn('passed_at');
            $table->index('users_parent_id_index');
            $table->index('users_identity_index');
            $table->index('users_hierarchical_level_index');
            $table->index('users_inspector_index');
        });
    }
}
