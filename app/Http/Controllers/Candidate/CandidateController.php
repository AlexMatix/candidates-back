<?php

namespace App\Http\Controllers\Candidate;

use App\Candidate;
use App\PoliticParty;
use App\Postulate;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;

class CandidateController extends ApiController
{

    public function index()
    {
        $user = Auth::user();
        $politic_party = PoliticParty::findOrFail($user->politic_party_id);

        if ($user->type === User::ADMIN) {
            return $this->showAll(Candidate::all());
        } else {
            return $this->showList(Candidate::where('party', $politic_party->id)->get());
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->type === User::CAP) {
            $history = Postulate::findOrFail($request->has('postulate_id'));
            $politic_party = PoliticParty::findOrFail($user->politic_party_id);
            $records = [];

            if ($request->has('postulate') === Candidate::REGIDURIA) {
                $records = Candidate::where([
                    ['postulate_id', '=', $history->id],
                    ['politic_party_id', '=', $politic_party->id],
                    ['postulate', '=', Candidate::REGIDURIA]
                ])->select('COUNT(id)')
                    ->get();
                $limitRecords = $history->regidurias * 2;
            }

            if ($request->has('postulate') === Candidate::SINDICATURA) {
                $records = Candidate::where([
                    ['postulate_id', '=', $history->id],
                    ['politic_party_id', '=', $politic_party->id],
                    ['postulate', '=', Candidate::SINDICATURA]

                ])->select('COUNT(id)')
                    ->get();
                $limitRecords = $history->sindicaturas * 2;
            }

            if ($request->has('postulate') === Candidate::PRESIDENCIA) {
                $records = Candidate::where([
                    ['postulate_id', '=', $history->id],
                    ['politic_party_id', '=', $politic_party->id],
                    ['postulate', '=', Candidate::PRESIDENCIA]
                ])->select('COUNT(id)')
                    ->get();
                $limitRecords = $history->presidency * 2;
            }

            if ($records < $limitRecords){
                $newCandidate = new Candidate($request->all());
                $newCandidate->politic_party_id = $politic_party->id;
                $newCandidate->save();

            }

        } else {
            return $this->showMessage('El administrador no puede dar de alta registros', 409);
        }
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
            're-election',
            'postulate',
            'type_postulate',
            'indigenous_group',
            'group_sexual_diversity',
            'disabled_group',
            'party',
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
