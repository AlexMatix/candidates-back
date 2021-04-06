<?php

namespace App\Http\Controllers\Postulate;

use App\Candidate;
use App\PoliticParty;
use App\Postulate;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;

class PostulateController extends ApiController
{

    public function index()
    {
        return $this->showAll(Postulate::all());
    }

    public function show(Postulate $postulate)
    {
        $user = Auth::user();
        $politic_party = PoliticParty::findOrFail($user->politic_party_id);

        return $this->showList([
            'presidency' => Candidate::where([
                'postulate_id', '=', $postulate->id,
                'politic_party_id', '=', $politic_party->id,
                'postulate', '=', Postulate::PRESIDENCIA,
            ])->get(),

            'regidurias' => Candidate::where([
                'postulate_id', '=', $postulate->id,
                'politic_party_id', '=', $politic_party->id,
                'postulate', '=', Postulate::REGIDURIA,
            ])->get(

            ),
            'sindicaturas' => Candidate::where([
                'postulate_id', '=', $postulate->id,
                'politic_party_id', '=', $politic_party->id,
                'postulate', '=', Postulate::SINDICATURA,
            ])->get(),

        ]);
    }


    public function getMunicipalities()
    {
        $postulates = Postulate::all();
        $districts = [];
        $municipalities = [];
        foreach ($postulates as $postulate) {
            if (!in_array($postulate->district, $districts)) {
                $districts[] = $postulate->district;
            }

            $municipalities[$postulate->district][] = [
                'name' => $postulate->municipality,
                'id' => $postulate->id,
                'presidency' => $postulate->presidency,
                'regidurias' => $postulate->regidurias,
                'sindicaturas' => $postulate->sindicaturas,
            ];
        }
        $districts = array_unique($districts, SORT_REGULAR);
        sort($districts);
        return $this->showList([
            'districts' => $districts,
            'municipalities' => $municipalities
        ]);

    }
}
