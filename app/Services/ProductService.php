<?php

namespace App\Services;

use App\Events\LowStockDetected;

use App\Models\Product;
use App\Models\ProductImage;

use Illuminate\Http\UploadedFile;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    // ── Create ────────────────────────────────────────────────

    public function create(array $data, array $images = []): Product
    {
        return DB::transaction(function () use ($data, $images) {

            $product = Product::create(
                $this->prepareData($data)
            );

            $this->handleImages($product, $images);

            return $product;
        });
    }

    // ── Update ────────────────────────────────────────────────

    public function update(
        Product $product,
        array $data,
        array $newImages = [],
        array $deleteImageIds = []
    ): Product {

        return DB::transaction(function () use (
            $product,
            $data,
            $newImages,
            $deleteImageIds
        ) {

            $product->update(
                $this->prepareData($data)
            );

            // ── Cache invalidation ─────────────────────────

            $this->cacheService->forgetProduct(
                $product->uuid
            );

            $this->cacheService->forgetRelated(
                $product->id
            );

            // ── Low stock detection ───────────────────────

            if (
                $product->isLowStock()
                || $product->isOutOfStock()
            ) {

                event(new LowStockDetected(
                    $product->fresh()
                ));
            }

            $this->deleteImages(
                $product,
                $deleteImageIds
            );

            $this->handleImages(
                $product,
                $newImages
            );

            return $product->fresh();
        });
    }

    // ── Delete ────────────────────────────────────────────────

    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {

            $product->clearMediaCollection(
                'product-images'
            );

            $product->images()->delete();

            $product->delete();

            // ── Cache invalidation ───────────────────────

            $this->cacheService->forgetProduct(
                $product->uuid
            );
        });
    }

    // ── Image Handling ────────────────────────────────────────

    private function handleImages(
        Product $product,
        array $uploads
    ): void {

        $hasPrimary = $product->images()
            ->where('is_primary', true)
            ->exists();

        foreach ($uploads as $index => $file) {

            /** @var UploadedFile $file */

            $media = $product->addMedia($file)
                ->usingName(
                    $product->name . ' image ' . ($index + 1)
                )
                ->toMediaCollection('product-images');

            $isPrimary = ! $hasPrimary && $index === 0;

            $product->images()->create([
                'path'       => $media->getPath(),
                'alt_text'   => $product->name,
                'is_primary' => $isPrimary,
                'sort_order' => $product->images()->count(),
            ]);

            if ($isPrimary) {
                $hasPrimary = true;
            }
        }
    }

    private function deleteImages(
        Product $product,
        array $ids
    ): void {

        if (empty($ids)) {
            return;
        }

        $images = $product->images()
            ->whereIn('id', $ids)
            ->get();

        foreach ($images as $image) {

            // Remove from Spatie media collection

            $media = $product->getMedia('product-images')
                ->first(
                    fn ($m) => $m->getPath() === $image->path
                );

            $media?->delete();

            $wasPrimary = $image->is_primary;

            $image->delete();

            // Re-assign primary image

            if ($wasPrimary) {

                $product->images()
                    ->oldest('sort_order')
                    ->first()
                    ?->update([
                        'is_primary' => true
                    ]);
            }
        }
    }

    // ── Helpers ───────────────────────────────────────────────

    private function prepareData(array $data): array
    {
        if (
            empty($data['slug'])
            && ! empty($data['name'])
        ) {

            $data['slug'] = Str::slug(
                $data['name']
            );
        }

        // HTML checkboxes

        $data['is_active'] = (bool) (
            $data['is_active'] ?? false
        );

        $data['is_featured'] = (bool) (
            $data['is_featured'] ?? false
        );

        $data['track_inventory'] = (bool) (
            $data['track_inventory'] ?? true
        );

        return $data;
    }
}