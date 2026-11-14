<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publication extends Model
{
    protected $fillable = [
        'event_id',
        'payload_hash',
        'title',
        'description',
        'image_path',
        'expires_at',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PublicationItem::class)->orderBy('position');
    }

    protected function casts(): array
    {
        return ['expires_at' => 'datetime'];
    }
    public function isAvailable(): bool
    {
        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }
        return $this->items->isNotEmpty() && $this->items->every(function (PublicationItem $item): bool {
            if (! $item->product || ! $item->product->active) {
                return false;
            }

            if ($item->variant_id !== null) {
                return $item->variant && $item->variant->active && $item->variant->qty > 0;
            }

            return $item->product->qty > 0;
        });
    }

    public function totalCents(): int
    {
        return (int) $this->items->sum(fn (PublicationItem $item): int => $item->currentPriceCents());
    }
}
