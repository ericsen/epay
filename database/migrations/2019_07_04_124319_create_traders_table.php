<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateTradersTable.
 */
class CreateTradersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Schema::disableForeignKeyConstraints();

        Schema::create('traders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unique()->unsigned()->comment('帳號編號');
            $table->string('contact_person')->nullable()->default(null)->comment('連絡人');
            $table->string('contact_phone')->nullable()->default(null)->comment('連絡電話');
            $table->text('note')->nullable()->default(null)->comment('註解');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade');

        });
        // Schema::enableForeignKeyConstraints();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('traders');
    }
}
