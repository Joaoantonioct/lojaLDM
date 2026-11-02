<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'barcode',
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

    public function currentPromotion(): HasOne
    {
        return $this->hasOne(ProductPromotion::class)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now());
    }

    public function getEffectivePriceAttribute(): string
    {
        $promotion = $this->currentPromotion;
        if (! $promotion) {
            return (string) $this->price;
        }

        $priceCents = (int) round((float) $this->price * 100);
        $effectiveCents = (int) round($priceCents * (10000 - $promotion->discount_basis_points) / 10000);

        return intdiv($effectiveCents, 100).'.'.str_pad((string) ($effectiveCents % 100), 2, '0', STR_PAD_LEFT);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
