<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['brand', 'name', 'slug', 'position'])]
class DeviceModel extends Model
{
    use HasFactory;

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function fullName(): string
    {
        return str_starts_with($this->name, $this->brand) ? $this->name : "{$this->brand} {$this->name}";
    }
}
