<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record_children', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('approved');
            $table->string('type');
            
            $table->timestamps();

            $table->integer('person_id')->unsigned();
            $table->integer('record_father_id')->unsigned();
            $table->integer('money_value_id')->unsigned();

            $table->foreign('person_id')->references('id')->on('persons');
            $table->foreign('record_father_id')->references('id')->on('record_father');
            $table->foreign('money_value_id')->references('id')->on('money_values');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('record_children');
    }
}
