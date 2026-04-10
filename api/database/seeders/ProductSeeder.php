<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');

        $products = [
            // Guitars
            ['name' => 'Fender Stratocaster', 'description' => 'Classic electric guitar with alder body and maple neck.', 'price' => 1299.99, 'category' => 'Guitars', 'image_url' => null],
            ['name' => 'Gibson Les Paul Standard', 'description' => 'Iconic solid body electric guitar with humbucker pickups.', 'price' => 2499.00, 'category' => 'Guitars', 'image_url' => null],
            ['name' => 'Taylor 214ce', 'description' => 'Grand auditorium acoustic-electric guitar with spruce top.', 'price' => 999.00, 'category' => 'Guitars', 'image_url' => null],
            ['name' => 'Epiphone Les Paul Classic', 'description' => 'Affordable Les Paul with ProBucker pickups.', 'price' => 449.00, 'category' => 'Guitars', 'image_url' => null],

            // Basses
            ['name' => 'Fender Precision Bass', 'description' => 'The foundation of modern bass playing.', 'price' => 1149.99, 'category' => 'Basses', 'image_url' => null],
            ['name' => 'Music Man StingRay', 'description' => 'Active humbucking bass with punchy tone.', 'price' => 1899.00, 'category' => 'Basses', 'image_url' => null],
            ['name' => 'Squier Affinity Jazz Bass', 'description' => 'Entry-level jazz bass with dual single-coil pickups.', 'price' => 299.99, 'category' => 'Basses', 'image_url' => null],

            // Drums
            ['name' => 'Pearl Export 5-Piece Kit', 'description' => 'Complete drum kit ideal for beginners and intermediate players.', 'price' => 799.00, 'category' => 'Drums', 'image_url' => null],
            ['name' => 'Roland TD-17KVX', 'description' => 'Electronic drum kit with mesh heads and advanced sound module.', 'price' => 1799.99, 'category' => 'Drums', 'image_url' => null],

            // Keyboards
            ['name' => 'Yamaha P-45', 'description' => '88-key weighted action digital piano.', 'price' => 449.99, 'category' => 'Keyboards', 'image_url' => null],
            ['name' => 'Roland FP-30X', 'description' => 'Portable digital piano with SuperNATURAL sound engine.', 'price' => 699.00, 'category' => 'Keyboards', 'image_url' => null],
            ['name' => 'Nord Stage 3', 'description' => 'Professional stage piano with organ, piano and synth sections.', 'price' => 3999.00, 'category' => 'Keyboards', 'image_url' => null],

            // Accessories
            ['name' => 'Ernie Ball Super Slinky Strings', 'description' => 'Electric guitar strings 9-42 gauge.', 'price' => 6.99, 'category' => 'Accessories', 'image_url' => null],
            ['name' => 'Boss TU-3 Chromatic Tuner', 'description' => 'Professional chromatic pedal tuner.', 'price' => 89.99, 'category' => 'Accessories', 'image_url' => null],
            ['name' => 'Mogami Gold Instrument Cable 10ft', 'description' => 'Premium low-noise instrument cable.', 'price' => 49.99, 'category' => 'Accessories', 'image_url' => null],
        ];

        foreach ($products as $data) {
            $categoryId = $categories[$data['category']] ?? null;
            if (!$categoryId) {
                continue;
            }

            Product::firstOrCreate(
                ['name' => $data['name']],
                [
                    'description' => $data['description'],
                    'price'       => $data['price'],
                    'category_id' => $categoryId,
                    'image_url'   => $data['image_url'],
                ]
            );
        }
    }
}
