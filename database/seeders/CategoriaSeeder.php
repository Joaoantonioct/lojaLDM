<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nome' => 'Moda Feminina', 'slug' => 'moda-feminina', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Moda Masculina', 'slug' => 'moda-masculina', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Games e Acessórios', 'slug' => 'games-e-acessorios', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Acessórios Celular', 'slug' => 'acessorios-celular', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categorias')->insert($categorias);
    }
}
