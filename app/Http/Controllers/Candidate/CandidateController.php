<?php

namespace App\Http\Controllers\Candidate;

use App\Candidate;
use App\Http\Controllers\ApiController;
use App\PoliticParty;
use App\Postulate;
use App\User;
use App\Util\ExportExcel;
use App\Util\FieldsExcelReport;
use App\Util\ImportExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CandidateController extends ApiController
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->type === User::ADMIN) {
            $candidates = Candidate::with('postulate_data');
        } else {
            $candidates = Candidate::where('user_id', $user->id)->with('postulate_data');
        }

        if ($request->has('value')) {
            $candidates = $candidates->where(function ($query) use ($request) {
                $query->searchInFields('father_lastname', $request->all()['value'])
                    ->searchInFields('mother_lastname', $request->all()['value'])
                    ->searchInFields('name', $request->all()['value'])
                    ->searchInFields('nickname', $request->all()['value'])
                    ->searchInFields('roads', $request->all()['value'])
                    ->searchInFields('roads_name', $request->all()['value'])
                    ->searchInFields('outdoor_number', $request->all()['value'])
                    ->searchInFields('interior_number', $request->all()['value'])
                    ->searchInFields('neighborhood', $request->all()['value'])
                    ->searchInFields('zipcode', $request->all()['value'])
                    ->searchInFields('municipality', $request->all()['value'])
                    ->searchInFields('elector_key', $request->all()['value'])
                    ->searchInFields('ocr', $request->all()['value'])
                    ->searchInFields('cic', $request->all()['value'])
                    ->searchInFields('entity', $request->all()['value'])
                    ->searchInFields('section', $request->all()['value'])
                    ->searchInFields('birthplace', $request->all()['value'])
                    ->searchInFields('occupation', $request->all()['value'])
                    ->searchInFields('re_election', $request->all()['value'])
                    ->searchInFields('indigenous_group', $request->all()['value'])
                    ->searchInFields('group_sexual_diversity', $request->all()['value'])
                    ->searchInFields('disabled_group', $request->all()['value']);
            });
        }

        $candidates = $candidates->paginate(100);
        return $this->showList($candidates);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->type === User::CAP) {
            $politic_party = PoliticParty::findOrFail($user->politic_party_id);
            $records = [];
            $limitRecords = 0;

            if (!$request->has('candidates')) {
                if ($request->get('postulate') == Candidate::DIPUTACION_RP) {

                    $records = Candidate::where([
                        ['politic_party_id', '=', $politic_party->id],
                        ['postulate', '=', Candidate::DIPUTACION_RP]
                    ])->get();
                    $records = $records->count();
                    $limitRecords = Candidate::TOTAL_DIPUTACION_RP;
                }

                if ($request->get('postulate') == Candidate::DIPUTACION_MR) {
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
                    $newCandidate->user_id = $user->id;
                    $newCandidate->save();
                    $newAlternate = new Candidate($request->get('alternate'));
                    $newAlternate->candidate_id = $newCandidate->id;
                    $newAlternate->politic_party_id = $politic_party->id;
                    $newAlternate->postulate_id = $newCandidate->postulate_id;
                    $newAlternate->postulate = $newCandidate->postulate;
                    $newAlternate->type_postulate = Candidate::ALTERNATE;
                    $newAlternate->user_id = $user->id;
                    $newAlternate->save();
                    return $this->showList([
                        'owner' => $newCandidate,
                        'alternate' => $newAlternate,
                    ]);
                }
            } else {
                foreach ($request->get('candidates')[0] as $candidate) {
                    if ($candidate['owner']['id'] == 0) {
                        if (empty($candidate['owner']['name'])) {
                            continue;
                        }

                        $checkCandidate = Candidate::where(
                            'elector_key',
                            $candidate['owner']['elector_key']
                        )->first();

                        if (!empty($checkCandidate)) {
                            continue;
                        }

                        unset($candidate['owner']['id']);
                        unset($candidate['alternate']['id']);
                        $owner = new Candidate($candidate['owner']);
                        $owner->postulate_id = $request->get('postulate_id');
                        $owner->politic_party_id = $politic_party->id;
                        $owner->postulate = $request->get('postulate');
                        $owner->type_postulate = Candidate::OWNER;
                        $owner->user_id = $user->id;
                        $owner->save();

                        $alternate = new Candidate($candidate['alternate']);
                        $alternate->postulate_id = $request->get('postulate_id');
                        $alternate->politic_party_id = $politic_party->id;
                        $alternate->postulate = $request->get('postulate');
                        $alternate->type_postulate = Candidate::ALTERNATE;
                        $alternate->candidate_id = $owner->id;
                        $alternate->user_id = $user->id;
                        $alternate->save();
                    } else {
                        $owner = Candidate::findOrFail($candidate['owner']['id']);
                        $alternate = Candidate::findOrFail($candidate['alternate']['id']);
                        if ($user->id == $candidate['owner']['user_id']) {
                            $this->updateCandidates($candidate['owner'], $owner);
                            $this->updateCandidates($candidate['alternate'], $alternate);
                        }
                    }
                }
                return $this->showMessage('Save success');
            }
        } else {
            return $this->showMessage('El administrador no puede dar de alta registros', 409);
        }
    }

    public function show(Candidate $candidate)
    {
        $candidate->postulate_data;
        $candidate->alternate;
        return $this->showOne($candidate);
    }

    public function update(Request $request, Candidate $candidate)
    {
        $user = Auth::user();
        $alternate = Candidate::findOrFail($request->all()['alternate']['id']);
        $candidate_ine = $candidate->copyCandidateIne;
        $alternate_ine = $alternate->copyCandidateIne;

        $candidate->fill($request->all());
        $alternate->fill($request->all()['alternate']);

        if (!is_null($candidate_ine)) {
            $candidate_ine->fill($request->all());
        }

        if (!is_null($alternate_ine)) {
            $alternate_ine->fill($request->all()['alternate']);
        }

        if (!$candidate->isClean()) {
            $candidate->user_id = $user->id;
            $candidate->save();
            if (!is_null($candidate_ine)) {
                $candidate_ine->save();
            }
        }

        if (!$alternate->isClean()) {
            $alternate->user_id = $user->id;
            $alternate->save();

            if (!is_null($alternate_ine)) {
                $alternate_ine->save();
            }
        }
        $candidate->alternate;
        return $this->showOne($candidate);
    }

    private function updateCandidates($request, $candidate)
    {
        $rules = [
            'date_birth' => 'date',
        ];

        $candidate_ine = $candidate->copyCandidateIne;
        $candidate->fill($request);
        $candidate->save();
 
        if (!is_null($candidate_ine)) {
            $candidate_ine->fill($request);
            $candidate_ine->save();
        }
    }

    public function destroy(Candidate $candidate)
    {
        $candidate->copyCandidateIne()->delete();
        $candidate->alternate()->delete();
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

        if (is_null($candidate) || empty($candidate->elector_key)) {
            return $this->successResponse([
                'result' => 'true',
                'data' => $candidate
            ], 200);
        } else {
            return $this->successResponse(
                [
                    'result' => 'false',
                    'data' => $candidate
                ],
                200
            );
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
                ->where('politic_party_id', $request->all()['politic_party_id'])
                ->getOwner()
                ->skipFields('Pendiente', -1)
                ->get();
        } else {
            $candidates = Candidate::where('postulate', $request->all()['type'])
                ->getOwner()
                ->skipFields('Pendiente', -1)
                ->get();
        }

        foreach ($candidates as $candidate) {
            $alternate = Candidate::where('candidate_id', $candidate->id)->skipFields('Pendiente', -1)->first();
            if (!is_null($alternate)) {
                $candidates_all[] = $candidate;
                $candidates_all[] = $alternate;
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
                } elseif ($key == 'TIEMPO_RESIDENCIA_MESES') {
                    $data_excel[$i][$key] = "";
                } elseif ($key == 'FECHA_NACIMIENTO') {
                    $date = date("d-m-Y", strtotime($candidate[$value]));
                    $data_excel[$i][$key] = $date;
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

    public function getAyuntamiento(Postulate $postulate)
    {

        $user = Auth::user();
        $owners = Candidate::where([
            ['postulate_id', '=', $postulate->id],
            ['type_postulate', '=', Candidate::OWNER],
            ['politic_party_id', '=', $user->politic_party_id],
            ['postulate', '<>', Candidate::DIPUTACION_RP],
            ['postulate', '<>', Candidate::DIPUTACION_MR],
        ])->get();

        $dataReturn = [];
        foreach ($owners as $key => $owner) {
            $dataReturn[$key]['owner'] = $owner;
            $dataReturn[$key]['alternate'] = $owner->alternate;
        }

        return $this->showList($dataReturn);
    }

    public function createReportByUser(Request $request)
    {
        $rules = [
            'type' => 'required',
            'user_id' => 'required'
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

        $candidates = Candidate::where('postulate', $request->all()['type'])
            ->where('user_id', $request->all()['user_id'])
            ->getOwner()
            ->skipFields('Pendiente', -1)
            ->get();

        foreach ($candidates as $candidate) {
            $alternate = Candidate::where('candidate_id', $candidate->id)->skipFields('Pendiente', -1)->first();
            if (!is_null($alternate)) {
                $candidates_all[] = $candidate;
                $candidates_all[] = $alternate;
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
                } elseif ($key == 'TIEMPO_RESIDENCIA_MESES') {
                    $data_excel[$i][$key] = "";
                } elseif ($key == 'FECHA_NACIMIENTO') {
                    $date = date("d-m-Y", strtotime($candidate[$value]));
                    $data_excel[$i][$key] = $date;
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

    public function importLayout(Request $request)
    {
        $path = Storage::path('ImportAux/');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $request->file->storeAs('ImportAux', 'LayoutCandidates.xlsx');

        $import = new ImportExcel($path . 'LayoutCandidates.xlsx');
        $dataToImport = $import->readExcel(2);
        dd($dataToImport);
        $candidate = [];
        foreach (FieldsExcelReport::LAYOUT_DATA as $key => $value) {
            foreach ($dataToImport as $field) {
                if (empty($key) || empty($value)) {
                    continue;
                }

                $candidate[$value] = $field[$key];
            }
        }
        dd($candidate);
    }

    public function getReportCityHall(Request $request)
    {
        $rules = [
            'postulate_id' => 'required',
            'politic_party_id' => 'required'
        ];

        $this->validate($request, $rules);

        $candidates = Candidate::where('postulate_id', $request->all()['postulate_id'])
            ->where('politic_party_id', $request->all()['politic_party_id'])
            ->where(function ($q) {
                $q->orWhere('postulate', Candidate::SINDICATURA)
                    ->orWhere('postulate', Candidate::REGIDURIA)
                    ->orWhere('postulate', Candidate::PRESIDENCIA);
            })
            ->getOwner()
            ->skipFields('Pendiente', -1)
            ->get();

        $candidates_aux = $candidates->groupBy('postulate');
        $candidates_aux->all();
        $candidates = collect();

        if (isset($candidates_aux[Candidate::PRESIDENCIA])) {
            $candidates = $candidates->toBase()->merge($candidates_aux[Candidate::PRESIDENCIA]);
        }
        if (isset($candidates_aux[Candidate::REGIDURIA])) {
            $candidates = $candidates->toBase()->merge($candidates_aux[Candidate::REGIDURIA]);
        }
        if (isset($candidates_aux[Candidate::SINDICATURA])) {
            $candidates = $candidates->toBase()->merge($candidates_aux[Candidate::SINDICATURA]);
        }

        $candidates_all = array();

        $i = 0;
        $ordinalNumber = [
            'PRIMERA',
            'SEGUNDA',
            'TERCERA',
            'CUARTA',
            'QUINTA',
            'SEXTA',
            'SÉPTIMA',
            'OCTAVA',
            'NOVENA',
            'DÉCIMA',
            'UNDÉCIMO',
            'DUOÉCIMO',
            'DÉCIMA TERCERA',
            'DÉCIMA CUARTA',
            'DÉCIMA QUINTA',
            'DÉCIMA SEXTA',
            'DÉCIMA SÉPTIMA',
            'DÉCIMA OCTAVA',
            'DÉCIMA NOVENA',
            'VIGÉSIMO',
        ];

        foreach ($candidates as $candidate) {
            $alternate = Candidate::where('candidate_id', $candidate->id)->skipFields('Pendiente', -1)->first();
            if (!is_null($alternate)) {

                switch ($candidate->postulate) {
                    case Candidate::PRESIDENCIA:
                        $charge = 'PRESIDENCIA';
                        break;
                    case Candidate::REGIDURIA:
                        $charge = 'REGIDURIA';
                        break;
                    case Candidate::SINDICATURA:
                        $charge = 'SINDICATURA';
                        break;
                    default:
                        $charge = '';
                        break;
                }

                $candidates_all[$i]['charge'] = $charge;
                $candidates_all[$i]['ordinal'] = $ordinalNumber[$i];
                $candidates_all[$i]['ordinalNumber'] = ($i + 1).'ª';
                $candidates_all[$i]['owner'] = $candidate;
                $candidates_all[$i]['alternate'] = $alternate;

                $i++;
            }
        }

        $postulate = Postulate::find($request->all()['postulate_id']);

        $reportPath = "";
        $output = "";
        $parameters = "";
        $data = [
            'municipality' => $postulate->municipality,
            "charges" => $candidates_all
        ];

        $data = \GuzzleHttp\json_encode($data);
        $pathJar = Storage::path('JasperReportGenerator/') . 'JasperReportGenerator.jar';
        $arguments = "$reportPath \"" . addslashes($data) . "\" --output=$output --parameters=\"$parameters\" --format=xls";
        dd("java -jar $pathJar $arguments");
    }
}
