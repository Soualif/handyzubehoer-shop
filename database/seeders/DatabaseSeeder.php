<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DeviceModel;
use Illuminate\Database\Seeder;

/**
 * Starting categories and phone models. Safe to run again: existing rows are kept.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'cases' => ['de' => 'Hüllen', 'fr' => 'Coques', 'it' => 'Cover', 'en' => 'Cases'],
            'screen-protectors' => ['de' => 'Displayschutz', 'fr' => 'Protections d\'écran', 'it' => 'Pellicole', 'en' => 'Screen protectors'],
            'chargers' => ['de' => 'Ladegeräte', 'fr' => 'Chargeurs', 'it' => 'Caricabatterie', 'en' => 'Chargers'],
            'cables' => ['de' => 'Kabel', 'fr' => 'Câbles', 'it' => 'Cavi', 'en' => 'Cables'],
            'power-banks' => ['de' => 'Powerbanks', 'fr' => 'Batteries externes', 'it' => 'Power bank', 'en' => 'Power banks'],
            'audio' => ['de' => 'Kopfhörer', 'fr' => 'Écouteurs', 'it' => 'Auricolari', 'en' => 'Earphones'],
            'holders' => ['de' => 'Halterungen', 'fr' => 'Supports', 'it' => 'Supporti', 'en' => 'Holders'],
        ];

        foreach (array_keys($categories) as $position => $slug) {
            Category::firstOrCreate(['slug' => $slug], ['name' => $categories[$slug], 'position' => $position]);
        }

        $models = [
            'Apple' => ['iPhone 17 Pro Max', 'iPhone 17 Pro', 'iPhone Air', 'iPhone 17', 'iPhone 16 Pro Max', 'iPhone 16 Pro', 'iPhone 16', 'iPhone 16e', 'iPhone 15 Pro', 'iPhone 15', 'iPhone 14', 'iPhone 13'],
            'Samsung' => ['Galaxy S25 Ultra', 'Galaxy S25+', 'Galaxy S25', 'Galaxy S24 Ultra', 'Galaxy S24', 'Galaxy A56', 'Galaxy A36', 'Galaxy A55', 'Galaxy Z Flip7', 'Galaxy Z Fold7'],
            'Google' => ['Pixel 10 Pro', 'Pixel 10', 'Pixel 9a', 'Pixel 9'],
            'Xiaomi' => ['Redmi Note 14 Pro', 'Xiaomi 15'],
        ];

        foreach ($models as $brand => $names) {
            foreach ($names as $position => $name) {
                DeviceModel::firstOrCreate(
                    ['slug' => str($brand.' '.$name)->slug()->toString()],
                    ['brand' => $brand, 'name' => $name, 'position' => $position],
                );
            }
        }
    }
}
