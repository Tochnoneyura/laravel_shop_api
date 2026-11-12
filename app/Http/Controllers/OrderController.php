<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Basket;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['per_page' => ['sometimes', 'integer', 'between:1,100']]);

        return Order::where('created_by', $request->user()->id)
            ->with('order_item')
            ->latest('id')
            ->paginate($data['per_page'] ?? 20);
    }

    public function submit(OrderRequest $request)
    {
        $data = $request->safe()->except(['products', 'website_comment_for_client']);
        $products = $request->validated()['products'];
        $userId = $request->user()->id;

        $order = DB::transaction(function () use ($data, $products, $userId) {
            $lines = Basket::where('baskets.created_by', $userId)
                ->whereIn('baskets.nomenclature_guid', $products)
                ->join('nomenclatures', 'nomenclatures.guid', '=', 'baskets.nomenclature_guid')
                ->where('baskets.amount', '>', 0)
                ->where('nomenclatures.price', '>=', 0)
                ->get([
                    'baskets.nomenclature_guid',
                    'baskets.amount',
                    'nomenclatures.price',
                    DB::raw('sum(round(baskets.amount * nomenclatures.price, 2)) over () as order_total'),
                ]);

            if ($lines->count() !== count($products)) {
                throw ValidationException::withMessages([
                    'products' => 'Each product must have a positive quantity in your basket and a valid price.',
                ]);
            }

            $order = Order::create($data + [
                'date' => now()->toDateString(),
                'total' => $lines->first()->order_total,
                'latest_update_by_client' => now(),
                'payment_type' => 'Безналичная',
                'created_by' => $userId,
            ]);

            $order->order_item()->createMany($lines->map(function ($line) {
                return $line->only(['nomenclature_guid', 'amount', 'price']) + ['discount' => 0];
            }));

            return $order->refresh()->load('order_item');
        });

        return response()->json(['message' => 'ok', 'order' => $order]);
    }
}
