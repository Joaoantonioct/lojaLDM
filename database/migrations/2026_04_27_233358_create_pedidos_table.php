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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('usuarios');
            $table->decimal('total_pedido', 10, 2);
            $table->enum('status_pagamento', ['pendente', 'pago', 'cancelado'])->default('pendente');
            $table->enum('status_entrega', ['processando', 'enviado', 'entregue'])->default('processando');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
};
