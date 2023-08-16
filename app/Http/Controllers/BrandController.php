<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Filters\BrandFilter;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(BrandFilter $filters, Request $request)
    {
        if($request->get('per_page') === 0) {
            $per_page = Brand::count();
        } else {
            $per_page = $request->get('per_page') ?? 20;
        }


        return Brand::filter($filters)
        ->OrderBy('brands.guid')
        ->distinct('brands.guid')
        ->paginate($per_page);
        
    }
}
