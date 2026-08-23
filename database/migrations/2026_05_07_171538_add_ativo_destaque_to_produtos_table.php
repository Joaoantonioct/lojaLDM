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
            // Verificar e adicionar colunas que não existem
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
            if (!Schema::hasColumn('produtos', 'slug')) {
                $table->string('slug')->unique();
            }
            if (!Schema::hasColumn('produtos', 'ativo')) {
                $table->boolean('ativo')->default(true);
            }
            if (!Schema::hasColumn('produtos', 'destaque')) {
                $table->boolean('destaque')->default(false);
            }
            
            // Adicionar índices se não existirem
            if (!Schema::hasIndex('produtos', 'produtos_ativo_destaque_index')) {
                $table->index(['ativo', 'destaque']);
            }
            if (!Schema::hasIndex('produtos', 'produtos_created_at_index')) {
                $table->index('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropIndex(['ativo', 'destaque']);
            $table->dropColumn(['ativo', 'destaque']);
        });
    }
};
