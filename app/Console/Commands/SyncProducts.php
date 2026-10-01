<?php

namespace App\Console\Commands;

use App\Actions\ImportSupplierProduct;
use App\Models\Product;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Signature('shop:sync-products')]
#[Description('Refresh prices and stock of supplier products')]
class SyncProducts extends Command
{
    public function handle(ImportSupplierProduct $import): int
    {
        Product::whereNotNull('supplier_product_id')->orderBy('synced_at')->each(function (Product $product) use ($import) {
            try {
                $import->handle($product->supplier_product_id);
            } catch (Throwable $e) {
                Log::warning('Product sync failed', ['product' => $product->id, 'error' => $e->getMessage()]);
                $this->error("{$product->slug}: {$e->getMessage()}");
            }
        });

        return self::SUCCESS;
    }
}
