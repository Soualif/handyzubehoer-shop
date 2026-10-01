<?php

namespace App\Suppliers;

final readonly class SupplierOrderStatus
{
    public function __construct(
        public string $status,
        public ?string $trackingNumber = null,
        public ?string $carrier = null,
    ) {}

    public function isShipped(): bool
    {
        return $this->trackingNumber !== null && $this->trackingNumber !== '';
    }
}
