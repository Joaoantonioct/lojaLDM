<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PdvIdentifierAllocator
{
    public function allocate(?int $productId = null): array
    {
        return DB::transaction(function () use ($productId): array {
            $sequence = DB::table('pdv_identifier_sequences')
                ->where('id', 1)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                [$nextSku, $nextBarcode] = $this->nextSequenceNumbers();
                $now = now();

                DB::table('pdv_identifier_sequences')->insertOrIgnore([
                    'id' => 1,
                    'next_sku' => $nextSku,
                    'next_barcode' => $nextBarcode,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $sequence = DB::table('pdv_identifier_sequences')
                    ->where('id', 1)
                    ->lockForUpdate()
                    ->first();
            }

            if (! $sequence) {
                throw new \RuntimeException('Não foi possível inicializar a sequência de identificadores do PDV.');
            }

            $skuNumber = (int) $sequence->next_sku;
            $barcodeNumber = (int) $sequence->next_barcode;

            while (true) {
                $sku = 'PDV-'.str_pad((string) $skuNumber, 6, '0', STR_PAD_LEFT);
                $barcode = $this->barcode($barcodeNumber);

                $skuExists = Product::query()->where('sku', $sku)->exists()
                    || DB::table('pdv_identifiers')->where('sku', $sku)->exists();
                $barcodeExists = Product::query()->where('barcode', $barcode)->exists()
                    || DB::table('pdv_identifiers')->where('barcode', $barcode)->exists();

                if (! $skuExists && ! $barcodeExists) {
                    $now = now();
                    DB::table('pdv_identifiers')->insert([
                        'sku' => $sku,
                        'barcode' => $barcode,
                        'product_id' => $productId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('pdv_identifier_sequences')
                        ->where('id', 1)
                        ->update([
                            'next_sku' => $skuNumber + 1,
                            'next_barcode' => $barcodeNumber + 1,
                            'updated_at' => $now,
                        ]);

                    return ['sku' => $sku, 'barcode' => $barcode];
                }

                if ($skuExists) {
                    $skuNumber++;
                }
                if ($barcodeExists) {
                    $barcodeNumber++;
                }
            }
        });
    }

    private function nextSequenceNumbers(): array
    {
        $skuValues = Product::query()->whereNotNull('sku')->pluck('sku')
            ->merge(DB::table('pdv_identifiers')->pluck('sku'));
        $barcodeValues = Product::query()->whereNotNull('barcode')->pluck('barcode')
            ->merge(DB::table('pdv_identifiers')->pluck('barcode'));

        $nextSku = 1;
        $hasGeneratedSku = false;
        foreach ($skuValues as $sku) {
            if (preg_match('/^PDV-([0-9]+)$/', (string) $sku, $matches)) {
                $hasGeneratedSku = true;
                $nextSku = max($nextSku, (int) $matches[1] + 1);
            }
        }

        $nextBarcode = 1;
        $hasGeneratedBarcode = false;
        foreach ($barcodeValues as $barcode) {
            if (preg_match('/^2[0-9]([0-9]{10})[0-9]$/', (string) $barcode, $matches)) {
                $hasGeneratedBarcode = true;
                $nextBarcode = max($nextBarcode, (int) $matches[1] + 1);
            }
        }

        if (! $hasGeneratedSku) {
            $nextSku = random_int(100_000_000, 999_999_999);
        }
        if (! $hasGeneratedBarcode) {
            $nextBarcode = random_int(100_000_000, 999_999_999);
        }

        return [$nextSku, $nextBarcode];
    }

    public function claim(string $sku, string $barcode, Product $product): void
    {
        $skuOwner = Product::query()
            ->where('sku', $sku)
            ->whereKeyNot($product->getKey())
            ->exists();
        $barcodeOwner = Product::query()
            ->where('barcode', $barcode)
            ->whereKeyNot($product->getKey())
            ->exists();

        $bySku = DB::table('pdv_identifiers')->where('sku', $sku)->lockForUpdate()->first();
        $byBarcode = DB::table('pdv_identifiers')->where('barcode', $barcode)->lockForUpdate()->first();
        $existingProductIdentifier = DB::table('pdv_identifiers')
            ->where('product_id', $product->getKey())
            ->lockForUpdate()
            ->first();

        if ($skuOwner || $barcodeOwner
            || ($bySku && ($bySku->barcode !== $barcode || ($bySku->product_id !== null && (int) $bySku->product_id !== $product->getKey())))
            || ($byBarcode && ($byBarcode->sku !== $sku || ($byBarcode->product_id !== null && (int) $byBarcode->product_id !== $product->getKey())))
            || ($existingProductIdentifier && ($existingProductIdentifier->sku !== $sku || $existingProductIdentifier->barcode !== $barcode))) {
            abort(409, 'O SKU ou código de barras já está vinculado a outro produto ou reserva.');
        }

        if ($bySku) {
            DB::table('pdv_identifiers')
                ->where('sku', $sku)
                ->update(['product_id' => $product->getKey(), 'updated_at' => now()]);
        } else {
            DB::table('pdv_identifiers')->insert([
                'sku' => $sku,
                'barcode' => $barcode,
                'product_id' => $product->getKey(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $product->sku = $sku;
        $product->barcode = $barcode;
        $product->save();
    }

    public function attachReserved(string $sku, string $barcode, Product $product): void
    {
        $identifier = DB::table('pdv_identifiers')
            ->where('sku', $sku)
            ->where('barcode', $barcode)
            ->lockForUpdate()
            ->first();

        if (! $identifier || $identifier->product_id !== null) {
            abort(409, 'Os identificadores não foram reservados para este produto pelo site.');
        }

        if (Product::query()->where('sku', $sku)->exists()
            || Product::query()->where('barcode', $barcode)->exists()) {
            abort(409, 'O SKU ou código de barras já está vinculado a um produto.');
        }

        DB::table('pdv_identifiers')
            ->where('sku', $sku)
            ->update(['product_id' => $product->getKey(), 'updated_at' => now()]);

        $product->sku = $sku;
        $product->barcode = $barcode;
        $product->save();
    }

    public function isValidBarcode(string $barcode): bool
    {
        if (! preg_match('/^2[0-9][0-9]{11}$/', $barcode)) {
            return false;
        }

        $sum = 0;
        for ($position = 0; $position < 12; $position++) {
            $sum += (int) $barcode[$position] * ($position % 2 === 0 ? 1 : 3);
        }

        return (10 - ($sum % 10)) % 10 === (int) $barcode[12];
    }

    private function barcode(int $number): string
    {
        $body = '20'.str_pad((string) $number, 10, '0', STR_PAD_LEFT);
        $sum = 0;
        for ($position = 0; $position < 12; $position++) {
            $sum += (int) $body[$position] * ($position % 2 === 0 ? 1 : 3);
        }

        return $body.(string) ((10 - ($sum % 10)) % 10);
    }
}
