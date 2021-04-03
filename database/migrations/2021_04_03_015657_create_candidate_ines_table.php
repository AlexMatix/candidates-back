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

            $table->string('number_line');
            $table->string('circumscription');
            $table->string('locality');
            $table->string('demarcation');
            $table->string('municipalities_council');
            $table->string('campaign_slogan');
            $table->string('list_number');
            $table->string('campaign');
            $table->string('curp');
            $table->string('curp_confirmation');
            $table->string('rfc');
            $table->string('phone_type');
            $table->string('lada');
            $table->string('phone');
            $table->string('extension');
            $table->string('email');
            $table->string('email_confirmation');
            $table->string('total_annual_income');
            $table->string('salary_annual_income');
            $table->string('financial_performances');
            $table->string('annual_profit_professional_activity');
            $table->string('annual_real_estate_lease_earnings');
            $table->string('professional_services_fees');
            $table->string('other_income');
            $table->string('total_annual_expenses');
            $table->string('personal_expenses');
            $table->string('real_estate_payments');
            $table->string('debt_payments');
            $table->string('loss_personal_activity');
            $table->string('other_expenses');
            $table->string('property');
            $table->string('vehicles');
            $table->string('other_movable_property');
            $table->string('bank_accounts');
            $table->string('other_assets');
            $table->string('payment_debt_amount');
            $table->string('other_passives');
            $table->string('others');
            $table->string('considerations');

            $table->unsignedBigInteger('postulate_id')->nullable(); //si - no
            $table->unsignedBigInteger('politic_party_id');

            $table->foreign('postulate_id')->references('id')->on('postulates');
            $table->foreign('politic_party_id')->references('id')->on('politic_parties');
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
        Schema::dropIfExists('candidate_ines');
    }
}
