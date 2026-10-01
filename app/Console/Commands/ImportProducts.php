<?php

namespace App\Console\Commands;

use App\Actions\ImportSupplierProduct;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('shop:import {ids* : CJ Dropshipping product ids (pid)}')]
#[Description('Import products from CJ Dropshipping (they stay hidden until activated in the admin)')]
class ImportProducts extends Command
{
    public function handle(ImportSupplierProduct $import): int
    {
        $failed = false;

        foreach ($this->argument('ids') as $id) {
            try {
                $product = $import->handle($id);
                $this->info("{$id}: {$product->translate('name', 'en')} ({$product->variants()->count()} variants)");
            } catch (Throwable $e) {
                $failed = true;
                $this->error("{$id}: {$e->getMessage()}");
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
