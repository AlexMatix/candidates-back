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
            $table->string('name');
            $table->string('patter_lastname');
            $table->string('mother_lastname');
            $table->string('nickname');
            $table->string('birthplace');
            $table->date('date_birth');
            $table->string('address');
            $table->string('residence_time');
            $table->string('occupation');
            $table->string('elector_key');
            $table->string('postulate');
            $table->string('type_postulate');
            $table->integer('party');

            $table->timestamps();
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
