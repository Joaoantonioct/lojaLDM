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
        Schema::create('variacao', function (Blueprint $table) {
           $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->string('nome_variacao'); // ex: Cor, Tamanho, Volumetria
            $table->string('valor_variacao'); // ex: Azul, G, 100ml
            $table->integer('estoque')->default(0);
            $table->decimal('preco_adicional', 10, 2)->default(0.00);
            $table->string('sku')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('variacao');
    }
};
