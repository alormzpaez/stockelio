<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\Variant;
use App\Services\PrintfulService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private PrintfulService $printfulService,
        private StripeService $stripeService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $orders = Auth::user()
            ->cart
            ->orders()
            ->where('status', Order::PENDING_STATUS)
            ->with([
                'variant.product:id,thumbnail_url',
                'variant:id,name,retail_price,product_id',
            ])
            ->select([
                'id',
                'variant_id',
                'quantity',
                'status',
            ])
            ->orderBy('id', 'DESC')
            ->get()
        ->map(fn (Order $order) => 
            array_merge($order->toArray(), [
                'total' => $order->quantity * $order->variant->retail_price
            ])
        );

        return Inertia::render('Orders/Index', compact('orders'));
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
    public function store(StoreOrderRequest $request)
    {
        $variant = Variant::with('product')->find($request->validated('variant_id'));

        DB::beginTransaction();

        try {
            $response = $this->printfulService->calculateShippingRate(
                Auth::user()->id,
                $variant->printful_variant_id,
                $request->validated('quantity')
            );

            $index = -1;

            foreach ($response['result'] as $key => $shippingRate) {
                if ($shippingRate['id'] == 'STANDARD') {
                    $index = $key;
                    break;
                }
            }

            $rate = floatval($response['result'][$index]['rate']);
            $minDeliveryDays = $response['result'][$index]['minDeliveryDays'];
            $maxDeliveryDays = $response['result'][$index]['maxDeliveryDays'];
            $minDeliveryDate = $response['result'][$index]['minDeliveryDate'];
            $maxDeliveryDate = $response['result'][$index]['maxDeliveryDate'];

            $orderUnitAmount = ($variant->retail_price * 100) * $request->validated('quantity');
            $shippingRateUnitAmount = $rate * 100;

            $response = $this->stripeService->createAPrice(
                'mxn',
                $orderUnitAmount + $shippingRateUnitAmount,
                $variant->product->stripe_product_id
            );

            $order = Auth::user()
                ->cart
                ->orders()
            ->create(array_merge($request->validated(), [
                'stripe_price_id' => $response['id'],
            ]));

            $order->shippingBreakdown()->create([
                'rate' => $rate,
                'min_delivery_days' => $minDeliveryDays,
                'max_delivery_days' => $maxDeliveryDays,
                'min_delivery_date' => $minDeliveryDate,
                'max_delivery_date' => $maxDeliveryDate,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            $request->session()->flash('type', 'error');
            $request->session()->flash('message', 'Error calculando los gastos de envío.');

            return to_route('products.show', $variant->product_id);
        }

        DB::commit();

        $request->session()->flash('message', 'Producto agregado a tu carrito.');

        return to_route('products.show', $variant->product_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): Response
    {
        $order->load([
            'variant.product:id,thumbnail_url',
            'variant:id,name,retail_price,product_id',
        ]);

        return Inertia::render('Orders/Show', [
            'order' => Arr::only($order->toArray(), [
                'id',
                'variant_id',
                'quantity',
                'status',
                'variant',
            ])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->validated());

        return to_route('carts.show', Auth::user()->cart->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $message = null;

        if ($order->delete()) {
            $message = 'Tu carrito ha sido actualizado.';
        }

        request()->session()->flash('message', $message);

        return to_route('carts.show', Auth::user()->cart->id);
    }
}
