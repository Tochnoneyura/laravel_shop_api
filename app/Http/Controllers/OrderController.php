<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Order_item;

class OrderController extends Controller
{
    public function index(OrderFilter $filters, Request $request)
    {
        
    }

    public function submit(OrderRequest $request)
    {
        $data = $request->all();
        $currentUser = Auth::user();

        try {

         $order =  Order::create([
                'date' => now(),
                'is_processed' => false,
                //'total' =>,
                'payment_status' => 'НеОплачен',
                'status' => '4454a860-1b1e-4020-83c4-beaede97e3ec',
                'delivery_method' => $data['delivery_method'],
                'delivery_date' => $data['delivery_date'],
                'delivery_address' => $data['delivery_address'],
                'delivery_company' => $data['delivery_company'],
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'website_comment' => $data['website_comment'],
                'website_comment_for_client' => null,
                'latest_update_by_client' => now(),
                'payment_type' => 'Безналичная',
                'is_delivery_today' => $data['is_delivery_today'],
                'created_by' => $currentUser['id'],
            ]);


            foreach($data['products'] as $product)
            {
                Order_item::create([

                    'order_id' => $order['id'],
                    //'amount' =>,
                    'nomenclature_guid' => $product,
                    //'price' =>
                    'discount' => 0,
                ]);

            }
            

        }  catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
        return response(['message' => 'ok'], 200);
    }
}
