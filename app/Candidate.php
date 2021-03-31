<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'name',
        'patter_lastname',
        'mother_lastname',
        'nickname',
        'birthplace',
        'date_birth',
        'address',
        'residence_time',
        'occupation',
        'elector_key',
        'postulate',
        'type_postulate',
        'party'
    ];
}
