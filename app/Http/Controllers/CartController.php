<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart): Response
    {
        Gate::authorize('view', $cart);

        $cart->load([
            'incartOrders:id,cart_id,status,quantity,variant_id',
            'incartOrders.variant:id,product_id,retail_price',
            'incartOrders.variant.product:id,name,thumbnail_url',
        ]);

        $sortedOrders = $cart->incartOrders->sortDesc()->values();

        return Inertia::render('Carts/Show', [
            'cart' => [
                ...Arr::only($cart->toArray(), ['id', 'user_id', 'created_at', 'updated_at']),
                'total' => $sortedOrders->sum(fn ($order) =>
                    $order->quantity * $order->variant->retail_price
                ),
                'orders' => $sortedOrders,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
