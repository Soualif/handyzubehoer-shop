<?php

namespace App\Suppliers;

final readonly class SupplierProduct
{
    /**
     * @param  list<string>  $images
     * @param  list<array{id: string, sku: string, name: string, cost: int, image: ?string}>  $variants  cost in USD cents
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public array $images,
        public array $variants,
    ) {}
}
