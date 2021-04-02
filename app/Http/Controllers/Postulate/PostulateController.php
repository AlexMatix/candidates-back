<?php

namespace App\Http\Controllers\Postulate;

use App\Postulate;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class PostulateController extends ApiController
{

    public function index()
    {
        return $this->showAll(Postulate::all());
    }

    public function show(Postulate $postulate)
    {
        return $this->showOne($postulate);
    }


    public function update(Request $request, Postulate $postulate)
    {

    }

    public function getMunicipalities()
    {
        $postulates = Postulate::all();
        $districts = [];
        $municipalities = [];
        foreach ($postulates as $postulate) {
            $districts[] = $postulate->district;
            $municipalities[$postulate->district][$postulate->id] = [
                'name' => $postulate->municipality,
                'id' => $postulate->id,
                'presidency' => $postulate->presidency,
                'regidurias' => $postulate->regidurias,
                'sindicaturas' => $postulate->sindicaturas,
            ];
        }

        return $this->showList([
            'districts' => $districts,
            'municipalities' => $municipalities
        ]);

    }
}
