<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = collect([
            [
                'name' => 'Roupas',
                'slug' => 'roupas',
                'description' => 'Camisetas, calcas, vestidos e outras pecas de vestuario.',
            ],
            [
                'name' => 'Acessorios de celular',
                'slug' => 'acessorios-de-celular',
                'description' => 'Capas, carregadores, suportes e acessorios para celulares.',
            ],
            [
                'name' => 'Papelaria',
                'slug' => 'papelaria',
                'description' => 'Materiais para estudo, escritorio e organizacao.',
            ],
        ])->mapWithKeys(function (array $dados): array {
            return [
                $dados['slug'] => Category::updateOrCreate(
                    ['slug' => $dados['slug']],
                    [...$dados, 'active' => true],
                ),
            ];
        });

        $subcategorias = [
            'roupas' => [
                ['name' => 'Camisetas', 'slug' => 'camisetas'],
                ['name' => 'Calcas', 'slug' => 'calcas'],
                ['name' => 'Vestidos', 'slug' => 'vestidos'],
            ],
            'acessorios-de-celular' => [
                ['name' => 'Capas', 'slug' => 'capas-de-celular'],
                ['name' => 'Carregadores', 'slug' => 'carregadores-de-celular'],
                ['name' => 'Cabos', 'slug' => 'cabos-de-celular'],
            ],
            'papelaria' => [
                ['name' => 'Cadernos', 'slug' => 'cadernos'],
                ['name' => 'Canetas e lapis', 'slug' => 'canetas-e-lapis'],
                ['name' => 'Organizacao', 'slug' => 'organizacao'],
            ],
        ];

        foreach ($subcategorias as $categoriaPai => $itens) {
            foreach ($itens as $subcategoria) {
                Category::updateOrCreate(
                    ['slug' => $subcategoria['slug']],
                    [
                        'name' => $subcategoria['name'],
                        'description' => 'Subcategoria de '.$categorias[$categoriaPai]->name.'.',
                        'parent_id' => $categorias[$categoriaPai]->id,
                        'active' => true,
                    ],
                );
            }
        }

        $marcas = collect([
            ['name' => 'LDM Basic', 'slug' => 'ldm-basic'],
            ['name' => 'Urbana', 'slug' => 'urbana'],
            ['name' => 'Tech Plus', 'slug' => 'tech-plus'],
            ['name' => 'Connect', 'slug' => 'connect'],
            ['name' => 'Office Mais', 'slug' => 'office-mais'],
            ['name' => 'ColorPop', 'slug' => 'colorpop'],
        ])->mapWithKeys(function (array $dados): array {
            return [
                $dados['slug'] => Brand::updateOrCreate(
                    ['slug' => $dados['slug']],
                    [...$dados, 'active' => true],
                ),
            ];
        });

        $produtos = [
            [
                'category' => 'roupas',
                'brand' => 'ldm-basic',
                'name' => 'Camiseta basica algodao preta',
                'description' => 'Camiseta confortavel de algodao, modelagem regular e cor preta.',
                'price' => 49.90,
                'qty' => 24,
            ],
            [
                'category' => 'roupas',
                'brand' => 'urbana',
                'name' => 'Calca jeans slim azul',
                'description' => 'Calca jeans azul com corte slim para uso diario.',
                'price' => 129.90,
                'qty' => 12,
            ],
            [
                'category' => 'roupas',
                'brand' => 'urbana',
                'name' => 'Moletom canguru cinza',
                'description' => 'Moletom macio com capuz e bolso frontal.',
                'price' => 159.90,
                'qty' => 8,
            ],
            [
                'category' => 'roupas',
                'brand' => 'ldm-basic',
                'name' => 'Vestido midi floral',
                'description' => 'Vestido midi leve com estampa floral e alcas ajustaveis.',
                'price' => 119.90,
                'qty' => 0,
            ],
            [
                'category' => 'roupas',
                'brand' => 'urbana',
                'name' => 'Jaqueta corta vento',
                'description' => 'Jaqueta leve para dias de vento, com bolsos laterais.',
                'price' => 189.90,
                'qty' => 5,
            ],
            [
                'category' => 'acessorios-de-celular',
                'brand' => 'tech-plus',
                'name' => 'Capa antig impacto para celular',
                'description' => 'Capa transparente com bordas reforcadas e protecao contra impactos.',
                'price' => 39.90,
                'qty' => 30,
            ],
            [
                'category' => 'acessorios-de-celular',
                'brand' => 'connect',
                'name' => 'Carregador rapido USB-C 25W',
                'description' => 'Carregador compacto com carregamento rapido e entrada USB-C.',
                'price' => 79.90,
                'qty' => 18,
            ],
            [
                'category' => 'acessorios-de-celular',
                'brand' => 'connect',
                'name' => 'Cabo USB-C trancado 2 metros',
                'description' => 'Cabo resistente com dois metros de comprimento para carga e dados.',
                'price' => 34.90,
                'qty' => 22,
            ],
            [
                'category' => 'acessorios-de-celular',
                'brand' => 'tech-plus',
                'name' => 'Suporte de mesa para celular',
                'description' => 'Suporte ajustavel para assistir videos ou fazer chamadas.',
                'price' => 44.90,
                'qty' => 11,
            ],
            [
                'category' => 'acessorios-de-celular',
                'brand' => 'connect',
                'name' => 'Película de vidro 3D',
                'description' => 'Película de vidro temperado com cobertura para a tela do celular.',
                'price' => 24.90,
                'qty' => 0,
            ],
            [
                'category' => 'papelaria',
                'brand' => 'office-mais',
                'name' => 'Caderno universitario 10 materias',
                'description' => 'Caderno espiral com divisorias e folhas pautadas.',
                'price' => 32.90,
                'qty' => 16,
            ],
            [
                'category' => 'papelaria',
                'brand' => 'colorpop',
                'name' => 'Kit canetas coloridas 12 cores',
                'description' => 'Kit de canetas coloridas para estudos, desenhos e organizacao.',
                'price' => 27.90,
                'qty' => 25,
            ],
            [
                'category' => 'papelaria',
                'brand' => 'office-mais',
                'name' => 'Planner semanal de mesa',
                'description' => 'Planner sem data para organizar tarefas e compromissos da semana.',
                'price' => 22.90,
                'qty' => 9,
            ],
            [
                'category' => 'papelaria',
                'brand' => 'colorpop',
                'name' => 'Marca texto pastel 6 cores',
                'description' => 'Marca textos de cores pastel com ponta chanfrada.',
                'price' => 19.90,
                'qty' => 20,
            ],
            [
                'category' => 'papelaria',
                'brand' => 'office-mais',
                'name' => 'Estojo escolar grande',
                'description' => 'Estojo com compartimentos para canetas, lapis e acessorios.',
                'price' => 39.90,
                'qty' => 7,
            ],
        ];

        foreach ($produtos as $produto) {
            $category = $categorias[$produto['category']];
            $brand = $marcas[$produto['brand']];

            Product::updateOrCreate(
                ['slug' => $produto['category'].'-'.str()->slug($produto['name'])],
                [
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'name' => $produto['name'],
                    'description' => $produto['description'],
                    'price' => $produto['price'],
                    'qty' => $produto['qty'],
                    'condition' => $produto['category'] === 'roupas' ? 'novo' : null,
                    'sku' => 'DEMO-'.str()->upper(str()->random(8)),
                    'private' => false,
                    'active' => true,
                ],
            );
        }
    }
}
