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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->decimal('preco_antigo', 10, 2)->nullable();
            $table->decimal('preco_atual', 10, 2);
            $table->integer('desconto')->nullable();
            $table->string('imagem')->nullable();
            $table->string('slug')->unique();
            $table->boolean('ativo')->default(true);
            $table->boolean('destaque')->default(false);
            $table->timestamps();
            
            $table->index(['ativo', 'destaque']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
