<?php

namespace App\Http\Controllers;

use App\Filters\NomenclatureFilter;
use App\Models\nomenclature;
use Illuminate\Http\Request;

class NomenclatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(NomenclatureFilter $filters, Request $request)
    {
        if($request->get('per_page') === 0) {
            $per_page = Nomenclature::count();
        } else {
            $per_page = $request->get('per_page') ?? 20;
        }


        return Nomenclature::filter($filters)
        ->OrderBy('nomenclatures.guid')
        ->distinct('nomenclatures.guid')
        ->paginate($per_page);
        
    
    }
    
}
