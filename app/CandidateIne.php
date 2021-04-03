<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CandidateIne extends Model
{
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
        'postulate_id',
        'number'
    ];
}
