<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_update_and_remove(): void
    {
        $variant = Product::factory()->withVariant(1990, stock: 5)->create(['name' => ['en' => 'Clear case']])->variants->first();

        $this->post('/en/cart', ['variant' => $variant->id, 'quantity' => 2])->assertRedirect('/en/cart');

        $this->get('/en/cart')
            ->assertSee('Clear case')
            ->assertSee('CHF 39.80')
            ->assertSee('CHF 4.90')
            ->assertSee('CHF 44.70');

        $this->patch("/en/cart/{$variant->id}", ['quantity' => 3]);
        $this->get('/en/cart')->assertSee('CHF 59.70')->assertSee('Free');

        $this->patch("/en/cart/{$variant->id}", ['quantity' => 0]);
        $this->get('/en/cart')->assertSee('Your cart is empty.');
    }

    public function test_quantity_is_capped_by_stock(): void
    {
        $variant = Product::factory()->withVariant(1000, stock: 2)->create()->variants->first();

        $this->post('/en/cart', ['variant' => $variant->id, 'quantity' => 5]);

        $this->assertSame([$variant->id => 2], session('cart'));
    }

    public function test_out_of_stock_variant_cannot_be_added(): void
    {
        $variant = Product::factory()->withVariant(stock: 0)->create()->variants->first();

        $this->from('/en/products')->post('/en/cart', ['variant' => $variant->id])->assertSessionHasErrors('variant');
        $this->assertNull(session('cart'));
    }
}
