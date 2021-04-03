<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PoliticParty extends Model
{
    protected $fillable = [
      'id',
      'name'
    ];

    public $timestamps = false;
}
