<?php

namespace App\Http\Controllers;

use App\Http\Requests\BasketRequest;
use App\Models\Basket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class BasketController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        if($request->get('per_page') === 0) {
            $per_page = Basket::count();
        } else {
            $per_page = $request->get('per_page') ?? 20;
            }


        return Basket::where('created_by', '=', $currentUser['id'])
        ->select('nomenclature_guid', 'amount')
        ->paginate($per_page);
        
    }

    public function add(Request $request)
    {
        $currentUser = Auth::user();
        $rawobjects = $request->all();
        
        try {
            foreach($rawobjects as $objects) {

                    foreach($objects as $object) {
                        $validator = Validator::make($object,
                        [
                           'nomenclature_guid' => ['required', 'exists:nomenclatures,guid'],
                           'amount' => ['nullable', 'numeric'],
                        ]);
                        if($validator->fails()) {
                            return response()->json(['error' => $validator->errors()->toJson(JSON_UNESCAPED_UNICODE)], 400);
                        }
                        $object['created_by'] = $currentUser['id'];
                        
                    }
                    
             }
            
             Basket::upsert($objects, ['nomenclature_guid', 'created_by'], ['amount']);
             

        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
        return response(['message' => 'ok'], 200);
    }
}
