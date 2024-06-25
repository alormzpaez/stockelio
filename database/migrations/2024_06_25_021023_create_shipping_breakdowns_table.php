<?php

use App\Models\Order;
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
        Schema::create('shipping_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class);
            $table->string('carrier')->nullable();
            $table->text('service')->nullable();
            $table->text('tracking_url')->nullable();
            $table->date('ship_date')->nullable();
            $table->float('rate');
            $table->integer('min_delivery_days');
            $table->integer('max_delivery_days');
            $table->date('min_delivery_date');
            $table->date('max_delivery_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_breakdowns');
    }
};
