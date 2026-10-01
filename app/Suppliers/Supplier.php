<?php

namespace App\Suppliers;

use App\Models\Order;

interface Supplier
{
    /**
     * Product details from the supplier catalogue.
     */
    public function fetchProduct(string $productId): SupplierProduct;

    /**
     * Units available for a variant.
     */
    public function stock(string $variantId): int;

    /**
     * Create the order at the supplier and return the supplier's order id.
     */
    public function placeOrder(Order $order): string;

    /**
     * Current state of a supplier order.
     */
    public function orderStatus(string $supplierOrderId): SupplierOrderStatus;
}
