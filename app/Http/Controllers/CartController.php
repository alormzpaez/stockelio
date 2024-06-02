<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
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
            'orders:id,cart_id,status,quantity,variant_id',
            'orders.variant:id,product_id,retail_price',
            'orders.variant.product:id,name,thumbnail_url',
        ]);

        $sortedOrders = $cart->orders->sortDesc()->values();

        return Inertia::render('Carts/Show', [
            'cart' => [
                'id' => $cart->id,
                'user_id' => $cart->user_id,
                'created_at' => $cart->created_at,
                'updated_at' => $cart->updated_at,
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
