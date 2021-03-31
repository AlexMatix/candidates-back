<?php

namespace App\Http\Controllers\Candidate;

use App\Candidate;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;

class CandidateController extends ApiController
{

    public function index()
    {
        return $this->showAll(Candidate::all());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $candidate = new Candidate($request->all());
        $candidate->party = $user->party;
        $candidate->save();
        return $this->showOne($candidate);
    }

    public function show(Candidate $candidate)
    {
        return $this->showOne($candidate);
    }


    public function update(Request $request, Candidate $candidate)
    {
        $rules = [
            'date_birth' => 'date',
        ];

        $this->validate($request, $rules);
        $candidate->fill($request->only([
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
        ]));
    }


    public function destroy(Candidate $candidate)
    {
        $candidate->delete();
        return $this->showOne($candidate);
    }
}
