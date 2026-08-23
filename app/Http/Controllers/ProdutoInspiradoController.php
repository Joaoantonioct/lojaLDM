<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdutoInspiradoController extends Controller
{
    /**
     * Exibe a página principal com produtos inspirados
     */
    public function index()
    {
        // Buscar produtos inspirados baseados em critérios inteligentes
        $produtosInspirados = $this->getProdutosInspirados();
        
        return view('welcome', compact('produtosInspirados'));
    }
    
    /**
     * Obtém produtos inspirados baseados em múltiplos critérios
     */
    private function getProdutosInspirados($limit = 12)
    {
        // Critérios para produtos inspirados:
        // 1. Produtos mais vendidos
        // 2. Produtos com maior desconto
        // 3. Produtos recentemente visualizados (se houver sessão)
        // 4. Produtos da mesma categoria dos visualizados
        // 5. Produtos em alta
        
        $produtos = collect();
        
        // 1. Produtos mais vendidos (simulado - poderia vir de tabela de pedidos)
        $maisVendidos = Produto::with(['variants'])
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit(4)
            ->get();
        
        // 2. Produtos com maiores descontos
        $comDesconto = ProductVariant::with('produto')
            ->where('desconto', '>', 0)
            ->whereHas('produto', function($query) {
                $query->where('status', 'active');
            })
            ->orderBy('desconto', 'desc')
            ->limit(4)
            ->get()
            ->map(function($variant) {
                return $variant->produto;
            });
        
        // 3. Produtos recentes (lançamentos)
        $recentes = Produto::with(['variants'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
        
        // Combinar e remover duplicatas
        $produtosInspirados = $maisVendidos
            ->merge($comDesconto)
            ->merge($recentes)
            ->unique('id')
            ->take($limit)
            ->map(function($produto) {
                // Preparar dados para exibição
                $variantAtiva = $produto->variants->first();
                
                return [
                    'id' => $produto->id,
                    'nome' => $produto->nome,
                    'descricao' => $produto->descricao,
                    'imagem' => $produto->imagem ?? '/img/placeholder.png',
                    'preco_original' => $variantAtiva->preco_original ?? 0,
                    'preco_atual' => $variantAtiva->preco_atual ?? 0,
                    'desconto' => $variantAtiva->desconto ?? 0,
                    'estoque' => $variantAtiva->estoque ?? 0,
                    'categoria_id' => $produto->categoria_id,
                    'slug' => $produto->slug,
                    'variant_id' => $variantAtiva->id ?? null,
                ];
            });
        
        return $produtosInspirados;
    }
    
    /**
     * API endpoint para carregar mais produtos inspirados
     */
    public function carregarMais(Request $request)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 6);
        
        $produtos = $this->getProdutosInspirados($limit + $offset)
            ->slice($offset)
            ->take($limit);
        
        return response()->json([
            'produtos' => $produtos,
            'hasMore' => $produtos->count() >= $limit
        ]);
    }
    
    /**
     * Atualiza produtos baseados no último produto visualizado
     */
    public function atualizarPorVisualizacao(Request $request)
    {
        $produtoId = $request->get('produto_id');
        
        if (!$produtoId) {
            return response()->json(['error' => 'Produto não informado'], 400);
        }
        
        // Buscar o produto visualizado
        $produtoVisualizado = Produto::find($produtoId);
        
        if (!$produtoVisualizado) {
            return response()->json(['error' => 'Produto não encontrado'], 404);
        }
        
        // Buscar produtos da mesma categoria
        $mesmaCategoria = Produto::with(['variants'])
            ->where('categoria_id', $produtoVisualizado->categoria_id)
            ->where('id', '!=', $produtoId)
            ->where('status', 'active')
            ->limit(8)
            ->get();
        
        // Buscar produtos relacionados (baseado em nome/descrição)
        $palavrasChave = $this->extrairPalavrasChave($produtoVisualizado->nome);
        $relacionados = collect();
        
        if (!empty($palavrasChave)) {
            $relacionados = Produto::with(['variants'])
                ->where('id', '!=', $produtoId)
                ->where('status', 'active')
                ->where(function($query) use ($palavrasChave) {
                    foreach ($palavrasChave as $palavra) {
                        $query->orWhere('nome', 'LIKE', "%{$palavra}%")
                              ->orWhere('descricao', 'LIKE', "%{$palavra}%");
                    }
                })
                ->limit(4)
                ->get();
        }
        
        // Combinar resultados
        $produtosRelacionados = $mesmaCategoria
            ->merge($relacionados)
            ->unique('id')
            ->take(12)
            ->map(function($produto) {
                $variantAtiva = $produto->variants->first();
                
                return [
                    'id' => $produto->id,
                    'nome' => $produto->nome,
                    'descricao' => $produto->descricao,
                    'imagem' => $produto->imagem ?? '/img/placeholder.png',
                    'preco_original' => $variantAtiva->preco_original ?? 0,
                    'preco_atual' => $variantAtiva->preco_atual ?? 0,
                    'desconto' => $variantAtiva->desconto ?? 0,
                    'estoque' => $variantAtiva->estoque ?? 0,
                    'categoria_id' => $produto->categoria_id,
                    'slug' => $produto->slug,
                    'variant_id' => $variantAtiva->id ?? null,
                ];
            });
        
        return response()->json([
            'produtos' => $produtosRelacionados,
            'produto_visualizado' => [
                'id' => $produtoVisualizado->id,
                'nome' => $produtoVisualizado->nome,
                'imagem' => $produtoVisualizado->imagem ?? '/img/placeholder.png'
            ]
        ]);
    }
    
    /**
     * Extrai palavras-chave do nome do produto para busca relacionada
     */
    private function extrairPalavrasChave($nome)
    {
        // Remover palavras comuns e extrair palavras relevantes
        $palavrasComuns = ['kit', 'conjunto', 'com', 'para', 'de', 'da', 'do', 'em', 'um', 'uma'];
        $palavras = explode(' ', strtolower($nome));
        
        return array_filter($palavras, function($palavra) use ($palavrasComuns) {
            return strlen($palavra) > 2 && !in_array($palavra, $palavrasComuns);
        });
    }
    
    /**
     * Adiciona produto ao carrinho (API endpoint)
     */
    public function adicionarAoCarrinho(Request $request)
    {
        $produtoId = $request->get('produto_id');
        $variantId = $request->get('variant_id');
        $quantidade = $request->get('quantidade', 1);
        
        // Validar produto
        $produto = Produto::find($produtoId);
        if (!$produto) {
            return response()->json(['error' => 'Produto não encontrado'], 404);
        }
        
        // Validar variant se informada
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if (!$variant || $variant->produto_id !== $produtoId) {
                return response()->json(['error' => 'Variante não encontrada'], 404);
            }
            
            if ($variant->estoque < $quantidade) {
                return response()->json(['error' => 'Estoque insuficiente'], 400);
            }
        }
        
        // Lógica do carrinho (session ou banco)
        $carrinho = session()->get('carrinho', []);
        
        $itemKey = $variantId ? "{$produtoId}_{$variantId}" : $produtoId;
        
        if (isset($carrinho[$itemKey])) {
            $carrinho[$itemKey]['quantidade'] += $quantidade;
        } else {
            $carrinho[$itemKey] = [
                'produto_id' => $produtoId,
                'variant_id' => $variantId,
                'quantidade' => $quantidade,
                'nome' => $produto->nome,
                'imagem' => $produto->imagem ?? '/img/placeholder.png',
                'preco' => $variant ? $variant->preco_atual : $produto->preco_minimo,
                'added_at' => now()
            ];
        }
        
        session()->put('carrinho', $carrinho);
        
        return response()->json([
            'success' => true,
            'message' => 'Produto adicionado ao carrinho!',
            'carrinho_count' => array_sum(array_column($carrinho, 'quantidade'))
        ]);
    }
}
