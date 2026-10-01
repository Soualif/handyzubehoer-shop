<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * A few sample products to try the shop locally (php artisan db:seed --class=DemoSeeder).
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DatabaseSeeder::class);

        $demo = [
            ['cases', 'demo-silicone-case', ['de' => 'Silikonhülle', 'fr' => 'Coque en silicone', 'it' => 'Cover in silicone', 'en' => 'Silicone case'], [
                ['Schwarz', 'Noir', 'Nero', 'Black', 1990], ['Blau', 'Bleu', 'Blu', 'Blue', 1990],
            ], ['apple-iphone-17', 'apple-iphone-16']],
            ['chargers', 'demo-usb-c-charger-30w', ['de' => 'USB-C Ladegerät 30 W', 'fr' => 'Chargeur USB-C 30 W', 'it' => 'Caricabatterie USB-C 30 W', 'en' => 'USB-C charger 30 W'], [
                [null, null, null, null, 2490],
            ], []],
            ['cables', 'demo-usb-c-cable', ['de' => 'USB-C Kabel 1 m', 'fr' => 'Câble USB-C 1 m', 'it' => 'Cavo USB-C 1 m', 'en' => 'USB-C cable 1 m'], [
                [null, null, null, null, 990],
            ], []],
        ];

        foreach ($demo as [$category, $slug, $name, $variants, $devices]) {
            $product = Product::updateOrCreate(['slug' => $slug], [
                'category_id' => Category::where('slug', $category)->value('id'),
                'name' => $name,
                'description' => ['en' => '<p>Demo product.</p>', 'de' => '<p>Demo-Produkt.</p>', 'fr' => '<p>Produit de démonstration.</p>', 'it' => '<p>Prodotto dimostrativo.</p>'],
                'is_active' => true,
            ]);

            foreach ($variants as $i => [$de, $fr, $it, $en, $price]) {
                $product->variants()->updateOrCreate(['sku' => strtoupper($slug).'-'.$i], [
                    'name' => $en ? compact('de', 'fr', 'it', 'en') : null,
                    'price' => $price,
                    'stock' => 50,
                ]);
            }

            $product->deviceModels()->sync(DeviceModel::whereIn('slug', $devices)->pluck('id'));
        }
    }
}
