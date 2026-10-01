<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['category_id', 'name', 'description', 'slug', 'supplier', 'supplier_product_id', 'is_active', 'synced_at'])]
class Product extends Model
{
    use HasFactory, HasTranslations;

    protected array $translatable = ['name', 'description'];

    protected $attributes = [
        'is_active' => false,
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function deviceModels(): BelongsToMany
    {
        return $this->belongsToMany(DeviceModel::class);
    }

    /**
     * Products a customer can see and buy.
     */
    public function scopeVisible(Builder $query): void
    {
        $query->where('is_active', true)
            ->whereHas('variants', fn (Builder $variants) => $variants->purchasable());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function lowestPrice(): ?int
    {
        return $this->variants->where('is_active', true)->min('price');
    }

    public function mainImageUrl(): ?string
    {
        return $this->images->first()?->url ?? $this->variants->firstWhere('image_url')?->image_url;
    }
}
