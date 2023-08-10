<?php

namespace App\Http\Controllers;

use App\Models\Brand;
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


        return User::withTrashed()
        ->filter($filters)
        ->OrderBy('users.id')
        ->distinct('users.id')
        ->paginate($per_page);
        
    }
}
