<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Publication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'eventId' => ['required', 'uuid'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'productSkus' => ['required', 'array', 'min:1', 'max:8'],
            'productSkus.*' => ['required', 'string', 'max:100', 'distinct:ignore_case'],
            'durationHours' => ['required', 'integer', 'min:1', 'max:8783'],
            'imageData' => ['required', 'string', 'max:1400000'],
        ]);

        [$imageBytes, $imageExtension] = $this->decodeImage($data['imageData']);
        $payloadHash = hash('sha256', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $imagePath = 'publications/'.Str::uuid().'.'.$imageExtension;

        try {
            $result = DB::transaction(function () use ($data, $payloadHash, $imageBytes, $imagePath): array {
                $existing = Publication::query()
                    ->where('event_id', $data['eventId'])
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    if (! hash_equals($existing->payload_hash, $payloadHash)) {
                        abort(409, 'O identificador do evento já foi utilizado com outros dados.');
                    }

                    return ['publicationId' => $existing->id, 'duplicate' => true];
                }

                $items = [];
                foreach ($data['productSkus'] as $position => $sku) {
                    $variant = ProductVariant::query()
                        ->with('product')
                        ->where('sku', $sku)
                        ->where('active', true)
                        ->lockForUpdate()
                        ->first();
                    $product = $variant?->product;

                    if (! $variant) {
                        $product = Product::query()
                            ->where('sku', $sku)
                            ->where('active', true)
                            ->lockForUpdate()
                            ->first();
                    }

                    if (! $product || ! $product->active) {
                        abort(422, 'Não foi encontrada uma peça ativa com o código '.$sku.'.');
                    }

                    $stock = (int) ($variant?->qty ?? $product->qty);
                    if ($stock < 1) {
                        abort(422, "A peça com o código ".$sku." está sem estoque e não pode ser publicada.");
                    }

                    $activePublicationCount = DB::table("publication_items")
                        ->join("publications", "publications.id", "=", "publication_items.publication_id")
                        ->where("publication_items.sku", $sku)
                        ->where(function ($query) {
                            $query->whereNull("publications.expires_at")
                                ->orWhere("publications.expires_at", ">", now());
                        })
                        ->count();

                    if ($activePublicationCount >= 3) {
                        abort(409, "A peça com o código ".$sku." já atingiu o limite de 3 publicações ativas.");
                    }


                    $variantLabel = collect([
                        $variant?->attributes['color'] ?? $variant?->color?->name,
                        $variant?->attributes['size'] ?? null,
                    ])->filter()->implode(' ');

                    $items[] = [
                        'product_id' => $product->id,
                        'variant_id' => $variant?->id,
                        'sku' => $sku,
                        'item_name' => trim($product->name.' '.$variantLabel),
                        'position' => $position,
                    ];
                }

                if (! Storage::disk('public')->put($imagePath, $imageBytes)) {
                    throw new \RuntimeException('Não foi possível salvar a foto da publicação.');
                }

                $publication = Publication::query()->create([
                    'event_id' => $data['eventId'],
                    'payload_hash' => $payloadHash,
                    'title' => trim($data['title']),
                    'description' => $data['description'] ?? null,
                    'image_path' => $imagePath,
                    'expires_at' => now()->addHours((int) $data['durationHours']),
                ]);

                foreach ($items as $item) {
                    $publication->items()->create($item);
                }

                return ['publicationId' => $publication->id, 'duplicate' => false];
            }, 3);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($imagePath);
            throw $exception;
        }

        if ($result['duplicate']) {
            Storage::disk('public')->delete($imagePath);
        }

        return response()->json([
            'received' => true,
            ...$result,
        ], $result['duplicate'] ? 200 : 201);
    }

    private function decodeImage(string $dataUrl): array
    {
        if (! preg_match('/^data:image\/(jpeg|png|webp);base64,([A-Za-z0-9+\/=]+)$/', $dataUrl, $matches)) {
            abort(422, 'A foto da publicação deve ser JPEG, PNG ou WebP.');
        }

        $bytes = base64_decode($matches[2], true);
        $isValid = match ($matches[1]) {
            'jpeg' => is_string($bytes) && str_starts_with($bytes, "\xFF\xD8\xFF"),
            'png' => is_string($bytes) && str_starts_with($bytes, "\x89PNG\r\n\x1A\n"),
            'webp' => is_string($bytes) && substr($bytes, 0, 4) === 'RIFF' && substr($bytes, 8, 4) === 'WEBP',
        };

        if (! $isValid || strlen($bytes) > 1024 * 1024) {
            abort(422, 'A foto da publicação é inválida ou ultrapassa 1 MB.');
        }

        return [$bytes, $matches[1] === 'jpeg' ? 'jpg' : $matches[1]];
    }
}
