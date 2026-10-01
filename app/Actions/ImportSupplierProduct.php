<?php

namespace App\Actions;

use App\Models\Product;
use App\Suppliers\CjDropshipping;
use App\Suppliers\Supplier;
use App\Support\Pricing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Create or refresh a product from the supplier catalogue. New products stay hidden
 * until an admin has checked the translations and activated them.
 */
class ImportSupplierProduct
{
    public function __construct(private Supplier $supplier) {}

    public function handle(string $supplierProductId): Product
    {
        $remote = $this->supplier->fetchProduct($supplierProductId);

        return DB::transaction(function () use ($remote) {
            $product = Product::firstOrNew([
                'supplier' => CjDropshipping::NAME,
                'supplier_product_id' => $remote->id,
            ]);

            if (! $product->exists) {
                $product->fill([
                    'name' => ['en' => $remote->name],
                    'description' => ['en' => $remote->description],
                    'slug' => $this->uniqueSlug($remote->name),
                    'is_active' => false,
                ]);
            }

            $product->synced_at = now();
            $product->save();

            if ($product->images()->doesntExist()) {
                foreach ($remote->images as $position => $url) {
                    $product->images()->create(['url' => $url, 'position' => $position]);
                }
            }

            $seen = [];

            foreach ($remote->variants as $remoteVariant) {
                $variant = $product->variants()->firstOrNew(['supplier_variant_id' => $remoteVariant['id']]);

                if (! $variant->exists) {
                    $variant->fill([
                        'sku' => $remoteVariant['sku'],
                        'name' => ['en' => $remoteVariant['name']],
                        'image_url' => $remoteVariant['image'],
                    ]);
                }

                $variant->cost_price = $remoteVariant['cost'];

                if (! $variant->price_locked) {
                    $variant->price = Pricing::salePrice($remoteVariant['cost']);
                }

                $variant->stock = $this->supplier->stock($remoteVariant['id']);
                $variant->save();

                $seen[] = $variant->id;
            }

            // Variants the supplier no longer sells.
            $product->variants()->whereNotIn('id', $seen)->update(['is_active' => false, 'stock' => 0]);

            return $product;
        });
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug(Str::limit($name, 60, '')) ?: 'product';
        $slug = $base;

        for ($i = 2; Product::where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}
