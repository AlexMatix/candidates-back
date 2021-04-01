<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostulatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postulates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('district');
            $table->string('municipality');
            $table->bigIncrements('total_population');
            $table->bigIncrements('regidurias');
            $table->bigIncrements('sindicaturas');
            $table->bigIncrements('tatal');
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
        Schema::dropIfExists('postulates');
    }
}
