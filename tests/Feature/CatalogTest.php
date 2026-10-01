<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_lists_visible_products_in_the_visitor_language(): void
    {
        Product::factory()->withVariant(2490)->create(['name' => ['en' => 'Blue case', 'de' => 'Blaue Hülle']]);
        Product::factory()->inactive()->withVariant()->create(['name' => ['en' => 'Hidden case']]);
        Product::factory()->withVariant(stock: 0)->create(['name' => ['en' => 'Sold out case']]);

        $this->get('/de')
            ->assertOk()
            ->assertSee('Blaue Hülle')
            ->assertSee('CHF 24.90')
            ->assertDontSee('Hidden case')
            ->assertDontSee('Sold out case');
    }

    public function test_missing_translation_falls_back_to_english(): void
    {
        Product::factory()->withVariant()->create(['name' => ['en' => 'Magnetic holder']]);

        $this->get('/it/products')->assertSee('Magnetic holder');
    }

    public function test_products_can_be_filtered_by_category_phone_and_search(): void
    {
        $cases = Category::factory()->create(['slug' => 'cases']);
        $iphone = DeviceModel::factory()->create(['slug' => 'apple-iphone-17']);

        $case = Product::factory()->withVariant()->create(['category_id' => $cases->id, 'name' => ['en' => 'Clear case', 'de' => 'Transparente Hülle']]);
        $case->deviceModels()->attach($iphone);
        Product::factory()->withVariant()->create(['name' => ['en' => 'USB cable', 'de' => 'USB Kabel']]);

        $this->get('/en/category/cases')->assertSee('Clear case')->assertDontSee('USB cable');
        $this->get('/en/products?device=apple-iphone-17')->assertSee('Clear case')->assertDontSee('USB cable');
        $this->get('/de/products?q=hülle')->assertSee('Transparente Hülle')->assertDontSee('USB Kabel');
        $this->get('/de/products?q=kabel')->assertSee('USB Kabel')->assertDontSee('Transparente Hülle');
    }

    public function test_product_page(): void
    {
        $iphone = DeviceModel::factory()->create(['brand' => 'Apple', 'name' => 'iPhone 17']);
        $product = Product::factory()->withVariant(1990)->create(['slug' => 'clear-case', 'name' => ['en' => 'Clear case', 'fr' => 'Coque transparente']]);
        $product->deviceModels()->attach($iphone);

        $this->get('/fr/product/clear-case')
            ->assertOk()
            ->assertSee('Coque transparente')
            ->assertSee('CHF 19.90')
            ->assertSee('Apple iPhone 17')
            ->assertSee('hreflang="de" href="'.url('/de/product/clear-case').'"', false);
    }

    public function test_description_html_is_sanitized(): void
    {
        Product::factory()->withVariant()->create([
            'slug' => 'clear-case',
            'description' => ['en' => '<p onclick="steal()">Strong <b>magnets</b></p><script>alert(1)</script><img src=x onerror=alert(1)>'],
        ]);

        $this->get('/en/product/clear-case')
            ->assertSee('<p>Strong <b>magnets</b></p>', false)
            ->assertDontSee('onclick', false)
            ->assertDontSee('<script>alert', false)
            ->assertDontSee('onerror', false);
    }

    public function test_hidden_products_are_not_found(): void
    {
        Product::factory()->inactive()->withVariant()->create(['slug' => 'secret']);

        $this->get('/en/product/secret')->assertNotFound();
    }

    public function test_legal_pages_exist_in_every_language(): void
    {
        foreach (['de', 'fr', 'it', 'en'] as $locale) {
            foreach (['impressum', 'terms', 'privacy', 'shipping-returns'] as $page) {
                $this->get("/{$locale}/info/{$page}")->assertOk();
            }
        }

        $this->get('/de/info/impressum')->assertSee('Impressum');
        $this->get('/fr/info/terms')->assertSee('Conditions générales de vente');
    }
}
