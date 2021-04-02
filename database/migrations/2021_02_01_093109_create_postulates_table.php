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
            $table->string('total_population');
            $table->integer('presidency');
            $table->integer('regidurias');
            $table->integer('sindicaturas');

            $table->integer('presidency_owner_count')->default(0);
            $table->integer('regidurias_owner_count')->default(0);
            $table->integer('sindicaturas_owner_count')->default(0);
            $table->integer('diputacion_owner_count')->default(0);

            $table->integer('presidency_alternate_count')->default(0);
            $table->integer('regidurias_alternate_count')->default(0);
            $table->integer('sindicaturas_alternate_count')->default(0);
            $table->integer('diputacion_alternate_count')->default(0);

            $table->integer('total');
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
