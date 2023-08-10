<?php

namespace App\Http\Controllers;

use App\Models\Nomenclature;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function import(Request $request, $table)
    {
        $objects[] = json_decode($request, true);

        try {
            switch(true)
        {
            case($table === 'nomenclatures'):

                foreach($objects as $object) {
                    
                    $validatedObject[] = $this->validate($object,
                        [
                            'guid' => ['required', 'max:36', 'string', 'unique:nomenclatures,guid'],
                            'code' => ['required', 'max:1', 'string', 'unique:nomenclatures,code'],
                            'name' => ['required', 'max:100', 'string'],
                            'full_name' => ['required', 'string'],
                            'set_number' => ['required', 'max:25', 'string'],
                            'brand_guid' => ['required', 'max:36', 'string'],
                            'price' => ['required', 'numeric', 'min:0'],
                        ]);
                }

                Nomenclature::upsert($validatedObject, 'guid', ['code', 'name', 'full_name', 'set_number', 'brand_guid', 'price']);
                break;

            case($table === 'document_statuses'):
                break;
                  
            case($table === 'brands'):
                break;
        }

        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
        return response(['message' => 'ok'], 200);
        
        
    } 
}
