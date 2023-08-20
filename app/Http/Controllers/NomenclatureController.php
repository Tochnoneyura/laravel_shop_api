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
        ->orderBy('nomenclatures.guid')
        ->distinct('nomenclatures.guid')
        ->join('brands', 'nomenclatures.brand_guid', '=', 'brands.guid')
        ->select('nomenclatures.*', 'brands.name as brand_name', 'brands.main_brand_guid')
        ->paginate($per_page);
        
    
    }
    
}
