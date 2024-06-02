<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $products = Product::select(['id', 'name', 'thumbnail_url'])
            ->withCount('variants')
            ->with('cheapestVariant:id,variants.product_id,retail_price')
            ->latest()
        ->paginate();

        return Inertia::render('Products/Index', compact('products'));
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
    public function show(Product $product): Response
    {
        $product->load([
            'cheapestVariant:id,variants.product_id,retail_price',
            'variants:id,variants.product_id,retail_price',
        ]);

        return Inertia::render('Products/Show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
