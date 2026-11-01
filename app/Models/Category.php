<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'active',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function isApparelCategory(): bool
    {
        $categoryNames = Str::ascii(mb_strtolower(implode(' ', array_filter([
            $this->name,
            $this->slug,
            $this->parent?->name,
            $this->parent?->slug,
        ]))));

        return preg_match(
            '/\b(?:roup\w*|vestuario\w*|vestimenta\w*|vestid\w*|moda\w*|calc(?:as?|ados?)|sapat\w*|tenis|sandalia\w*|chinelo\w*|botas?|camis\w*|blusa\w*|uniforme\w*|lingerie|biquini|jaqueta\w*|moletom|meia|cueca|sutia|short\w*|bermuda\w*|saia\w*|acessorios\s+de\s+vestuario\w*|acessorios\s+de\s+vestimenta\w*)\b/',
            $categoryNames,
        ) === 1;
    }
}
