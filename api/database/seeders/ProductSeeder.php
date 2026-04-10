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
            ['name' => 'Fender Stratocaster', 'description' => 'Guitarra elétrica clássica com corpo em alder e braço em maple.', 'price' => 1299.99, 'category' => 'Guitars', 'image_url' => null],
            ['name' => 'Gibson Les Paul Standard', 'description' => 'Icônica guitarra elétrica de corpo sólido com captadores humbucker.', 'price' => 2499.00, 'category' => 'Guitars', 'image_url' => null],
            ['name' => 'Taylor 214ce', 'description' => 'Violão eletroacústico grand auditorium com tampo em abeto.', 'price' => 999.00, 'category' => 'Guitars', 'image_url' => null],
            ['name' => 'Epiphone Les Paul Classic', 'description' => 'Les Paul acessível com captadores ProBucker.', 'price' => 449.00, 'category' => 'Guitars', 'image_url' => null],

            // Basses
            ['name' => 'Fender Precision Bass', 'description' => 'A base do baixo moderno, referência em som e confiabilidade.', 'price' => 1149.99, 'category' => 'Basses', 'image_url' => null],
            ['name' => 'Music Man StingRay', 'description' => 'Baixo ativo com humbucker de som encorpado e presença marcante.', 'price' => 1899.00, 'category' => 'Basses', 'image_url' => null],
            ['name' => 'Squier Affinity Jazz Bass', 'description' => 'Baixo jazz de entrada com dois captadores single-coil.', 'price' => 299.99, 'category' => 'Basses', 'image_url' => null],

            // Drums
            ['name' => 'Pearl Export 5-Piece Kit', 'description' => 'Bateria completa de 5 peças ideal para iniciantes e intermediários.', 'price' => 799.00, 'category' => 'Drums', 'image_url' => null],
            ['name' => 'Roland TD-17KVX', 'description' => 'Bateria eletrônica com peles mesh e módulo de som avançado.', 'price' => 1799.99, 'category' => 'Drums', 'image_url' => null],

            // Keyboards
            ['name' => 'Yamaha P-45', 'description' => 'Piano digital com 88 teclas de ação pesada.', 'price' => 449.99, 'category' => 'Keyboards', 'image_url' => null],
            ['name' => 'Roland FP-30X', 'description' => 'Piano digital portátil com motor de som SuperNATURAL.', 'price' => 699.00, 'category' => 'Keyboards', 'image_url' => null],
            ['name' => 'Nord Stage 3', 'description' => 'Piano de palco profissional com seções de órgão, piano e sintetizador.', 'price' => 3999.00, 'category' => 'Keyboards', 'image_url' => null],

            // Accessories
            ['name' => 'Ernie Ball Super Slinky Strings', 'description' => 'Cordas para guitarra elétrica calibre 9-42.', 'price' => 6.99, 'category' => 'Accessories', 'image_url' => null],
            ['name' => 'Boss TU-3 Chromatic Tuner', 'description' => 'Afinador cromático profissional em formato de pedal.', 'price' => 89.99, 'category' => 'Accessories', 'image_url' => null],
            ['name' => 'Mogami Gold Instrument Cable 10ft', 'description' => 'Cabo de instrumento premium com baixíssimo ruído.', 'price' => 49.99, 'category' => 'Accessories', 'image_url' => null],
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
