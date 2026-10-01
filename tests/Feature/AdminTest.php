<?php

namespace Tests\Feature;

use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\RelationManagers\VariantsRelationManager;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_can_open_the_back_office(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();

        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_admin_pages_render(): void
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();

        $product = Product::factory()->withVariant()->create();
        $order = Order::factory()->paid()->create();

        $this->actingAs($admin);
        $this->get('/admin/products')->assertOk();
        $this->get("/admin/products/{$product->slug}/edit")->assertOk();
        $this->get('/admin/categories')->assertOk();
        $this->get('/admin/device-models')->assertOk();
        $this->get('/admin/orders')->assertOk()->assertSee($order->number);
        $this->get("/admin/orders/{$order->number}/edit")->assertOk();
    }

    public function test_variant_price_is_edited_in_francs_and_stored_in_cents(): void
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();

        $product = Product::factory()->withVariant(1990)->create();
        $variant = $product->variants->first();

        $this->actingAs($admin);

        Livewire::test(VariantsRelationManager::class, ['ownerRecord' => $product, 'pageClass' => EditProduct::class])
            ->callAction(TestAction::make('edit')->table($variant), data: [
                'price' => '24.90',
                'price_locked' => true,
                'name' => ['de' => 'Schwarz', 'fr' => 'Noir', 'it' => 'Nero', 'en' => 'Black'],
            ])
            ->assertHasNoFormErrors();

        $variant->refresh();
        $this->assertSame(2490, $variant->price);
        $this->assertTrue($variant->price_locked);
        $this->assertSame('Noir', $variant->translate('name', 'fr'));
    }
}
