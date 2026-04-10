<?php

namespace Database\Seeders;

use App\Models\PortfolioService;
use App\Models\PortfolioServicePhoto;
use Illuminate\Database\Seeder;

class PortfolioServiceSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name_pt' => 'Chalés de Luxo em Pinus',
                'name_en' => 'Luxury Pine Chalets',
                'description_pt' => 'Pinus autoclavado com acabamento premium para estruturas residenciais de alto padrão.',
                'description_en' => 'Autoclaved pine with premium finishing for high-end residential structures.',
                'cover_photo' => 'https://images.unsplash.com/photo-1449158743715-0a90ebb6d2d8?w=1200&q=80',
                'order' => 1,
            ],
            [
                'name_pt' => 'Cercas de Design Contemporâneo',
                'name_en' => 'Contemporary Design Fences',
                'description_pt' => 'Execução especializada em cercas e divisórias com estética moderna e alta durabilidade.',
                'description_en' => 'Specialized execution of fences and dividers with modern aesthetics and high durability.',
                'cover_photo' => 'https://images.unsplash.com/photo-1597200381847-30ec200eeb9a?w=1200&q=80',
                'order' => 2,
            ],
            [
                'name_pt' => 'Áreas de Lazer Premium',
                'name_en' => 'Premium Leisure Areas',
                'description_pt' => 'Instalação de decks, pergolados e áreas gourmet com mão de obra especializada.',
                'description_en' => 'Installation of decks, pergolas, and gourmet areas with specialized workforce.',
                'cover_photo' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=1200&q=80',
                'order' => 3,
            ],
        ];

        foreach ($items as $item) {
            $service = PortfolioService::updateOrCreate(
                ['name_pt' => $item['name_pt']],
                [
                    'name_en' => $item['name_en'],
                    'description_pt' => $item['description_pt'],
                    'description_en' => $item['description_en'],
                    'cover_photo' => $item['cover_photo'],
                    'order' => $item['order'],
                    'is_active' => true,
                ]
            );

            if (!$service->photos()->exists()) {
                PortfolioServicePhoto::create([
                    'service_id' => $service->id,
                    'path' => $item['cover_photo'],
                    'order' => 0,
                ]);
            }
        }
    }
}
