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

    public function validateElectorKey(Request $request)
    {
        $electorKey = $request->all()['electorKey'];
        $id = $request->all()['id'] ?? null;

        if (is_null($id)) {
            $candidate = Candidate::where('elector_key', $electorKey)->first();
        } else {
            $candidate = Candidate::where('elector_key', $electorKey)
                ->where('id', '<>', $id)
                ->first();
        }

        if (is_null($candidate)) {
            return $this->successResponse([
                'result' => 'true',
                'data' => $candidate
            ], 200);
        } else {
            return $this->successResponse(
                [
                    'result' => 'false',
                    'data' => $candidate
                ], 200);
        }
    }
}
