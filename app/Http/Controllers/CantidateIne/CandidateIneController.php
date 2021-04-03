<?php

namespace App\Http\Controllers\CantidateIne;

use App\Candidate;
use App\CandidateIne;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

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
            "demarcation" => "required",
            "campaign" => "required",
            "curp" => "required",
            "curp_confirmation" => "required",
            "rfc" => "required",
            "phone_type" => "required",
            "phone" => "required",
            "email" => "required",
            "email_confirmation" => "required",
            "total_annual_income" => "required",
            "total_annual_expenses" => "required",
        ];
        $this->validate($request, $rules);

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

        if($candidate->type_postulate == Candidate::ALTERNATE){
            $candidate_ine_alternate = CandidateIne::where('origin_candidate_id', $candidate->candidate_id)->first();
            if(!is_null($candidate_ine_alternate)){
                $candidateIne->candidate_ine_id = $candidate_ine_alternate->id;
                $candidateIne->save();
            }
        }

        return $this->showOne($candidateIne);
    }

    public function show(CandidateIne $candidateIne)
    {
        $candidateIne->postulate;
        $candidateIne->politicParty;
        $candidateIne->owner;
        $candidateIne->alternate;
        $candidateIne->originCandidate;
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

        $candidateIne->alternate()->delete();
        $candidateIne->delete();
        return $this->showMessage('Record deleted successfully');
    }
}
