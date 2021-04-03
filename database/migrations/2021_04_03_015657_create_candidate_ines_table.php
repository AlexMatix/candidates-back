<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidateInesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_ines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('father_lastname');
            $table->string('mother_lastname');
            $table->string('name');
            $table->string('nickname')->nullable(); //CHICARCAS
            $table->string('roads'); //AVENIDA  Posible select
            $table->string('roads_name'); //LOS GIRASOLES
            $table->integer('outdoor_number'); //2155
            $table->integer('interior_number')->nullable(); //A
            $table->string('neighborhood'); //LA PAZ
            $table->string('zipcode'); // 72000
            $table->string('elector_key'); //RYGNHR94012221H700
            $table->string('ocr'); //876076131459
            $table->string('cic'); //37078740
            $table->integer('emission'); //2015
            $table->string('entity'); //PUEBLA
            $table->string('section'); //1025
            $table->date('date_birth'); //15/11/1999
            $table->string('gender'); // HOMBRE -- MUJER
            $table->string('birthplace'); //OAXACA
            $table->integer('residence_time_year'); //2
            $table->integer('residence_time_month'); //1
            $table->string('occupation'); //EMPLEADA
            $table->string('re-election'); //si - no
            $table->integer('postulate');
            $table->integer('type_postulate');
            $table->string('indigenous_group'); //si - no
            $table->string('group_sexual_diversity'); //si - no
            $table->string('disabled_group'); //si - no
            $table->integer('number');
            $table->unsignedBigInteger('postulate_id')->nullable(true); //si - no
            $table->unsignedBigInteger('politic_party_id');

            $table->timestamps();

            $table->foreign('postulate_id')->references('id')->on('postulates');
            $table->foreign('politic_party_id')->references('id')->on('politic_parties');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_ines');
    }
}
