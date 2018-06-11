<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('registry');
            $table->boolean('isBoarder');
            $table->boolean('filed');

            $table->timestamps();
            $table->softDeletes();

            $table->integer('school_id')->unsigned();
            $table->integer('person_id')->unsigned();

            $table->foreign('school_id')->references('id')->on('enviroments');
            $table->foreign('person_id')->references('id')->on('persons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
