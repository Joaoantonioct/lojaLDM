<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('address_id')->constrained('addresses');
            $table->decimal('total_products', 10, 2);
            $table->decimal('shipping_cost', 10, 2);
            $table->decimal('total_order', 10, 2);
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'delivered', 'canceled'])->default('pending');
            $table->string('payment_method', 50)->nullable();
            $table->string('tracking_code', 100)->nullable();
            $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
