<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'brand_id',
        'qty',
        'condition',
        'description',
        'price',
        'sku',
        'private',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'private' => 'boolean',
        'active' => 'boolean',
    ];

    public function conditionLabel(): ?string
    {
        return match ($this->condition) {
            'novo' => 'Novo',
            'seminovo' => 'Seminovo',
            'brecho' => 'Brechó',
            default => null,
        };
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
