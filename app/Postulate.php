<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postulate extends Model
{
    protected $fillable = [
        'id',
        'district',
        'municipality',
        'total_population',
        'regidurias',
        'sindicaturas',
        'tatal',
    ];
}
