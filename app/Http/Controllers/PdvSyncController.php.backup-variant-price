<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductPromotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PdvSyncController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $token = (string) config('pdv.sync_token');
        $providedToken = (string) $request->bearerToken();

        if (strlen($token) < 32) {
            return response()->json(['message' => 'A integração do PDV não está configurada corretamente.'], 503);
        }

        if ($providedToken === '' || ! hash_equals($token, $providedToken)) {
            return response()->json(['message' => 'Acesso não autorizado.'], 401);
        }

        $event = $request->validate([
            'eventId' => ['required', 'uuid'],
            'type' => ['required', 'string', 'in:inventory.delta,inventory.set,product.price,promotion.upsert,promotion.cancel'],
            'payload' => ['required', 'array'],
        ]);
        $payload = $this->validatePayload($event['type'], $event['payload']);

        $result = DB::transaction(function () use ($event, $payload): array {
            if (DB::table('pdv_sync_receipts')->where('event_id', $event['eventId'])->exists()) {
                return ['received' => true, 'duplicate' => true];
            }

            $variant = ProductVariant::query()
                ->where('sku', $payload['sku'])
                ->lockForUpdate()
                ->first();
            $product = Product::query()
                ->where('sku', $payload['sku'])
                ->lockForUpdate()
                ->first() ?? $variant?->product()->lockForUpdate()->first();

            if (! $product) {
                abort(422, 'Não existe produto no site com o SKU '.$payload['sku'].'.');
            }

            switch ($event['type']) {
                case 'inventory.delta':
                    if ($variant) {
                        $variantQuantity = $variant->qty + $payload['delta'];
                        if ($variantQuantity < 0) {
                            abort(409, 'O movimento deixaria o estoque do tamanho '.$variant->name.' negativo.');
                        }
                        $variant->qty = $variantQuantity;
                        $variant->save();
                    }
                    $quantity = $product->qty + $payload['delta'];
                    if ($quantity < 0) {
                        abort(409, 'O movimento deixaria o estoque do site negativo para o SKU '.$payload['sku'].'.');
                    }
                    $product->qty = $quantity;
                    $product->save();
                    break;

                case 'inventory.set':
                    if ($variant) {
                        $product->qty += $payload['quantity'] - $variant->qty;
                        $variant->qty = $payload['quantity'];
                        $variant->save();
                        $product->save();
                        break;
                    }
                    $product->qty = $payload['quantity'];
                    $product->save();
                    break;

                case 'product.price':
                    $product->price = $this->decimalFromCents($payload['priceCents']);
                    if (isset($payload['costCents'])) {
                        $product->cost = $this->decimalFromCents($payload['costCents']);
                    }
                    if (array_key_exists('description', $payload)) {
                        $product->description = $payload['description'];
                    }
                    $product->save();
                    break;

                case 'promotion.upsert':
                    ProductPromotion::query()->updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'external_id' => (string) $payload['promotionId'],
                        ],
                        [
                            'discount_basis_points' => $payload['discountBasisPoints'],
                            'starts_at' => $payload['startsAt'],
                            'ends_at' => $payload['endsAt'],
                        ],
                    );
                    break;

                case 'promotion.cancel':
                    $promotion = ProductPromotion::query()
                        ->where('product_id', $product->id)
                        ->where('external_id', (string) $payload['promotionId'])
                        ->first();
                    if ($promotion) {
                        $currentTime = now();
                        if ($promotion->ends_at->gt($currentTime)) {
                            $promotion->ends_at = $currentTime;
                            $promotion->save();
                        }
                    }
                    break;
            }

            DB::table('pdv_sync_receipts')->insert([
                'event_id' => $event['eventId'],
                'event_type' => $event['type'],
                'received_at' => now(),
            ]);

            return ['received' => true, 'duplicate' => false];
        });

        return response()->json($result);
    }

    private function validatePayload(string $type, array $payload): array
    {
        $rules = [
            'sku' => ['required', 'string', 'max:100'],
        ];

        if ($type === 'inventory.delta') {
            $rules += [
                'delta' => ['required', 'integer', 'between:-1000000,1000000', 'not_in:0'],
                'reason' => ['required', 'string', 'max:240'],
                'saleId' => ['nullable', 'integer', 'min:1'],
            ];
        } elseif ($type === 'inventory.set') {
            $rules += [
                'quantity' => ['required', 'integer', 'min:0', 'max:2147483647'],
                'reason' => ['required', 'string', 'max:240'],
            ];
        } elseif ($type === 'product.price') {
            $rules['priceCents'] = ['required', 'integer', 'min:0', 'max:2147483647'];
            $rules['costCents'] = ['nullable', 'integer', 'min:0', 'max:2147483647'];
            $rules['description'] = ['sometimes', 'nullable', 'string', 'max:1000'];
        } elseif ($type === 'promotion.upsert') {
            $rules += [
                'promotionId' => ['required', 'integer', 'min:1'],
                'discountBasisPoints' => ['required', 'integer', 'between:1,9999'],
                'startsAt' => ['required', 'date'],
                'endsAt' => ['required', 'date', 'after:startsAt'],
            ];
        } elseif ($type === 'promotion.cancel') {
            $rules['promotionId'] = ['required', 'integer', 'min:1'];
        }

        $validator = Validator::make($payload, $rules);
        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $validator->validated();
    }

    private function decimalFromCents(int $cents): string
    {
        return intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
}
