<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('device_models', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->json('name');
            $table->json('description')->nullable();
            $table->string('slug')->unique();
            $table->string('supplier')->nullable();
            $table->string('supplier_product_id')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['supplier', 'supplier_product_id']);
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->json('name')->nullable();
            $table->string('sku')->unique();
            $table->string('supplier_variant_id')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('cost_price')->default(0)->comment('Supplier price in USD cents');
            $table->unsignedInteger('price')->comment('Sale price in CHF cents, VAT included');
            $table->unsignedInteger('compare_at_price')->nullable()->comment('Former price in CHF cents');
            $table->boolean('price_locked')->default(false)->comment('Keep the price when syncing with the supplier');
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('device_model_product', function (Blueprint $table) {
            $table->foreignId('device_model_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['device_model_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_model_product');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('device_models');
        Schema::dropIfExists('categories');
    }
};
