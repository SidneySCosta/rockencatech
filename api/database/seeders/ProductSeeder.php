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
            // Roupas
            [
                'name'        => 'Camiseta Essencial',
                'description' => 'Camiseta confeccionada em 100% algodão orgânico, com caimento perfeito e disponível nos tamanhos P, M, G e GG. Lavável à máquina.',
                'price'       => 89.90,
                'category'    => 'Roupas',
                'image_url'   => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600&h=600&fit=crop',
            ],
            [
                'name'        => 'Calça Slim',
                'description' => 'Calça slim fit em sarja com 2% elastano para mais conforto. Bolsos frontais e traseiros, cós com passantes para cinto e fechamento em zíper e botão.',
                'price'       => 189.90,
                'category'    => 'Roupas',
                'image_url'   => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=600&h=600&fit=crop',
            ],
            [
                'name'        => 'Jaqueta Oversized',
                'description' => 'Jaqueta jeans em algodão com lavagem clara. Corte oversized, bolsos frontais com aba e fechamento em botões metálicos. Peça-chave para looks descontraídos.',
                'price'       => 259.90,
                'category'    => 'Roupas',
                'image_url'   => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=600&h=600&fit=crop',
            ],
            [
                'name'        => 'Vestido Midi Floral',
                'description' => 'Vestido midi com estampa floral delicada, confeccionado em viscose leve. Alças finas com regulagem e saia ampla. Ideal para o dia a dia e ocasiões especiais.',
                'price'       => 219.90,
                'category'    => 'Roupas',
                'image_url'   => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=600&h=600&fit=crop',
            ],
            [
                'name'        => 'Moletom Classic',
                'description' => 'Moletom unissex em fleece premium, interior felpudo para máximo conforto. Bolso canguru, punhos e barra em ribana. Lavável à máquina.',
                'price'       => 149.90,
                'category'    => 'Roupas',
                'image_url'   => 'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=600&h=600&fit=crop',
            ],

            // Acessórios
            [
                'name'        => 'Bolsa Minimalista',
                'description' => 'Bolsa estruturada em couro sintético premium. Compartimento principal com zíper, bolso interno e alça ajustável. Dimensões: 30x25x12 cm.',
                'price'       => 249.90,
                'category'    => 'Acessórios',
                'image_url'   => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600&h=600&fit=crop',
            ],
            [
                'name'        => 'Relógio Clássico',
                'description' => 'Relógio com caixa em aço inoxidável de 40 mm, pulseira de couro legítimo e movimento quartz japonês. Resistente à água até 30 m.',
                'price'       => 349.90,
                'category'    => 'Acessórios',
                'image_url'   => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=600&h=600&fit=crop',
            ],
            [
                'name'        => 'Óculos de Sol Retrô',
                'description' => 'Armação acetato em formato redondo com lentes polarizadas UV400. Design retrô que combina com diferentes formatos de rosto. Acompanha estojo rígido.',
                'price'       => 179.90,
                'category'    => 'Acessórios',
                'image_url'   => 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=600&h=600&fit=crop',
            ],
            [
                'name'        => 'Cinto de Couro',
                'description' => 'Cinto em couro legítimo curtido ao vegetal, com fivela dourada. Largura de 3 cm, disponível em múltiplos tamanhos. Versátil para looks casuais e formais.',
                'price'       => 119.90,
                'category'    => 'Acessórios',
                'image_url'   => 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=600&h=600&fit=crop',
            ],

            // Calçados
            [
                'name'        => 'Tênis Urbano',
                'description' => 'Tênis em couro sintético com solado emborrachado antiderrapante. Design clean e versátil para o dia a dia. Palmilha removível e numeração do 34 ao 44.',
                'price'       => 299.90,
                'category'    => 'Calçados',
                'image_url'   => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=600&h=600&fit=crop',
            ],
            [
                'name'        => 'Sandália de Couro',
                'description' => 'Sandália rasteira em couro natural curtido ao vegetal. Solado em borracha reciclada, palmilha anatômica e fechamento com fivela ajustável.',
                'price'       => 179.90,
                'category'    => 'Calçados',
                'image_url'   => 'https://images.unsplash.com/photo-1603487742131-4160ec999306?w=600&h=600&fit=crop',
            ],
            [
                'name'        => 'Bota Cano Médio',
                'description' => 'Bota feminina em couro ecológico com cano de 15 cm. Salto bloco de 5 cm para mais estabilidade. Forro interno em tecido macio. Fechamento lateral em zíper.',
                'price'       => 389.90,
                'category'    => 'Calçados',
                'image_url'   => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&h=600&fit=crop',
            ],

            // Casa
            [
                'name'        => 'Vela Aromática',
                'description' => 'Vela artesanal em cera de soja 100% natural com fragrância de baunilha e notas de âmbar. Tempo de queima de aproximadamente 45 horas. Pote reutilizável em cerâmica.',
                'price'       => 79.90,
                'category'    => 'Casa',
                'image_url'   => 'https://images.unsplash.com/photo-1602607753498-39524fe5e903?w=600&h=600&fit=crop',
            ],
            [
                'name'        => 'Almofada Decorativa',
                'description' => 'Almofada decorativa em linho 100% natural com textura artesanal. Enchimento em fibra siliconada antialérgica. Dimensões: 45x45 cm. Capa removível com zíper invisível.',
                'price'       => 119.90,
                'category'    => 'Casa',
                'image_url'   => 'https://images.unsplash.com/photo-1629949009765-40fc74c9ec21?w=600&h=600&fit=crop',
            ],
            [
                'name'        => 'Xícara de Cerâmica',
                'description' => 'Xícara artesanal em cerâmica de alta temperatura, com acabamento fosco e alça ergonômica. Capacidade de 300 ml. Vai ao micro-ondas e à lava-louças.',
                'price'       => 64.90,
                'category'    => 'Casa',
                'image_url'   => 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=600&h=600&fit=crop',
            ],
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
