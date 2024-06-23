<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService,
    ) {
    }

    public function checkout(Request $request)
    {
        $cart = $request->user()->cart;

        $cart->load('incartOrders.variant');

        if ($cart->incartOrders->count() == 0) {
            $request->session()->flash('message', 'No hay ninguna orden en tu carrito.');
            $request->session()->flash('type', 'error');

            return to_route('carts.show', $cart->id);
        }

        $orders = $cart->incartOrders->flatMap(fn ($order) => [
            $order->variant->stripe_price_id => $order->quantity
        ])->toArray();

        return $request->user()->checkout($orders, [
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
        ]);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
 
        if ($sessionId === null) {
            return to_route('checkout.cancel');
        }
        
        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);
 
        if ($session->payment_status !== 'paid') {
            return to_route('checkout.cancel');
        }
        
        try {
            $response = $this->checkoutService->executeSuccess($request->user()->id);

            if (!$response) {
                return to_route('checkout.cancel');
            }
        } catch (\Throwable $th) {
            return to_route('checkout.cancel');
        }

        $request->session()->flash('message', 'Tus ordenes pendientes han sido actualizadas.');
        $request->session()->flash('type', 'notification');

        return to_route('orders.index');
    }
    
    public function cancel(Request $request)
    {
        $request->session()->flash('message', 'Error intentando completar el pago.');
        $request->session()->flash('type', 'error');

        return to_route('carts.show', $request->user()->cart->id);
    }
}
