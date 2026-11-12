<?php

namespace App\Http\Controllers;

use App\Http\Requests\BasketRequest;
use App\Models\Basket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
        ->join('nomenclatures', 'nomenclatures.guid', '=', 'baskets.nomenclature_guid')
        ->join('brands', 'brands.guid', '=', 'nomenclatures.brand_guid')
        ->select('nomenclatures.guid as nomenclature_guid', 'nomenclatures.full_name', 'nomenclatures.set_number', 'nomenclatures.price', 'brands.name', 'brands.guid as brand_guid', 'baskets.amount')
        ->withCasts(['amount' => 'float', 'price' => 'float'])
        ->paginate($per_page);
        
    }

    public function add(BasketRequest $request)
    {
        $userId = $request->user()->id;
        $rows = [];
        foreach ($request->validated()['basket'] as $item) {
            $rows[] = [
                'nomenclature_guid' => $item['nomenclature_guid'],
                'amount' => $item['amount'],
                'created_by' => $userId,
            ];
        }

        Basket::upsert($rows, ['nomenclature_guid', 'created_by'], ['amount']);

        return response(['message' => 'ok'], 200);
    }

    public function delete(Request $request)
    {
        $currentUser = Auth::user();

        $rawobjects = $request->all();

        try {

        foreach($rawobjects as $objects)
        {
            foreach($objects as $object)
            {
                Basket::where('created_by', '=', $currentUser['id'])
                ->where('nomenclature_guid', '=', $object)
                ->delete();
                
            }
        }
             } catch (\Exception $e) {
                return response(['message' => $e->getMessage()], 500);
             }
            return response(['message' => 'ok'], 200);
    }
    public function clear()
    {
        $currentUser = Auth::user();

        try {

            Basket::where('created_by', '=', $currentUser['id'])
                ->delete();

             } catch (\Exception $e) {
                return response(['message' => $e->getMessage()], 500);
             }
            return response(['message' => 'ok'], 200);
    }
}
