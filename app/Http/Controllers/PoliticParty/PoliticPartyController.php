<?php

namespace App\Http\Controllers\PoliticParty;

use App\Http\Controllers\ApiController;
use App\PoliticParty;

class PoliticPartyController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->showAll(PoliticParty::all());
    }
}
