<?php

namespace App\Support;

class ProductSizeCatalog
{
    public static function forType(?string $type): array
    {
        return match ($type) {
            'footwear' => array_map('strval', range(33, 46)),
            'trousers' => array_map('strval', range(34, 60, 2)),
            'clothing' => ['PP', 'P', 'M', 'G', 'GG', 'XGG'],
            default => [],
        };
    }
}
