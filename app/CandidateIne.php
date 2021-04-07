<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CandidateIne extends Model
{
    const OWNER = 1;
    const ALTERNATE = 2;

    const DIPUTACION_RP= 1; //8
    const DIPUTACION_MR = 2; //7
    const REGIDURIA = 3; // 28
    const SINDICATURA = 4; // 26
    const PRESIDENCIA = 5; // 9

    protected $fillable = [
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
        'postulate_id',
        'politic_party_id',
        'candidate_id',
        'origin_candidate_id',
        'candidate_ine_id',

        'number_line',
        'number_list',
        'circumscription',
        'locality',
        'demarcation',
        'municipalities_council',
        'campaign_slogan',
        'list_number',
        'campaign',
        'curp',
        'curp_confirmation',
        'rfc',
        'phone_type',
        'lada',
        'phone',
        'extension',
        'email',
        'email_confirmation',
        'total_annual_income',
        'salary_annual_income',
        'financial_performances',
        'annual_profit_professional_activity',
        'annual_real_estate_lease_earnings',
        'annual_real_estate_lease_earnings',
        'professional_services_fees',
        'other_income',
        'total_annual_expenses',
        'personal_expenses',
        'real_estate_payments',
        'debt_payments',
        'loss_personal_activity',
        'other_expenses',
        'property',
        'vehicles',
        'other_movable_property',
        'bank_accounts',
        'other_assets',
        'payment_debt_amount',
        'other_passives',
        'others',
        'considerations'
    ];

    public function postulate(){
        return $this->belongsTo(Postulate::class);
    }

    public function politicParty(){
        return $this->belongsTo(PoliticParty::class);
    }

    public function owner(){
        return $this->belongsTo(CandidateIne::class,'candidate_ine_id');
    }

    public function alternate(){
        return $this->hasOne(CandidateIne::class);
    }

    public function originCandidate(){
        return $this->belongsTo(Candidate::class);
    }

    public function scopeGetOwner($query){
        return $query->where('type_postulate',Self::OWNER)->orderBy('politic_party_id')->orderBy('created_at');
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
}
