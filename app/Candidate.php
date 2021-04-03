<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{

    const DIPUTACION_RP= 1;
    const DIPUTACION_MR = 2;
    const REGIDURIA = 3;
    const SINDICATURA = 4;
    const PRESIDENCIA = 5;

    const OWNER = 1;
    const ALTERNATE = 2;


    protected $fillable = [
        'id',
        'father_lastname',
        'mother_lastname',
        'name',
        'nickname',
        'roads',
        'roads_name',
        'outdoor_number',
        'interior_number',
        'neighborhood',
        'zipcode',
        'municipality',
        'elector_key',
        'ocr',
        'cic',
        'emission',
        'entity',
        'section',
        'date_birth',
        'gender',
        'birthplace',
        'residence_time_year',
        'residence_time_month',
        'occupation',
        're-election',
        'postulate',
        'type_postulate',
        'indigenous_group',
        'group_sexual_diversity',
        'disabled_group',
        'politic_party_id',
        'postulate_id',
        'ine_check'
    ];

    public function postulate(){
        return $this->belongsTo(Postulate::class);
    }
}
