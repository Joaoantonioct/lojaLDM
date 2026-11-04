<?php

namespace Tests\Unit;

use App\Support\ProductSizeCatalog;
use PHPUnit\Framework\TestCase;

class ProductSizeCatalogTest extends TestCase
{
    public function test_clothing_sizes_do_not_include_trouser_or_footwear_sizes(): void
    {
        $this->assertSame(['PP', 'P', 'M', 'G', 'GG', 'XGG'], ProductSizeCatalog::forType('clothing'));
    }

    public function test_trousers_and_footwear_have_separate_brazilian_size_ranges(): void
    {
        $this->assertSame(array_map('strval', range(34, 60, 2)), ProductSizeCatalog::forType('trousers'));
        $this->assertSame(array_map('strval', range(33, 46)), ProductSizeCatalog::forType('footwear'));
    }
}
