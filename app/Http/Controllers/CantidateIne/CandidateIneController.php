<?php

namespace App\Http\Controllers\CantidateIne;

use App\Candidate;
use App\CandidateIne;
use App\PoliticParty;
use App\Http\Controllers\ApiController;
use App\Postulate;
use App\Util\ExportExcel;
use App\Util\FieldsExcelReport;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Collection;
use Illuminate\Support\Carbon;

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

    public function show(Candidate $candidate)
    {
        $candidateIne = CandidateIne::where('origin_candidate_id', $candidate->id)->firstOrFail();

        $candidateIne->postulate;
        $candidateIne->politicParty;
        $candidateIne->owner;
        $candidateIne->alternate;
        $candidateIne->originCandidate;
        return $this->showOne($candidateIne);
    }

    public function update(Request $request, Candidate $candidate)
    {
        $candidateIne = CandidateIne::where('origin_candidate_id', $candidate->id)->firstOrFail();
        $candidateIne->fill($request->all());
        if ($candidateIne->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $candidateIne->save();
        return $this->showOne($candidateIne);
    }

    public function destroy(Candidate $candidate)
    {
        $candidateIne = CandidateIne::where('origin_candidate_id', $candidate->id)->firstOrFail();

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

        if ($request->has('politic_party_id')) {
            if ($request->all()['type'] == 1) {
                $data = FieldsExcelReport::INE;
                $data_alternate = FieldsExcelReport::INE_ALTERNATE;
                $candidates = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                    ->select('candidate_ines.*', 'candidates.user_id')
                    ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                    ->where(function ($q) {
                        $q->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_MR)
                            ->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_RP)
                            ->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA);
                    })
                    ->where('candidate_ines.politic_party_id', $request->all()['politic_party_id'])
                    ->skipFields('Pendiente', -1)
                    ->orderBy('candidate_ines.postulate')
                    ->orderBy('candidate_ines.number_line')
                    ->get();
            } else {
                $data = FieldsExcelReport::INE_2;
                $data_alternate = FieldsExcelReport::INE_2_ALTERNATE;
                $candidates = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                    ->select('candidate_ines.*', 'candidates.user_id')
                    ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                    ->where(function ($q) {
                        $q->orWhere('candidate_ines.postulate', CandidateIne::SINDICATURA)
                            ->orWhere('candidate_ines.postulate', CandidateIne::REGIDURIA);
                    })
                    ->where('candidate_ines.politic_party_id', $request->all()['politic_party_id'])
                    ->skipFields('Pendiente', -1)
                    ->orderBy('candidate_ines.number_line')
                    ->orderBy('candidate_ines.type_postulate')
                    ->get();

                $candidates_aux = $candidates->groupBy('postulate_id');
                $candidates_aux->all();

                $candidates = collect();
                foreach ($candidates_aux as $candidate) {
                    $candidates = $candidates->toBase()->merge($candidate);
                }
            }
        } else {
            if ($request->all()['type'] == 1) {
                $data = FieldsExcelReport::INE;
                $data_alternate = FieldsExcelReport::INE_ALTERNATE;
                $candidates = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                    ->select('candidate_ines.*', 'candidates.user_id')
                    ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                    ->where(function ($q) {
                        $q->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_MR)
                            ->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_RP)
                            ->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA);
                    })
                    ->skipFields('Pendiente', -1)
                    ->orderBy('candidate_ines.postulate')
                    ->orderBy('candidate_ines.number_line')
                    ->get();

            } else {
                $data = FieldsExcelReport::INE_2;
                $data_alternate = FieldsExcelReport::INE_2_ALTERNATE;
                $candidates = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                    ->select('candidate_ines.*', 'candidates.user_id')
                    ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                    ->where(function ($q) {
                        $q->orWhere('candidate_ines.postulate', CandidateIne::SINDICATURA)
                            ->orWhere('candidate_ines.postulate', CandidateIne::REGIDURIA);
                    })
                    ->skipFields('Pendiente', -1)
                    ->orderBy('candidate_ines.number_line')
                    ->orderBy('candidate_ines.type_postulate')
                    ->get();

                $candidates_aux = $candidates->groupBy('postulate_id');
                $candidates_aux->all();

                $candidates = collect();
                foreach ($candidates_aux as $candidate) {
                    $candidates = $candidates->toBase()->merge($candidate);
                }
            }
        }

        $i = 0;
        foreach ($candidates as $candidate) {
            //OWNER DATA
            foreach ($data as $key => $value) {
                if ($key == 'Distrito') {
                    $postulate = Postulate::find($candidate[$value]);
                    $data_excel[$i][$key] = $postulate->district;
                } elseif ($key == 'Municipio' || $key == 'MUNICIPIO') {
                    $postulate = Postulate::find($candidate->postulate_id);
                    $data_excel[$i][$key] = $postulate->municipality;
                } elseif ($key == 'Correo electr??nico' || $key == 'CORREO_ELECTR??NICO') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
//                    $data_excel[$i][$key] = "morenasnr@gmail.com";
                } elseif ($key == 'Confirmaci??n correo electronico' || $key == 'CONFIRMACI??N_CORREO') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
//                    $data_excel[$i][$key] = "morenasnr@gmail.com";
                } elseif ($key == 'Tipo de residencia en meses' || $key == 'TIEMPO_RESIDENCIA_MESES') {
                    $data_excel[$i][$key] = "";
                } elseif ($key == 'Tipo candidatura' || $key == 'TIPO_CANDIDATURA') {
                    $reportCandidate = [
                        "1" => 8,
                        "2" => 7,
                        "3" => 28,
                        "4" => 26,
                        "5" => 9,
                    ];
                    $data_excel[$i][$key] = $reportCandidate[$candidate[$value]];
                } elseif ($key == 'Fecha de nacimiento' || $key == 'FECHA_NACIMIENTO') {
                    $date = date("d-m-Y", strtotime($candidate[$value]));
                    $data_excel[$i][$key] = $date;
                } elseif ($key == 'Sexo' || $key == 'SEXO') {
                    $data_excel[$i][$key] = $candidate[$value] === 'HOMBRE' ? 'H' : 'M';
                } elseif ($key == 'PARTIDO' || $key == 'Partido') {
                    $politic_party = PoliticParty::find($candidate->politic_party_id);
                    $data_excel[$i][$key] = $politic_party->name;
                } else {
                    $data_excel[$i][$key] = $candidate[$value];
                }
            }

            //ALTERNATE DATA
            if (!is_null($candidate->alternate)) {
                foreach ($data_alternate as $key => $value) {
                    if ($key == 'Registra suplencia|') {
                        $data_excel[$i][$key] = 1;
                    } elseif ($key == 'Tipo de residencia en meses|' || $key == 'RESIDENCIA_MESES_SUPLENCIA') {
                        $data_excel[$i][$key] = "";
                    } elseif ($key == 'Fecha de nacimiento|') {
                        $date = date("d-m-Y", strtotime($candidate->alternate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Correo electr??nico|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                        $data_excel[$i][$key] = "morenasnr@gmail.com";
                    } elseif ($key == 'Confirmaci??n de correo electr??nico|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
//                        $data_excel[$i][$key] = "morenasnr@gmail.com";
                    } elseif ($key == 'CONFIRMACI??N_CORREO_SUPLENCIA|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                    } elseif ($key == 'FECHA_NACIMIENTO_SUPLENCIA|' || $key == "Fecha de nacimiento|") {
                        $date = date("d-m-Y", strtotime($candidate->alternate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Sexo|' || $key == 'SEXO_SUPLENCIA|') {
                        $data_excel[$i][$key] = $candidate->alternate[$value] === 'HOMBRE' ? 'H' : 'M';
                    } elseif ($key == 'Correo electr??nico|' || $key == 'Confirmaci??n de correo electr??nico|' || $key == 'CORREO_ELECTR??NICO_SUPLENCIA|' || $key == 'CONFIRMACI??N_CORREO_SUPLENCIA|') {
//                        $data_excel[$i][$key] = "morenasnr@gmail.com";;
                    } else {
                        $data_excel[$i][$key] = $candidate->alternate[$value];
                    }
                }
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

    public function createReportINEByUser(Request $request)
    {
        $rules = [
            'type' => 'required',
            'user_id' => 'required'
        ];

        $this->validate($request, $rules);

        $data_excel = [];
        $array_key_alternate = [];
        $candidates = [];
        if ($request->all()['type'] == 1) {
            $data = FieldsExcelReport::INE;
            $data_alternate = FieldsExcelReport::INE_ALTERNATE;
            $candidates = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                ->select('candidate_ines.*', 'candidates.user_id')
                ->where('user_id', $request->all()['user_id'])
                ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                ->where(function ($q) {
                    $q->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_MR)
                        ->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_RP)
                        ->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA);
                })
                ->skipFields('Pendiente', -1)
                ->orderBy('candidate_ines.politic_party_id')
                ->orderBy('candidate_ines.created_at')->get();

        } else {
            $data = FieldsExcelReport::INE_2;
            $data_alternate = FieldsExcelReport::INE_2_ALTERNATE;
            $candidates = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                ->select('candidate_ines.*', 'candidates.user_id')
                ->where('user_id', $request->all()['user_id'])
                ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                ->where(function ($q) {
                    $q->orWhere('candidate_ines.postulate', CandidateIne::SINDICATURA)
                        ->orWhere('candidate_ines.postulate', CandidateIne::REGIDURIA);
                })
                ->skipFields('Pendiente', -1)
                ->orderBy('candidate_ines.politic_party_id')
                ->orderBy('candidate_ines.created_at')->get();

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
                } elseif ($key == 'Tipo de residencia en meses|' || $key == 'RESIDENCIA_MESES_SUPLENCIA') {
                    $data_excel[$i][$key] = "";
                } elseif ($key == 'Fecha de nacimiento|' || $key == 'FECHA_NACIMIENTO') {
                    $date = date("d-m-Y", strtotime($candidate[$value]));
                    $data_excel[$i][$key] = $date;
                } elseif ($key == 'Correo electr??nico|') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                } elseif ($key == 'Confirmaci??n de correo electr??nico|') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                } elseif ($key == 'CONFIRMACI??N_CORREO_SUPLENCIA|') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                } elseif ($key == 'FECHA_NACIMIENTO_SUPLENCIA|') {
                    $date = date("d-m-Y", strtotime($candidate[$value]));
                    $data_excel[$i][$key] = $date;
                } elseif ($key == 'Sexo' || $key == 'SEXO') {
                    $data_excel[$i][$key] = $candidate[$value] === 'HOMBRE' ? 'H' : 'M';
                } elseif ($key == 'Sexo|' || $key == 'SEXO_SUPLENCIA|') {
                    $data_excel[$i][$key] = $candidate[$value] === 'HOMBRE' ? 'H' : 'M';
                } elseif ($key == 'Tipo candidatura' || $key == 'TIPO_CANDIDATURA') {
                    $reportCandidate = [
                        "1" => 8,
                        "2" => 7,
                        "3" => 28,
                        "4" => 26,
                        "5" => 9,
                    ];
                    $data_excel[$i][$key] = $reportCandidate[$candidate[$value]];
                } else {
                    $data_excel[$i][$key] = $candidate[$value];
                }
            }

            //ALTERNATE DATA
            if (!is_null($candidate->alternate)) {
                foreach ($data_alternate as $key => $value) {
                    if ($key == 'Registra suplencia|') {
                        $data_excel[$i][$key] = 1;
                    } elseif ($key == 'Tipo de residencia en meses|' || $key == 'RESIDENCIA_MESES_SUPLENCIA') {
                        $data_excel[$i][$key] = "";
                    } elseif ($key == 'Fecha de nacimiento|') {
                        $date = date("d-m-Y", strtotime($candidate->alternate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Correo electr??nico|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                    } elseif ($key == 'Confirmaci??n de correo electr??nico|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                    } elseif ($key == 'CONFIRMACI??N_CORREO_SUPLENCIA|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                    } elseif ($key == 'FECHA_NACIMIENTO_SUPLENCIA|') {
                        $date = date("d-m-Y", strtotime($candidate->alternate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Sexo|' || $key == 'SEXO_SUPLENCIA|') {
                        $data_excel[$i][$key] = $candidate->alternate[$value] === 'HOMBRE' ? 'H' : 'M';
                    } else {
                        $data_excel[$i][$key] = $candidate->alternate[$value];
                    }
                }
            }
//            else {
//                return $this->errorResponse('Candidato sin suplente registrado', 404);
//            }
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

    public function reportDiputadosByDistrictIne(Request $request)
    {
        $rules = [
            'district' => 'required'
        ];

        $this->validate($request, $rules);

        $data_excel = [];
        $array_key_alternate = [];
        $candidates = [];

        $data = FieldsExcelReport::INE;
        $data_alternate = FieldsExcelReport::INE_ALTERNATE;

        if ($request->has('politic_party_id')) {
            $data = FieldsExcelReport::INE;
            $data_alternate = FieldsExcelReport::INE_ALTERNATE;
            $candidatesResult = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                ->select('candidate_ines.*', 'candidates.user_id')
                ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                ->where('candidate_ines.politic_party_id', $request->all()['politic_party_id'])
                ->where(function ($q) {
                    $q->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_MR)
                        ->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_MR);
                })
                ->orderBy('candidate_ines.postulate')
                ->orderBy('candidate_ines.number_line')
                ->with("postulate_data")
                ->get();
        } else {

            $data = FieldsExcelReport::INE;
            $data_alternate = FieldsExcelReport::INE_ALTERNATE;
            $candidatesResult = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                ->select('candidate_ines.*', 'candidates.user_id')
                ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                ->where(function ($q) {
                    $q->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_MR)
                        ->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_RP);
                })
                ->orderBy('candidate_ines.postulate')
                ->orderBy('candidate_ines.number_line')
                ->with("postulate_data")
                ->get();
        }
        $districts = $request->get('district');
        foreach ($districts as $district) {
            foreach ($candidatesResult as $candidate) {
                if ($district['id'] === $candidate->postulate_data->district) {
                    $candidates[] = $candidate;
                }
            }
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
                } elseif ($key == 'Tipo de residencia en meses|' || $key == 'RESIDENCIA_MESES_SUPLENCIA') {
                    $data_excel[$i][$key] = "";
                } elseif ($key == 'Fecha de nacimiento|') {
                    $date = date("d-m-Y", strtotime($candidate[$value]));
                    $data_excel[$i][$key] = $date;
                } elseif ($key == 'Correo electr??nico|') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                } elseif ($key == 'Confirmaci??n de correo electr??nico|') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                } elseif ($key == 'CONFIRMACI??N_CORREO_SUPLENCIA|') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                } elseif ($key == 'FECHA_NACIMIENTO_SUPLENCIA|') {
                    $date = date("d-m-Y", strtotime($candidate[$value]));
                    $data_excel[$i][$key] = $date;
                } elseif ($key == 'Sexo' || $key == 'SEXO') {
                    $data_excel[$i][$key] = $candidate[$value] === 'HOMBRE' ? 'H' : 'M';
                } elseif ($key == 'Sexo|' || $key == 'SEXO_SUPLENCIA|') {
                    $data_excel[$i][$key] = $candidate[$value] === 'HOMBRE' ? 'H' : 'M';
                } elseif ($key == 'Tipo candidatura' || $key == 'TIPO_CANDIDATURA') {
                    $reportCandidate = [
                        "1" => 8,
                        "2" => 7,
                        "3" => 28,
                        "4" => 26,
                        "5" => 9,
                    ];
                    $data_excel[$i][$key] = $reportCandidate[$candidate[$value]];
                } else {
                    $data_excel[$i][$key] = $candidate[$value];
                }
            }

            //ALTERNATE DATA
            if (!is_null($candidate->alternate)) {
                foreach ($data_alternate as $key => $value) {
                    if ($key == 'Registra suplencia|') {
                        $data_excel[$i][$key] = 1;
                    } elseif ($key == 'Tipo de residencia en meses|' || $key == 'RESIDENCIA_MESES_SUPLENCIA') {
                        $data_excel[$i][$key] = "";
                    } elseif ($key == 'Fecha de nacimiento|') {
                        $date = date("d-m-Y", strtotime($candidate->alternate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Correo electr??nico|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                    } elseif ($key == 'Confirmaci??n de correo electr??nico|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                    } elseif ($key == 'CONFIRMACI??N_CORREO_SUPLENCIA|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                    } elseif ($key == 'FECHA_NACIMIENTO_SUPLENCIA|') {
                        $date = date("d-m-Y", strtotime($candidate->alternate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Sexo|' || $key == 'SEXO_SUPLENCIA|') {
                        $data_excel[$i][$key] = $candidate->alternate[$value] === 'HOMBRE' ? 'H' : 'M';
                    } else {
                        $data_excel[$i][$key] = $candidate->alternate[$value];
                    }
                }
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

    public function ayuntamientoMunicipalityPoliticParty(Request $request)
    {
        $rules = [
            'district' => 'required'
        ];

        $this->validate($request, $rules);

        $data_excel = [];
        $array_key_alternate = [];
        $candidates = [];

        $data = FieldsExcelReport::INE;
        $data_alternate = FieldsExcelReport::INE_ALTERNATE;

        if ($request->has('politic_party_id')) {
            $data = FieldsExcelReport::INE;
            $data_alternate = FieldsExcelReport::INE_ALTERNATE;
            $candidatesResult = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                ->select('candidate_ines.*', 'candidates.user_id')
                ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                ->where('candidate_ines.politic_party_id', $request->all()['politic_party_id'])
                ->where(function ($q) {
                    $q->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA)
                        ->orWhere('candidate_ines.postulate', CandidateIne::REGIDURIA)
                        ->orWhere('candidate_ines.postulate', CandidateIne::SINDICATURA);
                })
                ->orderBy('candidate_ines.postulate')
                ->orderBy('candidate_ines.number_line')
                ->with("postulate_data")
                ->get();

        } else {
            $data = FieldsExcelReport::INE;
            $data_alternate = FieldsExcelReport::INE_ALTERNATE;
            $candidatesResult = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                ->select('candidate_ines.*', 'candidates.user_id')
                ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                ->where(function ($q) {
                    $q->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA)
                        ->orWhere('candidate_ines.postulate', CandidateIne::SINDICATURA)
                        ->orWhere('candidate_ines.postulate', CandidateIne::REGIDURIA);
                })
                ->orderBy('candidate_ines.postulate')
                ->orderBy('candidate_ines.number_line')
                ->with("postulate_data")
                ->get();
        }

        $districts = $request->get('district');
        foreach ($districts as $district) {
            foreach ($candidatesResult as $candidate) {
                if ($district['id'] === $candidate->postulate_data->id) {
                    $candidates[] = $candidate;
                }
            }
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
                } elseif ($key == 'Tipo de residencia en meses|' || $key == 'RESIDENCIA_MESES_SUPLENCIA') {
                    $data_excel[$i][$key] = "";
                } elseif ($key == 'Fecha de nacimiento|') {
                    $date = date("d-m-Y", strtotime($candidate[$value]));
                    $data_excel[$i][$key] = $date;
                } elseif ($key == 'Correo electr??nico|') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                    $data_excel[$i][$key] = "morenasnr@gmail.com";
                } elseif ($key == 'Confirmaci??n de correo electr??nico|') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                    $data_excel[$i][$key] = "morenasnr@gmail.com";
                } elseif ($key == 'CONFIRMACI??N_CORREO_SUPLENCIA|') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                    $data_excel[$i][$key] = "morenasnr@gmail.com";
                } elseif ($key == 'FECHA_NACIMIENTO_SUPLENCIA|') {
                    $date = date("d-m-Y", strtotime($candidate[$value]));
                    $data_excel[$i][$key] = $date;
                } elseif ($key == 'Sexo' || $key == 'SEXO') {
                    $data_excel[$i][$key] = $candidate[$value] === 'HOMBRE' ? 'H' : 'M';
                } elseif ($key == 'Sexo|' || $key == 'SEXO_SUPLENCIA|') {
                    $data_excel[$i][$key] = $candidate[$value] === 'HOMBRE' ? 'H' : 'M';
                } elseif ($key == 'Tipo candidatura' || $key == 'TIPO_CANDIDATURA') {
                    $reportCandidate = [
                        "1" => 8,
                        "2" => 7,
                        "3" => 28,
                        "4" => 26,
                        "5" => 9,
                    ];
                    $data_excel[$i][$key] = $reportCandidate[$candidate[$value]];
                } else {
                    $data_excel[$i][$key] = $candidate[$value];
                }
            }

            //ALTERNATE DATA
            if (!is_null($candidate->alternate)) {
                foreach ($data_alternate as $key => $value) {
                    if ($key == 'Registra suplencia|') {
                        $data_excel[$i][$key] = 1;
                    } elseif ($key == 'Tipo de residencia en meses|' || $key == 'RESIDENCIA_MESES_SUPLENCIA') {
                        $data_excel[$i][$key] = "";
                    } elseif ($key == 'Fecha de nacimiento|') {
                        $date = date("d-m-Y", strtotime($candidate->alternate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Correo electr??nico|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                        $data_excel[$i][$key] = "morenasnr@gmail.com";
                    } elseif ($key == 'Confirmaci??n de correo electr??nico|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                        $data_excel[$i][$key] = "morenasnr@gmail.com";
                    } elseif ($key == 'CONFIRMACI??N_CORREO_SUPLENCIA|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                        $data_excel[$i][$key] = "morenasnr@gmail.com";
                    } elseif ($key == 'FECHA_NACIMIENTO_SUPLENCIA|') {
                        $date = date("d-m-Y", strtotime($candidate->alternate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Sexo|' || $key == 'SEXO_SUPLENCIA|') {
                        $data_excel[$i][$key] = $candidate->alternate[$value] === 'HOMBRE' ? 'H' : 'M';
                    } else {
                        $data_excel[$i][$key] = $candidate->alternate[$value];
                    }
                }
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

    public function createSpecialReportINE(Request $request)
    {
        $rules = [
            'type' => 'required'
        ];

        $this->validate($request, $rules);

        $data_excel = [];
        $array_key_alternate = [];


        if ($request->has('politic_party_id')) {
            if ($request->all()['type'] == 1) {
                $data = FieldsExcelReport::INE;
                $data_alternate = FieldsExcelReport::INE_ALTERNATE;
                $candidates = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                    ->select('candidate_ines.*', 'candidates.user_id')
                    ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                    ->where(function ($q) {
                        $q->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_MR)
                            ->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_RP)
                            ->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA);
                    })
                    ->where('candidate_ines.politic_party_id', $request->all()['politic_party_id'])
                    ->skipFields('Pendiente', -1)
                    ->orderBy('candidate_ines.postulate')
                    ->orderBy('candidate_ines.number_line')
                    ->get();
            } else {
                $data = FieldsExcelReport::INE_2;
                $data_alternate = FieldsExcelReport::INE_2_ALTERNATE;
                $candidates = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                    ->select('candidate_ines.*', 'candidates.user_id')
                    ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                    ->where(function ($q) {
                        $q->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA)
                            ->orWhere('candidate_ines.postulate', CandidateIne::REGIDURIA);
                    })
                    ->where('candidate_ines.politic_party_id', $request->all()['politic_party_id'])
                    ->skipFields('Pendiente', -1)
                    ->orderBy('candidate_ines.number_line')
                    ->orderBy('candidate_ines.type_postulate')
                    ->get();

                $candidates_aux = $candidates->groupBy('postulate_id');
                $candidates_aux->all();

                $candidates = collect();
                foreach ($candidates_aux as $candidate) {
                    $candidates = $candidates->toBase()->merge($candidate);
                }
            }
        } else {
            if ($request->all()['type'] == 1) {
                $data = FieldsExcelReport::INE;
                $data_alternate = FieldsExcelReport::INE_ALTERNATE;
                $candidates = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                    ->select('candidate_ines.*', 'candidates.user_id')
                    ->where(function ($q) {
                        $q->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                            ->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_MR)
                            ->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_RP)
                            ->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA);
                    })
                    ->skipFields('Pendiente', -1)
                    ->orderBy('candidate_ines.postulate')
                    ->orderBy('candidate_ines.number_line')
                    ->get();

            } else {
                $data = FieldsExcelReport::INE_2;
                $data_alternate = FieldsExcelReport::INE_2_ALTERNATE;
                $candidates = CandidateIne::join('candidates', 'candidate_ines.origin_candidate_id', '=', 'candidates.id')
                    ->select('candidate_ines.*', 'candidates.user_id')
                    ->where('candidate_ines.type_postulate', CandidateIne::OWNER)
                    ->where(function ($q) {
                        $q->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA)
                            ->orWhere('candidate_ines.postulate', CandidateIne::REGIDURIA);
                    })
                    ->skipFields('Pendiente', -1)
                    ->orderBy('candidate_ines.number_line')
                    ->orderBy('candidate_ines.type_postulate')
                    ->get();

                $candidates_aux = $candidates->groupBy('postulate_id');
                $candidates_aux->all();

                $candidates = collect();
                foreach ($candidates_aux as $candidate) {
                    $candidates = $candidates->toBase()->merge($candidate);
                }
            }
        }

        $i = 0;
        foreach ($candidates as $candidate) {
            //OWNER DATA
            foreach ($data as $key => $value) {
                if ($key == 'Distrito') {
                    $postulate = Postulate::find($candidate[$value]);
                    $data_excel[$i][$key] = $postulate->district;
                } elseif ($key == 'Municipio' || $key == 'MUNICIPIO') {
                    $postulate = Postulate::find($candidate->postulate_id);
                    $data_excel[$i][$key] = $postulate->municipality;
                } elseif ($key == 'Correo electr??nico' || $key == 'CORREO_ELECTR??NICO') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                } elseif ($key == 'Confirmaci??n correo electronico' || $key == 'CONFIRMACI??N_CORREO') {
                    $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                } elseif ($key == 'Tipo de residencia en meses' || $key == 'TIEMPO_RESIDENCIA_MESES') {
                    $data_excel[$i][$key] = "";
                } elseif ($key == 'Tipo candidatura' || $key == 'TIPO_CANDIDATURA') {
                    $reportCandidate = [
                        "1" => 8,
                        "2" => 7,
                        "3" => 28,
                        "4" => 26,
                        "5" => 9,
                    ];
                    $data_excel[$i][$key] = $reportCandidate[$candidate[$value]];
                } elseif ($key == 'Fecha de nacimiento' || $key == 'FECHA_NACIMIENTO') {
                    $date = date("d-m-Y", strtotime($candidate[$value]));
                    $data_excel[$i][$key] = $date;
                } elseif ($key == 'Sexo' || $key == 'SEXO') {
                    $data_excel[$i][$key] = $candidate[$value] === 'HOMBRE' ? 'H' : 'M';
                } else {
                    $data_excel[$i][$key] = $candidate[$value];
                }
            }

            //ALTERNATE DATA
            if (!is_null($candidate->alternate)) {
                foreach ($data_alternate as $key => $value) {
                    if ($key == 'Registra suplencia|') {
                        $data_excel[$i][$key] = 1;
                    } elseif ($key == 'Tipo de residencia en meses|' || $key == 'RESIDENCIA_MESES_SUPLENCIA') {
                        $data_excel[$i][$key] = "";
                    } elseif ($key == 'Fecha de nacimiento|') {
                        $date = date("d-m-Y", strtotime($candidate->alternate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Correo electr??nico|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                    } elseif ($key == 'Confirmaci??n de correo electr??nico|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                    } elseif ($key == 'CONFIRMACI??N_CORREO_SUPLENCIA|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                    } elseif ($key == 'FECHA_NACIMIENTO_SUPLENCIA|' || $key == "Fecha de nacimiento|") {
                        $date = date("d-m-Y", strtotime($candidate->alternate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Sexo|' || $key == 'SEXO_SUPLENCIA|') {
                        $data_excel[$i][$key] = $candidate->alternate[$value] === 'HOMBRE' ? 'H' : 'M';
                    } else {
                        $data_excel[$i][$key] = $candidate->alternate[$value];
                    }
                }
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

    public function getReportFinal(Request $request)
    {
        $rules = [
            'type' => 'required'
        ];

        $this->validate($request, $rules);

        $candidates_all = collect();
        $candidates = CandidateIne::joinCandidates()->getOwner()->skipFields('Pendiente', -1);

        if ($request->has('politic_party_id')) {
            $candidates = $candidates->where('candidate_ines.politic_party_id', $request->all()['politic_party_id']);
        }

        if ($request->has('today')) {
            $candidates = $candidates->whereDate('candidate_ines.created_at', Carbon::today());
        }

        if ($request->has('user_id')) {
            $candidates = $candidates->where('user_id', $request->all()['user_id']);
        }

        if ($request->has('districts')) {
            $candidates = $candidates->filterDistrict($request->all()['districts']);
        }

        switch ($request->all()['type']) {
            case CandidateIne::REPORT_TYPE_1:
                $data = FieldsExcelReport::INE;
                $data_alternate = FieldsExcelReport::INE_ALTERNATE;

                $candidates = $candidates->where(function ($q) {
                    $q->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_MR)
                        ->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_RP)
                        ->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA);
                });
                break;
            case CandidateIne::REPORT_TYPE_2:
                $data = FieldsExcelReport::INE_2;
                $data_alternate = FieldsExcelReport::INE_2_ALTERNATE;

                $candidates = $candidates->where(function ($q) {
                    $q->orWhere('candidate_ines.postulate', CandidateIne::SINDICATURA)
                        ->orWhere('candidate_ines.postulate', CandidateIne::REGIDURIA);
                });
                break;
            case CandidateIne::REPORT_TYPE_3:
                $data = FieldsExcelReport::INE;
                $data_alternate = FieldsExcelReport::INE_ALTERNATE;

                $candidates = $candidates->where(function ($q) {
                    $q->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_RP)
                        ->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_MR);
                });
                break;
            case CandidateIne::REPORT_TYPE_4:
                $data = FieldsExcelReport::INE_2;
                $data_alternate = FieldsExcelReport::INE_2_ALTERNATE;
                $candidates = $candidates->where(function ($q) {
                    $q->orWhere('candidate_ines.postulate', CandidateIne::SINDICATURA)
                        ->orWhere('candidate_ines.postulate', CandidateIne::REGIDURIA)
                        ->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA);
                });
                break;
            case CandidateIne::REPORT_TYPE_5:
                $data = FieldsExcelReport::INE_2;
                $data_alternate = FieldsExcelReport::INE_2_ALTERNATE;

                $candidates = $candidates->where(function ($q) {
                    $q->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA)
                        ->orWhere('candidate_ines.postulate', CandidateIne::REGIDURIA);
                });
                break;
            case CandidateIne::REPORT_TYPE_6:
                $data = FieldsExcelReport::INE_2;
                $data_alternate = FieldsExcelReport::INE_2_ALTERNATE;
                $candidates = $candidates->where(function ($q) {
                    $q->orWhere('candidate_ines.postulate', CandidateIne::SINDICATURA)
                        ->orWhere('candidate_ines.postulate', CandidateIne::REGIDURIA)
                        ->orWhere('candidate_ines.postulate', CandidateIne::PRESIDENCIA)
                        ->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_MR)
                        ->orWhere('candidate_ines.postulate', CandidateIne::DIPUTACION_RP);
                });
                break;
            case CandidateIne::REPORT_TYPE_7:
                $data = FieldsExcelReport::INE;
                $data_alternate = FieldsExcelReport::INE_ALTERNATE;

                $candidates = $candidates->where('candidate_ines.postulate', CandidateIne::DIPUTACION_MR);
                break;
            case 8:
                $data = FieldsExcelReport::INE;
                $data_alternate = FieldsExcelReport::INE_ALTERNATE;

                $candidates = $candidates->where('candidate_ines.postulate', CandidateIne::PRESIDENCIA);
                break;
        }

        $candidates = $candidates->orderBy('candidate_ines.number_line')
            ->orderBy('candidate_ines.type_postulate')
            ->get();

        $candidates_aux = $candidates->groupBy('postulate_id');
        $candidates_aux->all();

        $candidates = collect();
        foreach ($candidates_aux as $candidate) {
            $candidates = $candidates->toBase()->merge($candidate);
        }

        $i = 0;
        foreach ($candidates as $candidate) {
            if (!is_null($candidate->alternate)) {
                //OWNER DATA
                foreach ($data as $key => $value) {
                    if ($key == 'Distrito') {
                        $postulate = Postulate::find($candidate[$value]);
                        if (!is_null($postulate)) {
                            $data_excel[$i][$key] = $postulate->district;
                        } else {
                            $data_excel[$i][$key] = '';
                        }
                    } elseif ($key == 'Municipio' || $key == 'MUNICIPIO') {
                        $postulate = Postulate::find($candidate->postulate_id);
                        if (!is_null($postulate)) {
                            $data_excel[$i][$key] = $postulate->municipality;
                        } else {
                            $data_excel[$i][$key] = '';
                        }
                    } elseif ($key == 'Correo electr??nico' || $key == 'CORREO_ELECTR??NICO') {
                        $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                    } elseif ($key == 'Confirmaci??n correo electronico' || $key == 'CONFIRMACI??N_CORREO') {
                        $data_excel[$i][$key] = mb_strtolower($candidate[$value]);
                    } elseif ($key == 'Tipo de residencia en meses' || $key == 'TIEMPO_RESIDENCIA_MESES') {
                        $data_excel[$i][$key] = "";
                    } elseif ($key == 'Tipo candidatura' || $key == 'TIPO_CANDIDATURA') {
                        $reportCandidate = [
                            "1" => 8,
                            "2" => 7,
                            "3" => 28,
                            "4" => 26,
                            "5" => 9,
                        ];
                        $data_excel[$i][$key] = $reportCandidate[$candidate[$value]];
                    } elseif ($key == 'Fecha de nacimiento' || $key == 'FECHA_NACIMIENTO') {
                        $date = date("d-m-Y", strtotime($candidate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Sexo' || $key == 'SEXO') {
                        $data_excel[$i][$key] = $candidate[$value] === 'HOMBRE' ? 'H' : 'M';
                    } elseif ($key == 'PARTIDO' || $key == 'Partido') {
                        $politic_party = PoliticParty::find($candidate->politic_party_id);
                        $data_excel[$i][$key] = $politic_party->name;
                    } else {
                        $data_excel[$i][$key] = $candidate[$value];
                    }
                }

                //ALTERNATE DATA
                foreach ($data_alternate as $key => $value) {
                    if ($key == 'Registra suplencia|') {
                        $data_excel[$i][$key] = 1;
                    } elseif ($key == 'Tipo de residencia en meses|' || $key == 'RESIDENCIA_MESES_SUPLENCIA') {
                        $data_excel[$i][$key] = "";
                    } elseif ($key == 'Fecha de nacimiento|') {
                        $date = date("d-m-Y", strtotime($candidate->alternate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Correo electr??nico|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
//                        $data_excel[$i][$key] = "morenasnr@gmail.com";
                    } elseif ($key == 'Confirmaci??n de correo electr??nico|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
//                        $data_excel[$i][$key] = "morenasnr@gmail.com";
                    } elseif ($key == 'CONFIRMACI??N_CORREO_SUPLENCIA|') {
                        $data_excel[$i][$key] = mb_strtolower($candidate->alternate[$value]);
                    } elseif ($key == 'FECHA_NACIMIENTO_SUPLENCIA|' || $key == "Fecha de nacimiento|") {
                        $date = date("d-m-Y", strtotime($candidate->alternate[$value]));
                        $data_excel[$i][$key] = $date;
                    } elseif ($key == 'Sexo|' || $key == 'SEXO_SUPLENCIA|') {
                        $data_excel[$i][$key] = $candidate->alternate[$value] === 'HOMBRE' ? 'H' : 'M';
                    } elseif ($key == 'Correo electr??nico|' || $key == 'Confirmaci??n de correo electr??nico|' || $key == 'CORREO_ELECTR??NICO_SUPLENCIA|' || $key == 'CONFIRMACI??N_CORREO_SUPLENCIA|') {
//                        $data_excel[$i][$key] = "morenasnr@gmail.com";;
                    } else {
                        $data_excel[$i][$key] = $candidate->alternate[$value];
                    }
                }
                $i++;
            }
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

    public function replaceGender()
    {
        $candidateInes = CandidateIne::whereNull('gender')->get();

        foreach ($candidateInes as $candidateIne) {
            if (!is_null($candidateIne->curp)) {
                if ($candidateIne->curp[10] == 'M') {
                    $candidateIne->gender = 'MUJER';
                } else {
                    $candidateIne->gender = 'HOMBRE';
                }

                $candidateOrigin = $candidateIne->originCandidate;
                $candidateIne->save();

                if (!is_null($candidateOrigin)) {
                    $candidateOrigin->gender = $candidateIne->gender;
                    $candidateOrigin->save();
                }
            }
        }
    }
}
