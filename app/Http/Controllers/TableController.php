<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\DocumentStatus;
use App\Models\Nomenclature;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    public function import(Request $request, $table)
    {
        $objects = $request->all();

        try {
            switch(true)
        {
            case($table === 'nomenclature'):

                foreach($objects as $object) {
                    
                   $validator = Validator::make($object,
                        [
                            'guid' => ['required', 'max:36', 'string', 'unique:nomenclatures,guid'],
                            'code' => ['required', 'string', 'unique:nomenclatures,code'],
                            'name' => ['required', 'max:100', 'string'],
                            'full_name' => ['required', 'string'],
                            'set_number' => ['required', 'max:25', 'string'],
                            'brand_guid' => ['required', 'max:36', 'string'],
                            'price' => ['numeric', 'min:0', 'nullable'],
                        ]);
                        if($validator->fails()) {
                            return response()->json(['error' => $validator->errors()->toJson(JSON_UNESCAPED_UNICODE)], 400);
                        }
                }
                Nomenclature::upsert($objects, 'guid', ['code', 'name', 'full_name', 'set_number', 'brand_guid', 'price']);
                break;

            case(($table === 'document_statuses') || ($table === 'document-statuses')):

                foreach($objects as $object) {
                    
                    $validator = Validator::make($object,
                         [
                            'guid' => ['required', 'string', 'max:36', 'unique:document_statuses,guid'],
                            'name' => ['required', 'string', 'max:50'],
                         ]);
                         if($validator->fails()) {
                             return response()->json(['error' => $validator->errors()->toJson(JSON_UNESCAPED_UNICODE)], 400);
                         }
                 }
                 DocumentStatus::upsert($objects, 'guid', ['name']);

                break;
                  
            case($table === 'brands'):
                foreach($objects as $object) {
                    
                    $validator = Validator::make($object,
                         [
                            'guid' => ['required', 'max:36', 'string', 'unique:brands,guid'],
                            'name' => ['required', 'max:25', 'string', 'unique:brands,name'],
                            'main_brand_guid' => ['max:36', 'string', 'nullable'],
                         ]);
                         if($validator->fails()) {
                             return response()->json(['error' => $validator->errors()->toJson(JSON_UNESCAPED_UNICODE)], 400);
                         }
                 }
                 Brand::upsert($objects, 'guid', ['name', 'main_brand_guid' ]);
                
                break;
            
            default: 
                return response(['message' => 'invalid data'], 400); 
        }

        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
        return response(['message' => 'ok'], 200);
        
        
    } 
}
