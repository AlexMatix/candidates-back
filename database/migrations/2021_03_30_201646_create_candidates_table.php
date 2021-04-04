<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('father_lastname')->nullable();;
            $table->string('mother_lastname')->nullable();;
            $table->string('name')->nullable();;
            $table->string('nickname')->nullable(); //CHICARCAS
            $table->string('roads')->nullable();; //AVENIDA  Posible select
            $table->string('roads_name')->nullable();; //LOS GIRASOLES
            $table->integer('outdoor_number')->nullable();; //2155
            $table->integer('interior_number')->nullable(); //A
            $table->string('neighborhood')->nullable();; //LA PAZ
            $table->string('zipcode')->nullable();; // 72000
            $table->string('municipality')->nullable();; //PUEBLA
            $table->string('elector_key')->nullable();; //RYGNHR94012221H700
            $table->string('ocr')->nullable();; //876076131459
            $table->string('cic')->nullable();; //37078740
            $table->integer('emission')->nullable();; //2015
            $table->string('entity')->nullable();; //PUEBLA
            $table->string('section')->nullable();; //1025
            $table->date('date_birth')->nullable();; //15/11/1999
            $table->string('gender')->nullable();; // HOMBRE -- MUJER
            $table->string('birthplace')->nullable();; //OAXACA
            $table->integer('residence_time_year')->nullable();; //2
            $table->integer('residence_time_month')->nullable();; //1
            $table->string('occupation')->nullable();; //EMPLEADA
            $table->string('re_election')->nullable();; //si - no
            $table->integer('postulate')->nullable();;
            $table->integer('type_postulate')->nullable();;
            $table->string('indigenous_group')->nullable();; //si - no
            $table->string('group_sexual_diversity')->nullable();; //si - no
            $table->string('disabled_group')->nullable();; //si - no
            $table->boolean('ine_check')->nullable();
            $table->unsignedBigInteger('postulate_id')->nullable(); //si - no
            $table->unsignedBigInteger('politic_party_id'); //si - no
            $table->unsignedBigInteger('candidate_id')->nullable(); //si - no

            $table->timestamps();

            $table->foreign('postulate_id')->references('id')->on('postulates');
            $table->foreign('candidate_id')->references('id')->on('candidates');
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
        Schema::dropIfExists('candidates');
    }
}
