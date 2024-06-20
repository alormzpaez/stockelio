<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function executeSuccess(int $userId): bool
    {
        $printfulService = new PrintfulService();

        $user = User::with('cart.incartOrders.variant')->find($userId);

        if (!$user) {
            throw new \Exception('User is not found.');
        }

        DB::beginTransaction();

        $user->cart->incartOrders->each(function (Order $order) use ($printfulService, $user) {
            try {
                $response = $printfulService->createANewOrder(
                    $user->id,
                    $order->variant->printful_variant_id,
                    $order->quantity
                );
            } catch (\Exception $e) {
                DB::rollBack();

                throw $e;
            }

            $order->update([
                'status' => Order::PENDING_STATUS,
                'printful_order_id' => $response['result']['id'],
            ]);
        });

        DB::commit();

        return true;
    }
}
