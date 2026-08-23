<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'preco_antigo',
        'preco_atual',
        'desconto',
        'imagem',
        'slug',
        'ativo',
        'destaque'
    ];

    protected $casts = [
        'preco_antigo' => 'decimal:2',
        'preco_atual' => 'decimal:2',
        'desconto' => 'integer',
        'ativo' => 'boolean',
        'destaque' => 'boolean'
    ];

    /**
     * Busca produtos em destaque para a página inicial
     */
    public static function getProdutosEmDestaque($limit = 6)
    {
        return self::where('ativo', true)
            ->where('destaque', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Busca produtos recentes para a seção "Novidade"
     */
    public static function getProdutosRecentes($limit = 6)
    {
        return self::where('ativo', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Calcula o percentual de desconto automaticamente
     */
    public function getDescontoCalculadoAttribute()
    {
        if ($this->preco_antigo && $this->preco_atual) {
            return round((($this->preco_antigo - $this->preco_atual) / $this->preco_antigo) * 100);
        }
        return $this->desconto;
    }

    /**
     * Formata o preço para exibição
     */
    public function getPrecoAntigoFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco_antigo, 2, ',', '.');
    }

    public function getPrecoAtualFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco_atual, 2, ',', '.');
    }

    public function getDescontoFormatadoAttribute()
    {
        return $this->getDescontoCalculadoAttribute() . '% OFF';
    }
}
