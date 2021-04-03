<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postulate extends Model
{
    protected $fillable = [
        'id',
        'district',
        'municipality_key',
        'municipality',
        'total_population',
        'presidency',
        'regidurias',
        'sindicaturas',
        'total',
    ];

    public function candidate(){
        return $this->hasMany(Candidate::class);
    }
}
