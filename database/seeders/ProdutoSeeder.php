<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produtos = [
            [
                'nome' => 'Vestido Vermundo Elegante',
                'descricao' => 'Vestido feminino elegante perfeito para ocasiões especiais',
                'preco_base' => 129.90,
                'preco_antigo' => 189.90,
                'preco_atual' => 129.90,
                'desconto' => 32,
                'imagem' => '/img/vestido vermelho.png',
                'slug' => 'vestido-vermelho-elegante',
                'categoria_id' => 1, // Moda Feminina
                'ativo' => true,
                'destaque' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Camisa Social Masculina',
                'descricao' => 'Camisa social moderna de algodão premium',
                'preco_base' => 89.90,
                'preco_antigo' => 149.90,
                'preco_atual' => 89.90,
                'desconto' => 40,
                'imagem' => '/img/camisa social.png',
                'slug' => 'camisa-social-masculina',
                'categoria_id' => 2, // Moda Masculina
                'ativo' => true,
                'destaque' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Headset Gamer Profissional',
                'descricao' => 'Headset gamer com áudio surround e microfone',
                'preco_base' => 199.90,
                'preco_antigo' => 299.90,
                'preco_atual' => 199.90,
                'desconto' => 33,
                'imagem' => '/img/heatset.png',
                'slug' => 'headset-gamer-profissional',
                'categoria_id' => 3, // Games e Acessórios
                'ativo' => true,
                'destaque' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Camisa Primavera',
                'descricao' => 'Camisa leve e colorida perfeita para a primavera',
                'preco_base' => 79.90,
                'preco_antigo' => 119.90,
                'preco_atual' => 79.90,
                'desconto' => 33,
                'imagem' => '/img/camisa primavera.png',
                'slug' => 'camisa-primavera',
                'categoria_id' => 1, // Moda Feminina
                'ativo' => true,
                'destaque' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Capinha Premium para Celular',
                'descricao' => 'Capinha de proteção premium com design moderno',
                'preco_base' => 29.90,
                'preco_antigo' => 49.90,
                'preco_atual' => 29.90,
                'desconto' => 40,
                'imagem' => '/img/capinha celular.png',
                'slug' => 'capinha-premium-celular',
                'categoria_id' => 4, // Acessórios Celular
                'ativo' => true,
                'destaque' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Kit Completo de Acessórios',
                'descricao' => 'Kit com todos os acessórios essenciais para seu celular',
                'preco_base' => 59.90,
                'preco_antigo' => 99.90,
                'preco_atual' => 59.90,
                'desconto' => 40,
                'imagem' => '/img/kit celular.png',
                'slug' => 'kit-completo-acessorios',
                'categoria_id' => 4, // Acessórios Celular
                'ativo' => true,
                'destaque' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('produtos')->insert($produtos);
    }
}
