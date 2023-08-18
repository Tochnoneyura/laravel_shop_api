<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Basket;
use App\Models\Order_item;
use App\Filters\OrderFilter;
use App\Models\Nomenclature;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(OrderFilter $filters, Request $request)
    {

        if($request->get('per_page') === 0) {
            $per_page = User::count();
        } else {
            $per_page = $request->get('per_page') ?? 20;
        }

        $currentUser = Auth::user();

        switch(true)
        {
            case($currentUser['role'] === 'customer'):
                return Order::where('orders.created_by', '=', $currentUser['id'])
                ->filter($filters)
                ->OrderBy('orders.date')
                ->paginate($per_page);
            
            case($currentUser['role'] === 'admin'):
                return Order::filter($filters)
                ->OrderBy('orders.date')
                ->paginate($per_page);
        }
    }

    public function show($id)
    {

        $order = Order::findOrFail($id);
        $currentUser = Auth::user();

        

        if(($currentUser['role'] === 'customer') && ($currentUser['id'] !== $order['created_by']))
        {
            return response()->json(['error' => 'Admin check failed'], 401);
        }
        
        return Order::where('orders.id', '=', $id)
        //->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->first();
    }

    public function submit(OrderRequest $request)
    {
        $data = $request->all();
        $currentUser = Auth::user();

        try {

         $order =  Order::create([
                'date' => now(),
                'is_processed' => false,
                'total' => 0,
                'payment_status' => 'НеОплачен',
                'status' => '4454a860-1b1e-4020-83c4-beaede97e3ec',
                'delivery_method' => $data['delivery_method'],
                'delivery_date' => now(),
                'delivery_address' => $data['delivery_address'],
                'delivery_company' => $data['delivery_company'],
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'website_comment' => $data['website_comment'],
                'website_comment_for_client' => null,
                'latest_update_by_client' => now(),
                'payment_type' => $data['payment_type'],
                'is_delivery_today' => $data['is_delivery_today'],
                'created_by' => $currentUser['id'],
            ]);


            foreach($data['products'] as $product)
            {
                $nomenclature = Nomenclature::where('guid', '=', $product)->first();

                $basket = Basket::where('created_by', '=', $currentUser['id'])
                ->where('nomenclature_guid', '=', $product)->first();
                


                Order_item::create([

                    'order_id' => $order['id'],
                    'amount' => $basket->amount,
                    'nomenclature_guid' => $product,
                    'price' => $nomenclature->price,
                    'discount' => 0,
                ]);

            }
            

            $orderItems = Order_item::where('order_id', '=', $order['id'])->get();
            $total = 0;
            

            foreach($orderItems as $orderItem)
            {
                $total = $total + ($orderItem['price'] * $orderItem['amount']) * ((100 - $orderItem['discount']) / 100);
                
            }
            
        
            $order->total = $total;
            $order->save();

        }  catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
        return response(['message' => 'ok'], 200);
    }

    public function update(OrderRequest $request, $id) //пока не работает, надо менять реквест
    {
        $data = $request->all();
        $currentUser = Auth::user();
        $order = Order::findOrFail($id);

        try {
        
            switch(true)
            {
                case(($currentUser['role'] === 'customer') && ($currentUser['id'] !== $order['created_by'])):

                    return response()->json(['error' => 'Admin check failed'], 401);

                    case(($currentUser['role'] === 'customer') && ($currentUser['id'] === $order['created_by'])):

                        $order->website_comment = $data['website_comment'];
                        $order->save();
                        break;

                        case(($currentUser['role'] === 'admin') && ($currentUser['id'] === $order['created_by'])):

                            $order->status = $data['status'] ?? $order->status;
                            $order->payment_status = $data['payment_status'] ?? $order->payment_status;
                            $order->website_comment_for_client = $data['website_comment_for_client'] ?? $order->website_comment_for_client;
                            $order->save();
                            break;

            }
        }  catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
        return response(['message' => 'ok'], 200);



    }
}
