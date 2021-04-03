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
            $table->string('re_election'); //si - no
            $table->integer('postulate');
            $table->integer('type_postulate');
            $table->string('indigenous_group'); //si - no
            $table->string('group_sexual_diversity'); //si - no
            $table->string('disabled_group'); //si - no

            $table->integer('number_line');
            $table->integer('number_list');
            $table->integer('circumscription')->nullable();
            $table->integer('locality')->nullable();
            $table->integer('demarcation');
            $table->integer('municipalities_council')->nullable();
            $table->string('campaign_slogan',500)->nullable();
            $table->integer('list_number')->nullable();
            $table->integer('campaign');
            $table->string('curp',18);
            $table->string('curp_confirmation',18);
            $table->string('rfc',13);
            $table->integer('phone_type');
            $table->integer('lada')->nullable();
            $table->string('phone');
            $table->string('extension')->nullable();
            $table->string('email');
            $table->string('email_confirmation');
            $table->decimal('total_annual_income',52,2);
            $table->decimal('salary_annual_income',52,2)->nullable();
            $table->decimal('financial_performances',52,2)->nullable();
            $table->decimal('annual_profit_professional_activity',52,2)->nullable();
            $table->decimal('annual_real_estate_lease_earnings',52,2)->nullable();
            $table->decimal('professional_services_fees',52,2)->nullable();
            $table->decimal('other_income',52,2)->nullable();
            $table->decimal('total_annual_expenses',52,2);
            $table->decimal('personal_expenses',52,2)->nullable();
            $table->decimal('real_estate_payments',52,2)->nullable();
            $table->decimal('debt_payments',52,2)->nullable();
            $table->decimal('loss_personal_activity',52,2)->nullable();
            $table->decimal('other_expenses',52,2)->nullable();
            $table->decimal('property',52,2)->nullable();
            $table->decimal('vehicles',52,2)->nullable();
            $table->decimal('other_movable_property',52,2)->nullable();
            $table->decimal('bank_accounts',52,2)->nullable();
            $table->decimal('other_assets',52,2)->nullable();
            $table->decimal('payment_debt_amount',52,2)->nullable();
            $table->decimal('other_passives',52,2)->nullable();
            $table->string('others')->nullable();
            $table->string('considerations')->nullable();
            $table->unsignedBigInteger('candidate_id')->nullable();

            $table->unsignedBigInteger('postulate_id')->nullable(); //si - no
            $table->unsignedBigInteger('politic_party_id');
            $table->unsignedBigInteger('candidate_ine_id')->nullable();
            $table->unsignedBigInteger('origin_candidate_id');

            $table->foreign('postulate_id')->references('id')->on('postulates');
            $table->foreign('politic_party_id')->references('id')->on('politic_parties');
            $table->foreign('candidate_ine_id')->references('id')->on('candidate_ines');
            $table->foreign('origin_candidate_id')->references('id')->on('candidates');
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
