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
            // Notebooks
            ['name' => 'Apple MacBook Pro 14" M3', 'description' => 'Notebook profissional com chip M3, tela Liquid Retina XDR de 14 polegadas e até 18h de bateria.', 'price' => 12999.00, 'category' => 'Notebooks', 'image_url' => null],
            ['name' => 'Apple MacBook Air 15" M2', 'description' => 'Notebook ultrafino com chip M2, tela de 15 polegadas e design sem ventilador.', 'price' => 9999.00, 'category' => 'Notebooks', 'image_url' => null],
            ['name' => 'Dell XPS 15', 'description' => 'Notebook premium com tela OLED 4K, processador Intel Core i9 e placa de vídeo dedicada NVIDIA.', 'price' => 11499.00, 'category' => 'Notebooks', 'image_url' => null],
            ['name' => 'Samsung Galaxy Book4 Pro', 'description' => 'Notebook fino com tela AMOLED, Intel Core Ultra e integração com ecossistema Galaxy.', 'price' => 7499.00, 'category' => 'Notebooks', 'image_url' => null],

            // Smartphones
            ['name' => 'Apple iPhone 16 Pro', 'description' => 'Smartphone com chip A18 Pro, câmera de 48MP com zoom óptico 5x e tela Super Retina XDR de 6,3".', 'price' => 9799.00, 'category' => 'Smartphones', 'image_url' => null],
            ['name' => 'Samsung Galaxy S24 Ultra', 'description' => 'Smartphone topo de linha com caneta S Pen integrada, câmera de 200MP e tela Dynamic AMOLED 2X.', 'price' => 8499.00, 'category' => 'Smartphones', 'image_url' => null],
            ['name' => 'Google Pixel 9 Pro', 'description' => 'Smartphone com câmera computacional avançada, chip Tensor G4 e sete anos de atualizações garantidas.', 'price' => 6999.00, 'category' => 'Smartphones', 'image_url' => null],

            // Periféricos
            ['name' => 'Apple Magic Mouse', 'description' => 'Mouse sem fio recarregável com superfície Multi-Touch e design ultrafino.', 'price' => 699.00, 'category' => 'Periféricos', 'image_url' => null],
            ['name' => 'Apple Magic Keyboard com Touch ID', 'description' => 'Teclado sem fio compacto com Touch ID integrado e teclas de função personalizáveis.', 'price' => 899.00, 'category' => 'Periféricos', 'image_url' => null],
            ['name' => 'Logitech MX Master 3S', 'description' => 'Mouse ergonômico de alto desempenho com scroll eletromagnético e botão silencioso.', 'price' => 549.00, 'category' => 'Periféricos', 'image_url' => null],
            ['name' => 'Sony DualSense PS5', 'description' => 'Controle oficial do PlayStation 5 com feedback háptico e gatilhos adaptáveis.', 'price' => 449.00, 'category' => 'Periféricos', 'image_url' => null],

            // Consoles & Games
            ['name' => 'Sony PlayStation 5 Slim', 'description' => 'Console de nova geração com SSD ultrarrápido, ray tracing e resolução 4K a 120fps.', 'price' => 3799.00, 'category' => 'Consoles & Games', 'image_url' => null],
            ['name' => 'Microsoft Xbox Series X', 'description' => 'Console mais potente da Microsoft com 1TB SSD, 4K nativo e retrocompatibilidade completa.', 'price' => 3599.00, 'category' => 'Consoles & Games', 'image_url' => null],
            ['name' => 'Nintendo Switch OLED', 'description' => 'Console híbrido com tela OLED de 7 polegadas, dock incluído e modo portátil aprimorado.', 'price' => 2199.00, 'category' => 'Consoles & Games', 'image_url' => null],

            // Áudio
            ['name' => 'HyperX QuadCast S', 'description' => 'Microfone USB profissional com iluminação RGB, quatro padrões polares e anti-vibração integrado.', 'price' => 899.00, 'category' => 'Áudio', 'image_url' => null],
            ['name' => 'Apple AirPods Pro 2', 'description' => 'Fone de ouvido com cancelamento ativo de ruído adaptativo, áudio espacial e chip H2.', 'price' => 1899.00, 'category' => 'Áudio', 'image_url' => null],
            ['name' => 'Sony WH-1000XM5', 'description' => 'Headphone over-ear com o melhor cancelamento de ruído do mercado e até 30h de bateria.', 'price' => 1799.00, 'category' => 'Áudio', 'image_url' => null],

            // Monitores
            ['name' => 'Apple Studio Display', 'description' => 'Monitor 5K de 27" com tela Retina, câmera Center Stage e áudio espacial integrado.', 'price' => 14999.00, 'category' => 'Monitores', 'image_url' => null],
            ['name' => 'LG UltraGear 27" 4K', 'description' => 'Monitor gamer 4K com painel Nano IPS, 144Hz, 1ms e compatibilidade com G-Sync e FreeSync.', 'price' => 3299.00, 'category' => 'Monitores', 'image_url' => null],
            ['name' => 'Samsung Odyssey OLED G8', 'description' => 'Monitor curvo OLED de 34" com resolução QD-OLED, 175Hz e design premium sem bordas.', 'price' => 5999.00, 'category' => 'Monitores', 'image_url' => null],
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
