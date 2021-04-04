<?php

namespace App\Http\Controllers\Candidate;

use App\Candidate;
use App\PoliticParty;
use App\Postulate;
use App\User;
use App\Utils\ExportExcel;
use App\Utils\FieldsExcelReport;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CandidateController extends ApiController
{

    public function index()
    {
        $user = Auth::user();
        $politic_party = PoliticParty::findOrFail($user->politic_party_id);

        if ($user->type === User::ADMIN) {
            return $this->showAll(Candidate::all());
        } else {
            return $this->showList(Candidate::where('politic_party_id', $politic_party->id)->get());
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->type === User::CAP) {
            $history = Postulate::findOrFail($request->has('postulate_id'));
            $politic_party = PoliticParty::findOrFail($user->politic_party_id);
            $records = [];
            $limitRecords = 0;

            if ($request->has('postulate') == Candidate::REGIDURIA) {
                $records = Candidate::where([
                    ['postulate_id', '=', $history->id],
                    ['politic_party_id', '=', $politic_party->id],
                    ['postulate', '=', Candidate::REGIDURIA]
                ])->get();
                $records = $records->count();
                $limitRecords = $history->regidurias * 2;
            }

            if ($request->has('postulate') == Candidate::SINDICATURA) {
                $records = Candidate::where([
                    ['postulate_id', '=', $history->id],
                    ['politic_party_id', '=', $politic_party->id],
                    ['postulate', '=', Candidate::SINDICATURA]

                ])->get();
                $records = $records->count();
                $limitRecords = $history->sindicaturas * 2;
            }

            if ($request->has('postulate') == Candidate::PRESIDENCIA) {
                $records = Candidate::where([
                    ['postulate_id', '=', $history->id],
                    ['politic_party_id', '=', $politic_party->id],
                    ['postulate', '=', Candidate::PRESIDENCIA]
                ])->get();
                $records = $records->count();
                $limitRecords = $history->presidency * 2;
            }

            if ($request->has('postulate') == Candidate::DIPUTACION_RP) {

                $records = Candidate::where([
                    ['politic_party_id', '=', $politic_party->id],
                    ['postulate', '=', Candidate::DIPUTACION_RP]
                ])->get();
                $records = $records->count();
                $limitRecords = Candidate::DIPUTACION_RP;
            }

            if ($request->has('postulate') == Candidate::DIPUTACION_MR) {
                $records = Candidate::where([
                    ['politic_party_id', '=', $politic_party->id],
                    ['postulate', '=', Candidate::TOTAL_DIPUTACION_MR]
                ])->get();
                $records = $records->count();
                $limitRecords = Candidate::TOTAL_DIPUTACION_MR;
            }
            if ($records < $limitRecords) {
                $newCandidate = new Candidate($request->all());
                $newCandidate->politic_party_id = $politic_party->id;
                $newCandidate->save();
                $newAlternate = new Candidate($request->get('alternate'));
                $newAlternate->candidate_id = $newCandidate->id;
                $newAlternate->politic_party_id = $politic_party->id;
                $newAlternate->postulate_id = $newCandidate->postulate_id;
                $newAlternate->postulate = $newCandidate->postulate;
                $newAlternate->type_postulate = Candidate::ALTERNATE;
                $newAlternate->save();
                return $this->showList([
                    'owner' => $newCandidate,
                    'alternate' => $newAlternate,
                ]);
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
            're_election',
            'postulate',
            'type_postulate',
            'indigenous_group',
            'group_sexual_diversity',
            'disabled_group',
            'politic_party_id',
            'postulate_id',
            'candidate_id',
            'ine_check'
        ]));
    }


    public function destroy(Candidate $candidate)
    {
        $candidate->copyCandidateIne()->delete();
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

    public function validateOCR(Request $request)
    {
        $ocr = $request->all()['ocr'];
        $id = $request->all()['id'] ?? null;

        if (is_null($id)) {
            $candidate = Candidate::where('ocr', $ocr)->first();
        } else {
            $candidate = Candidate::where('ocr', $ocr)
                ->where('id', '<>', $id)
                ->first();
        }

        if (is_null($candidate)) {
            return $this->successResponse('true', 200);
        } else {
            return $this->successResponse('false', 200);
        }
    }

    public function createReport(Request $request)
    {
        $rules = [
            'type' => 'required'
        ];

        $this->validate($request, $rules);

        $candidates_all = [];
        $data_excel = [];

        switch ($request->all()['type']) {
            case Candidate::DIPUTACION_RP:
                $data = FieldsExcelReport::DRP;
                break;
            case Candidate::DIPUTACION_MR:
                $data = FieldsExcelReport::DMR;
                break;
            default:
                $data = FieldsExcelReport::AYU;
                break;
        };

        if ($request->has('politic_party_id')) {
            $candidates = Candidate::where('postulate', $request->all()['type'])
                ->where('politic_party', $request->all()['politic_party_id'])
                ->getOwner()
                ->get();
        } else {
            $candidates = Candidate::where('postulate', $request->all()['type'])
                ->getOwner()
                ->get();
        }

        foreach ($candidates as $candidate) {
            $candidates_all[] = $candidate;
            if (!is_null($candidate->alternate)) {
                $candidates_all[] = $candidate->alternate;
            }
        }

        $i = 0;
        foreach ($candidates_all as $candidate) {
            foreach ($data as $key => $value) {
                if ($key == 'CARGO') {
                    $data_excel[$i][$key] = ($candidate[$value] == Candidate::OWNER) ? 'PROPIETARIO' : 'SUPLENTE';
                } elseif ($key == 'DISTRITO') {
                    $postulate = Postulate::find($candidate[$value]);
                    $data_excel[$i][$key] = $postulate->district;
                } elseif ($key == 'NO_MPIO') {
                    $postulate = Postulate::find($candidate[$value]);
                    $data_excel[$i][$key] = $postulate->municipality_key;
                } else {
                    $data_excel[$i][$key] = $candidate[$value];
                }
            }
            $i++;
        }

        $path = Storage::path('reports/');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $report = new ExportExcel($path . 'basic.xlsx');
        $report->createExcel($data_excel, array_keys($data));

        return $this->downloadFile($path . 'basic.xlsx');
    }
}
