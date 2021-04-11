<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class Candidate extends Model
{

    const DIPUTACION_RP = 1;//1
    const DIPUTACION_MR = 2;//1
    const REGIDURIA = 3;//2
    const SINDICATURA = 4;//2
    const PRESIDENCIA = 5; //1
    const TOTAL_DIPUTACION_RP = 15;
    const TOTAL_DIPUTACION_MR = 26;

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
        're_election',
        'postulate',
        'type_postulate',
        'indigenous_group',
        'group_sexual_diversity',
        'disabled_group',
        'politic_party_id',
        'postulate_id',
        'candidate_id',
        'ine_check',
        'user_id'
    ];

    public function postulate()
    {
        return $this->belongsTo(Postulate::class);
    }

    public function postulate_data()
    {
        return $this->belongsTo(Postulate::class, 'postulate_id');
    }

    public function owner()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function alternate()
    {
        return $this->hasOne(Candidate::class);
    }

    public function copyCandidateIne()
    {
        return $this->hasOne(CandidateIne::class, 'origin_candidate_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeGetOwner($query)
    {
        return $query->where('type_postulate', Self::OWNER)->orderBy('politic_party_id')->orderBy('created_at');
    }

    public function scopeSearchInFields($query, $column, $value)
    {
        return $query->orWhere($column, 'like', '%' . $value . '%');
    }

    public function scopeSkipFields($query, $text, $integer)
    {
        $special_fields = [
            'id',
            'emission',
            'date_birth',
            'residence_time_year',
            'residence_time_month',
            'postulate',
            'type_postulate',
            'ine_check',
            'user_id',
            'postulate_id',
            'politic_party_id',
            'candidate_id'
        ];
        $fields = array_diff($this->getFillable(), $special_fields);
        foreach ($fields as $field) {
            $query->where(function ($q) use ($field, $text, $integer) {
                if ($field == 'roads') {
                    $q->where($field, '<>', $integer)
                        ->orWhereNull($field);
                } else {
                    $q->where($field, '<>', $text)
                        ->orWhereNull($field);
                }
            });
        }

        return $query;
    }

    public function scopeGetByUser($query, $user_id){
        return $query->where('user_id', $user_id);
    }

    public function scopeGetByType($query, $types){
        return $query->where('postulate', $types);
    }

    public function scopeGetByMunicipality($query, $postulate_id){
        return $query->where('postulate_id', $postulate_id);
    }
}
