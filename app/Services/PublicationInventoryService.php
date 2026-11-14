<?php

namespace App\Services;

use App\Models\Publication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PublicationInventoryService
{
    public function removeUnavailable(): int
    {
        $imagePaths = [];
        $removed = 0;

        DB::transaction(function () use (&$imagePaths, &$removed): void {
            $publications = Publication::query()
                ->with(['items.product', 'items.variant'])
                ->lockForUpdate()
                ->get();

            foreach ($publications as $publication) {
                if ($publication->isAvailable()) {
                    continue;
                }

                $imagePaths[] = $publication->image_path;
                $publication->delete();
                $removed++;
            }
        }, 3);

        if ($imagePaths !== []) {
            Storage::disk('public')->delete($imagePaths);
        }

        return $removed;
    }
}
