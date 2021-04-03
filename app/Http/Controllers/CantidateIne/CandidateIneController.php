<?php

namespace App\Http\Controllers\CantidateIne;

use App\Candidate;
use App\CandidateIne;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CandidateIneController extends ApiController
{
    public function index()
    {
        return $this->showAll(CandidateIne::all());
    }

    public function store(Request $request)
    {
        $rules = [
            "origin_candidate_id" => "required",
            "number_line" => "required",
            "circumscription" => "required",
            "locality" => "required",
            "demarcation" => "required",
            "municipalities_council" => "required",
            "campaign_slogan" => "required",
            "list_number" => "required",
            "campaign" => "required",
            "curp" => "required",
            "curp_confirmation" => "required",
            "rfc" => "required",
            "phone_type" => "required",
            "lada" => "required",
            "phone" => "required",
            "extension" => "required",
            "email" => "required",
            "email_confirmation" => "required",
            "total_annual_income" => "required",
            "salary_annual_income" => "required",
            "financial_performances" => "required",
            "annual_profit_professional_activity" => "required",
            "annual_real_estate_lease_earnings" => "required",
            "professional_services_fees" => "required",
            "other_income" => "required",
            "total_annual_expenses" => "required",
            "personal_expenses" => "required",
            "real_estate_payments" => "required",
            "debt_payments" => "required",
            "loss_personal_activity" => "required",
            "other_expenses" => "required",
            "property" => "required",
            "vehicles" => "required",
            "other_movable_property" => "required",
            "bank_accounts" => "required",
            "other_assets" => "required",
            "payment_debt_amount" => "required",
            "other_passives" => "required",
            "others" => "exclude_if:type_postulate," . CandidateIne::OWNER . "|required",
            "considerations" => "exclude_if:type_postulate," . CandidateIne::OWNER . "|required",
        ];
        $this->validation($request, $rules);

        $candidateIne = CandidateIne::create($request->all());

        $candidate = Candidate::find($candidateIne->origin_candidate_id);
        $candidate->ine_check = true;
        $candidate->save();

        if($candidate->type_postulate == Candidate::OWNER){
            $candidate_ine_alternate = CandidateIne::where('candidate_id', $candidate->id)->first();
            if(!is_null($candidate_ine_alternate)){
                $candidate_ine_alternate->candidate_ine_id = $candidateIne->id;
                $candidate_ine_alternate->save();
            }
        }

        return $this->showOne($candidateIne);
    }

    public function show(CandidateIne $candidateIne)
    {
        $candidateIne->postulate;
        $candidateIne->politicParty;
        return $this->showOne($candidateIne);
    }

    public function update(Request $request, CandidateIne $candidateIne)
    {
        $candidateIne->fill($request->all());
        if($candidateIne->isClean()){
            return $this->errorResponse('A different value must be specified to update',422);
        }

        $candidateIne->save();
        return $this->showOne($candidateIne);
    }

    public function destroy(CandidateIne $candidateIne)
    {
        $candidate = Candidate::find($candidateIne->origin_candidate_id);
        $candidate->ine_check = false;
        $candidate->save();

        $candidateIne->delete();
        return $this->showMessage('Record deleted successfully');
    }
}
