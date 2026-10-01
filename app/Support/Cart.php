<?php

namespace App\Support;

use App\Models\ProductVariant;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * Shopping cart kept in the session as [variant id => quantity].
 */
class Cart
{
    private const KEY = 'cart';

    public function __construct(private Session $session) {}

    /**
     * @return array<int, int>
     */
    public function quantities(): array
    {
        return $this->session->get(self::KEY, []);
    }

    public function add(ProductVariant $variant, int $quantity = 1): void
    {
        $this->set($variant, ($this->quantities()[$variant->id] ?? 0) + $quantity);
    }

    public function set(ProductVariant $variant, int $quantity): void
    {
        $quantities = $this->quantities();
        $quantity = min($quantity, config('shop.max_quantity'), $variant->stock);

        if ($quantity > 0) {
            $quantities[$variant->id] = $quantity;
        } else {
            unset($quantities[$variant->id]);
        }

        $this->session->put(self::KEY, $quantities);
    }

    public function clear(): void
    {
        $this->session->forget(self::KEY);
    }

    public function count(): int
    {
        return array_sum($this->quantities());
    }

    /**
     * Cart lines with their variant, skipping variants that can no longer be bought.
     *
     * @return Collection<int, array{variant: ProductVariant, quantity: int, total: int}>
     */
    public function lines(): Collection
    {
        $quantities = $this->quantities();

        return ProductVariant::with('product.images')
            ->whereIn('id', array_keys($quantities))
            ->get()
            ->filter(fn (ProductVariant $variant) => $variant->isPurchasable())
            ->map(fn (ProductVariant $variant) => [
                'variant' => $variant,
                'quantity' => $quantity = min($quantities[$variant->id], $variant->stock),
                'total' => $variant->price * $quantity,
            ])
            ->values();
    }

    public static function subtotal(Collection $lines): int
    {
        return $lines->sum('total');
    }

    public static function shipping(int $subtotal): int
    {
        return $subtotal >= config('shop.free_shipping_from') ? 0 : config('shop.shipping_fee');
    }
}
