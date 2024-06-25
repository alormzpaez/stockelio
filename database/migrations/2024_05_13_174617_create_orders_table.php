<?php

use App\Models\Cart;
use App\Models\Variant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cart::class);
            $table->foreignIdFor(Variant::class);
            $table->integer('quantity');
            $table->enum('status', [
                'incart',
                'draft', // The order is created in system but is not yet submitted for fulfillment. You still can edit it and confirm later.
                'pending', // The order now is created in printful, waiting to be fulfilled there.
                'fulfilled', // All items have been shipped successfully
            ])->default('incart');
            $table->unsignedBigInteger('printful_order_id')->nullable()->unique();
            /**
             * Each order will have a stripe price (it will include: quantity for retail price of 
             * variant and shipping rate).
             */
            $table->string('stripe_price_id')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
