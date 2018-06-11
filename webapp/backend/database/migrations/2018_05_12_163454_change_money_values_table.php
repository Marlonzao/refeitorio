<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMoneyValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('record_children', function (Blueprint $table) {
            $table->dropForeign(['money_value_id']);
            $table->dropColumn('money_value_id');
            $table->dropColumn('type');
        });

        Schema::rename('money_values', 'payment_types');

        Schema::table('payment_types', function (Blueprint $table) {
            $table->dropColumn('active');
            $table->string('type');

            $table->integer('enviroment_id')->unsigned();
            $table->foreign('enviroment_id')->references('id')->on('enviroments');
        });

        Schema::table('record_children', function (Blueprint $table) {
            $table->integer('payment_type_id')->unsigned();
            $table->foreign('payment_type_id')->references('id')->on('payment_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
