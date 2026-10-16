<?php

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
        // A single table design that supports both:
        // - current cart
        // - past orders (order history)
        // by using a status field.

        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // optional if you want cart for guests too
            $table->foreignId('user_id')->nullable()->index();

            // cart vs order distinction in one table
            $table->string('status')->default('cart'); // cart|ordered|cancelled|completed

            // store the user snapshot for easy rendering (optional but useful)
            $table->string('currency', 3)->default('NGN');

            // totals snapshot
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->decimal('delivery_fee', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);

            // shipping / billing placeholders (add more columns as you need)
            $table->string('payment_method')->nullable();
            $table->string('transaction_reference')->nullable()->index();

            // payment/shipping state
            $table->timestamp('ordered_at')->nullable();
            $table->timestamps();

            // NOTE: no FK constraint to users because users migration exists, but
            // some environments may not have it yet.
            // If you want strict FK, tell me and I’ll add it.
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            $table->foreignId('item_id')->nullable()->index();
            $table->string('item_name');
            $table->string('item_image')->nullable();

            $table->unsignedInteger('quantity')->default(1);

            // price snapshot
            $table->decimal('unit_price', 14, 2);
            $table->decimal('line_total', 14, 2);

            // optional metadata
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};

