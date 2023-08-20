<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Basket;
use App\Models\OrderItem;
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
            $per_page = Order::count();
        } else {
            $per_page = $request->get('per_page') ?? 20;
        }

        $currentUser = Auth::user();

        switch(true)
        {
            case($currentUser['role'] === 'customer'):

                $request->remove('users');

                return Order::where('orders.created_by', '=', $currentUser['id'])
                ->filter($filters)
                ->orderBy('orders.date')
                ->paginate($per_page);
            
            case($currentUser['role'] === 'admin'):
                return Order::filter($filters)
                ->orderBy('orders.date')
                ->paginate($per_page);
        }
    }

    public function show($id)
    {

        $order = Order::findOrFail($id);
        $currentUser = Auth::user();

        

        if(($currentUser['role'] === 'customer') && ($currentUser['id'] !== $order['created_by'])){
        
            return response()->json(['error' => 'Admin check failed'], 401);
        }
        
        return Order::where('orders.id', '=', $id)
        ->with(['order_item' => function ($query) {
            $query->join('nomenclatures', 'order_items.nomenclature_guid', '=', 'nomenclatures.guid')
            ->join('brands', 'nomenclatures.brand_guid', '=', 'brands.guid')
            ->select('order_items.*', 'nomenclatures.code', 'nomenclatures.name', 'nomenclatures.full_name', 'nomenclatures.set_number', 'brands.guid as brand_guid', 'brands.name as brand_name');
        }])
        ->first();
    }

    public function submit(OrderRequest $request)
    {
        $data = $request->all();
        $currentUser = Auth::user();

        try {

         $order =  Order::create([
                'date' => $data['date'],
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
                


                OrderItem::create([

                    'order_id' => $order['id'],
                    'amount' => $basket->amount,
                    'nomenclature_guid' => $product,
                    'price' => $nomenclature->price,
                    'discount' => 0,
                ]);

            }
            

            $orderItems = OrderItem::where('order_id', '=', $order['id'])->get();
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

    public function update(OrderRequest $request, $id)
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
                    $order->updated_at = now();
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
