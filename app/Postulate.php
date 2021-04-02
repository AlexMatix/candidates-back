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
        'presidency',
        'regidurias',
        'sindicaturas',

        'presidency_owner_count',
        'regidurias_owner_count',
        'sindicaturas_owner_count',
        'diputacion_owner_count',

        'presidency_alternate_count',
        'regidurias_alternate_count',
        'sindicaturas_alternate_count',
        'diputacion_alternate_count',

        'total',
    ];

    public function candidate(){
        return $this->hasMany(Candidate::class);
    }
}
