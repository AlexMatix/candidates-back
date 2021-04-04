<?php

namespace App\Http\Controllers\CantidateIne;

use App\Candidate;
use App\CandidateIne;
use App\Http\Controllers\ApiController;
use App\Postulate;
use App\Utils\ExportExcel;
use App\Utils\FieldsExcelReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
            "curp" => "required",
            "curp_confirmation" => "required",
            "rfc" => "required",
            "phone_type" => "required",
            "phone" => "required",
            "email" => "required",
            "email_confirmation" => "required"
        ];
        $this->validate($request, $rules);

        $candidateIne = new CandidateIne($request->all());
        $candidateIne->number_list = ($request->has('number_list') && !is_null($request->all()['number_list'])) ? $request->all()['number_list'] : 0;
        $candidateIne->save();

        $candidate = Candidate::find($candidateIne->origin_candidate_id);
        $candidate->ine_check = true;
        $candidate->save();

        if ($candidate->type_postulate == Candidate::OWNER) {
            $candidate_ine_alternate = CandidateIne::where('candidate_id', $candidate->id)->first();
            if (!is_null($candidate_ine_alternate)) {
                $candidate_ine_alternate->candidate_ine_id = $candidateIne->id;
                $candidate_ine_alternate->save();
            }
        }

        if ($candidate->type_postulate == Candidate::ALTERNATE) {
            $candidate_ine_alternate = CandidateIne::where('origin_candidate_id', $candidate->candidate_id)->first();
            if (!is_null($candidate_ine_alternate)) {
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
        if ($candidateIne->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
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

    public function createReportINE(Request $request)
    {
        $rules = [
            'type' => 'required'
        ];

        $this->validate($request, $rules);

        $data_excel = [];
        $array_key_alternate = [];

        if ($request->all()['type'] == CandidateIne::DIPUTACION_RP || $request->all()['type'] == CandidateIne::DIPUTACION_MR || $request->all()['type'] == CandidateIne::PRESIDENCIA) {
            $data = FieldsExcelReport::INE;
            $data_alternate = FieldsExcelReport::INE_ALTERNATE;
        } else {
            $data = FieldsExcelReport::INE_2;
            $data_alternate = FieldsExcelReport::INE_2_ALTERNATE;
        }

        if ($request->has('politic_party_id')) {
            $candidates = CandidateIne::where('postulate', $request->all()['type'])
                ->where('politic_party_id', $request->all()['politic_party_id'])
                ->getOwner()
                ->get();
        } else {
            $candidates = CandidateIne::where('postulate', $request->all()['type'])
                ->getOwner()
                ->get();
        }

        $i = 0;
        foreach ($candidates as $candidate) {
            //OWNER DATA
            foreach ($data as $key => $value) {
                if ($key == 'Distrito') {
                    $postulate = Postulate::find($candidate[$value]);
                    $data_excel[$i][$key] = $postulate->district;
                } elseif ($key == 'Municipio') {
                    $postulate = Postulate::find($candidate->postulate_id);
                    $data_excel[$i][$key] = $postulate->municipality;
                } else {
                    $data_excel[$i][$key] = $candidate[$value];
                }
            }

            //ALTERNATE DATA
            if (!is_null($candidate->alternate)) {
                foreach ($data_alternate as $key => $value) {
                    if ($key == 'Registra suplencia|') {
                        $data_excel[$i][$key] = 1;
                    } else {
                        $data_excel[$i][$key] = $candidate[$value];
                    }
                }
            } else {
                return $this->errorResponse('Candidato sin suplente registrado', 404);
            }
            $i++;
        }

        foreach (array_keys($data_alternate) as $item) {
            $array_key_alternate[] = str_replace('|', '', $item);
        }

        $path = Storage::path('reports/');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $report = new ExportExcel($path . 'basic.xlsx');
        $report->createExcel($data_excel, array_merge(array_keys($data), $array_key_alternate));

        return $this->downloadFile($path . 'basic.xlsx');
    }
}
