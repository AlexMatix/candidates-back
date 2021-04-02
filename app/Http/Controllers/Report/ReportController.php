<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{

    public function getFinalReport()
    {
        $header =[
            'DISTRITO',
            'No_CARGO',
            'CARGO',
            'APELLIDO_PATERNO',
            'APELLIDO_MATERNO',
            'NOMBRE',
            'SOBRENOMBRE',
            'VIALIDAD',
            'NOMBRE_VIALIDAD',
            'NUM_EXTERIOR',
            'NUM_INTERIOR',
            'COLONIA',
            'CODIGO_POSTAL',
            'MUNICIPIO',
            'CLAVE DE ELECTOR',
            'OCR',
            'CIC',
            'EMISION',
            'ENTIDAD',
            'SECCION',
            'FECHA_NACIMIENTO',
            'TIEMPO_RESIDECIA_AÑOS',
            'TIEMPO_RESIDECIA_MESES',
            'OCUPACION',
            'REELECION'

        ];
    }
}
