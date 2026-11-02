<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_test_orders', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('total_cents');
            $table->timestamps();
        });

        Schema::create('site_test_order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('site_test_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('product');
            $table->string('product_name', 150);
            $table->string('sku', 100);
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_price_cents');
            $table->unsignedBigInteger('total_cents');
            $table->timestamps();
            $table->unique(['order_id', 'product_id']);
        });

        Schema::create('pdv_site_order_outbox', function (Blueprint $table): void {
            $table->uuid('event_id')->primary();
            $table->foreignId('order_id')->unique()->constrained('site_test_orders')->cascadeOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
            $table->index('acknowledged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdv_site_order_outbox');
        Schema::dropIfExists('site_test_order_items');
        Schema::dropIfExists('site_test_orders');
    }
};
