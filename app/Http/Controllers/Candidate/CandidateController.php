<?php

namespace App\Http\Controllers\Candidate;

use App\Candidate;
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
        if ($user->type === User::ADMIN) {
            return $this->showAll(Candidate::all());
        } else {
            return $this->showList(Candidate::where('party', $user->party)->get());
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->type === User::CAP) {

            $history = Postulate::findOrFail($request->has('postulate_id'));
            $save = false;

            switch ($request->has('postulate')) {
//                case Candidate::DIPUTACION :
//                    $countOwner = $this->checkSum($history->diputacion_owner_count, $history->);
//                    $countAlternate = $history->diputacion_alternate_count;
//                    break;

                case Candidate::REGIDURIA :
                    if ($request->has('type_postulate') === Candidate::OWNER && $this->checkSum($history->regidurias_owner_count, $history->regidurias)) {
                        $history->regidurias_owner_count++;
                        $history->save();
                        $save = true;
                    }

                    if ($request->has('type_postulate') === Candidate::ALTERNATE && $this->checkSum($history->regidurias_alternate_count, $history->regidurias)) {
                        $history->regidurias_alternate_count++;
                        $history->save();
                        $save = true;
                    }
                    break;

                case Candidate::SINDICATURA :
                    if ($request->has('type_postulate') === Candidate::OWNER && $this->checkSum($history->sindicaturas_owner_count, $history->sindicaturas)) {
                        $history->sindicaturas_owner_count++;
                        $history->save();
                        $save = true;
                    }

                    if ($request->has('type_postulate') === Candidate::ALTERNATE && $this->checkSum($history->sindicaturas_alternate_count, $history->sindicaturas)) {
                        $history->sindicaturas_alternate_count++;
                        $history->save();
                        $save = true;
                    }

                    break;

                case Candidate::PRESIDENCIA :
                    if ($request->has('type_postulate') === Candidate::OWNER && $this->checkSum($history->presidency_owner_count, $history->presidency)) {
                        $history->presidency_owner_count++;
                        $history->save();
                        $save = true;
                    }

                    if ($request->has('type_postulate') === Candidate::ALTERNATE && $this->checkSum($history->presidency_alternate_count, $history->presidency)) {
                        $history->presidency_alternate_count++;
                        $history->save();
                        $save = true;
                    }
                    break;

                default:
                    return $this->showMessage('postulate not found', 404);
            }

            if($save){
                $candidate = new Candidate($request->all());
                $candidate->party = $user->party;
                $candidate->save();
                return $this->showOne($candidate);
            }else{
                return $this->errorResponse('No se puede generar mas registros de este tipo para este municipio', 409);
            }


        } else {
            return $this->showMessage('El administrador no puede dar de alta registros', 409);
        }
    }

    private function checkSum($postulate, $total)
    {
        if (($postulate + 1) > $total) {
            return false;
        }

        return true;
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
