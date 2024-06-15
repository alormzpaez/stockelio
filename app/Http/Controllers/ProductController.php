<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\PermissionsEnum;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(
        private FileService $fileService,
    ) {
    }

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
            'files:id,product_id,filename',
        ]);

        return Inertia::render('Products/Show', array_merge(compact('product'), [
            'can' => [
                PermissionsEnum::UpdateProduct->value => Auth::user()->can(
                    PermissionsEnum::UpdateProduct->value
                )
            ]
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): Response
    {
        $product->loadCount('variants');
        $product->load('files:id,product_id');

        return Inertia::render('Products/Edit', [
            'product' => [
                ...Arr::only($product->toArray(), [
                    'id',
                    'name',
                    'thumbnail_url',
                    'description',
                    'variants_count',
                    'files',
                ]),
                'created_at' => $product->created_at->diffForHumans(),
                'updated_at' => $product->updated_at->diffForHumans(),
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->only('description'));

        foreach ($request->validated('files') as $file) {
            $this->fileService->save($product->id, $file);
        }

        if (count($request->validated('files')) > 0) {
            $product->touch();
        }

        $request->session()->flash('message', 'El producto ha sido actualizado.');

        return to_route('products.edit', $product->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
