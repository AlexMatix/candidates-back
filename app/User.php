<?php

namespace App;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, Authorizable;

    const ADMIN = 1;
    const CAP = 2;

    const MORENA = 1;
    const PT = 2;
    const VERDE = 3;
    const PSI = 4;
    const MORENA_PT = 5;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'type',
        'politic_party_id',
        'configuration',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'configuration' => 'array',
    ];


    public function politic_party()
    {
        return $this->hasOne(PoliticParty::class);
    }
}
