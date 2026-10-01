<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'name', 'sku', 'supplier_variant_id', 'image_url', 'cost_price', 'price', 'compare_at_price', 'price_locked', 'stock', 'is_active'])]
class ProductVariant extends Model
{
    use HasFactory, HasTranslations;

    protected array $translatable = ['name'];

    protected $attributes = [
        'cost_price' => 0,
        'price_locked' => false,
        'stock' => 0,
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'price_locked' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopePurchasable(Builder $query): void
    {
        $query->where('is_active', true)->where('stock', '>', 0);
    }

    public function isPurchasable(): bool
    {
        return $this->is_active && $this->stock > 0 && $this->product?->is_active;
    }

    /**
     * Product name plus the variant name, e.g. "Silikonhülle – Schwarz".
     */
    public function fullName(?string $locale = null): string
    {
        $variant = $this->translate('name', $locale);
        $product = $this->product->translate('name', $locale);

        return $variant === '' ? $product : "{$product} – {$variant}";
    }
}
