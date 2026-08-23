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
        Schema::table('produtos', function (Blueprint $table) {
            // Adicionar colunas que não existem
            if (!Schema::hasColumn('produtos', 'descricao')) {
                $table->text('descricao')->nullable();
            }
            if (!Schema::hasColumn('produtos', 'preco_antigo')) {
                $table->decimal('preco_antigo', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('produtos', 'preco_atual')) {
                $table->decimal('preco_atual', 10, 2);
            }
            if (!Schema::hasColumn('produtos', 'desconto')) {
                $table->integer('desconto')->nullable();
            }
            if (!Schema::hasColumn('produtos', 'imagem')) {
                $table->string('imagem')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn(['descricao', 'preco_antigo', 'preco_atual', 'desconto', 'imagem']);
        });
    }
};
