<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SiteTestOrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.productId' => ['required', 'integer', 'exists:product,id'],
            'items.*.variantId' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $order = DB::transaction(function () use ($data, $request) {
            $productIds = collect($data['items'])->pluck('productId')->unique();
            $products = Product::query()
                ->with('variants')
                ->whereIn('id', $productIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== $productIds->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Um ou mais produtos não estão mais disponíveis.',
                ]);
            }

            $remaining = [];
            $orderItems = [];
            $totalCents = 0;
            foreach ($data['items'] as $requestedItem) {
                $product = $products[$requestedItem['productId']];
                $variant = null;
                if ($product->variants->isNotEmpty()) {
                    if (! empty($requestedItem['variantId'])) {
                        $variant = ProductVariant::query()
                            ->where('product_id', $product->id)
                            ->where('active', true)
                            ->lockForUpdate()
                            ->find($requestedItem['variantId']);
                    }
                    if (! $variant) {
                        throw ValidationException::withMessages([
                            'items' => 'Selecione um tamanho válido para '.$product->name.'.',
                        ]);
                    }
                } elseif (! empty($requestedItem['variantId'])) {
                    throw ValidationException::withMessages([
                        'items' => 'O tamanho selecionado não pertence a '.$product->name.'.',
                    ]);
                }

                $sku = $variant?->sku ?? $product->sku;
                if (! $product->active || ! is_string($sku) || $sku === '') {
                    throw ValidationException::withMessages([
                        'items' => 'O produto '.$product->name.' ainda não está vinculado ao PDV.',
                    ]);
                }

                $stockKey = $variant ? 'variant-'.$variant->id : 'product-'.$product->id;
                $available = $remaining[$stockKey] ?? (int) ($variant?->qty ?? $product->qty);
                $quantity = (int) $requestedItem['quantity'];
                if ($available < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => 'Estoque insuficiente para '.$product->name.($variant ? ' no tamanho '.($variant->attributes['size'] ?? $variant->name) : '').'.',
                    ]);
                }
                $remaining[$stockKey] = $available - $quantity;

                $product->load('currentPromotion');
                $unitPriceCents = (int) round((float) $product->effective_price * 100);
                $lineTotalCents = $unitPriceCents * $quantity;
                $totalCents += $lineTotalCents;
                $orderItems[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'sku' => $sku,
                    'unitPriceCents' => $unitPriceCents,
                    'lineTotalCents' => $lineTotalCents,
                ];
            }

            $publicId = (string) Str::uuid();
            $orderId = DB::table('site_test_orders')->insertGetId([
                'public_id' => $publicId,
                'user_id' => $request->user()->id,
                'total_cents' => $totalCents,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($orderItems as $item) {
                $product = $item['product'];
                $variant = $item['variant'];
                $changed = Product::query()
                    ->whereKey($product->id)
                    ->where('qty', '>=', $item['quantity'])
                    ->decrement('qty', $item['quantity']);

                if ($changed !== 1) {
                    throw ValidationException::withMessages([
                        'items' => 'Estoque insuficiente para '.$product->name.'. Atualize o carrinho e tente novamente.',
                    ]);
                }
                if ($variant) {
                    $changedVariant = ProductVariant::query()
                        ->whereKey($variant->id)
                        ->where('qty', '>=', $item['quantity'])
                        ->decrement('qty', $item['quantity']);
                    if ($changedVariant !== 1) {
                        throw ValidationException::withMessages([
                            'items' => 'O estoque do tamanho selecionado acabou. Atualize o carrinho e tente novamente.',
                        ]);
                    }
                }

                DB::table('site_test_order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $product->id,
                    'product_name' => $product->name.($variant ? ' (Tamanho '.($variant->attributes['size'] ?? $variant->name).')' : ''),
                    'sku' => $item['sku'],
                    'quantity' => $item['quantity'],
                    'unit_price_cents' => $item['unitPriceCents'],
                    'total_cents' => $item['lineTotalCents'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('pdv_site_order_outbox')->insert([
                'event_id' => (string) Str::uuid(),
                'order_id' => $orderId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return ['publicId' => $publicId, 'totalCents' => $totalCents];
        });

        return response()->json([
            'message' => 'Pedido de teste registrado. Nenhum pagamento foi processado.',
            'redirectUrl' => route('cart.test.complete', $order['publicId']),
        ], 201);
    }

    public function complete(string $publicId): View
    {
        $order = DB::table('site_test_orders')
            ->where('public_id', $publicId)
            ->first();

        abort_unless($order, 404);

        $items = DB::table('site_test_order_items')
            ->where('order_id', $order->id)
            ->orderBy('id')
            ->get();

        return view('cart.test-complete', compact('order', 'items'));
    }
}
