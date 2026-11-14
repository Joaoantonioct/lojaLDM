<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicationItem extends Model
{
    protected $fillable = [
        'publication_id',
        'product_id',
        'variant_id',
        'sku',
        'item_name',
        'position',
    ];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function currentPriceCents(): int
    {
        if ($this->variant && $this->variant->price !== null) {
            return (int) round((float) $this->variant->price * 100);
        }

        if (! $this->product) {
            return 0;
        }

        $this->product->loadMissing('currentPromotion');

        return (int) round((float) $this->product->effective_price * 100);
    }
}
