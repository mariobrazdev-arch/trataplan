<?php

namespace Database\Seeders;

use App\Models\PortfolioCategory;
use App\Models\PortfolioCategoryPhoto;
use Illuminate\Database\Seeder;

class PortfolioCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_pt' => 'Deck e Pergolados',
                'name_en' => 'Deck and Pergolas',
                'description_pt' => 'Projetos para áreas de lazer externas com acabamento premium e alta durabilidade.',
                'description_en' => 'Projects for outdoor leisure areas with premium finishing and high durability.',
                'cover_photo' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=1200&q=80',
            ],
            [
                'name_pt' => 'Construção Civil',
                'name_en' => 'Civil Construction',
                'description_pt' => 'Aplicações estruturais em madeira tratada para obras residenciais e comerciais.',
                'description_en' => 'Structural applications in treated wood for residential and commercial projects.',
                'cover_photo' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=1200&q=80',
            ],
            [
                'name_pt' => 'Cercas e Currais',
                'name_en' => 'Fences and Corrals',
                'description_pt' => 'Soluções robustas para o setor agropecuário com longa vida útil.',
                'description_en' => 'Robust solutions for the agricultural sector with long service life.',
                'cover_photo' => 'https://images.unsplash.com/photo-1597200381847-30ec200eeb9a?w=1200&q=80',
            ],
            [
                'name_pt' => 'Jardinagem e Paisagismo',
                'name_en' => 'Gardening and Landscaping',
                'description_pt' => 'Integração estética entre madeira e natureza para ambientes externos.',
                'description_en' => 'Aesthetic integration between wood and nature for outdoor environments.',
                'cover_photo' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=1200&q=80',
            ],
        ];

        foreach ($categories as $data) {
            $category = PortfolioCategory::updateOrCreate(
                ['name_pt' => $data['name_pt']],
                [
                    ...$data,
                    'is_active' => true,
                ]
            );

            if (!$category->photos()->exists()) {
                PortfolioCategoryPhoto::create([
                    'category_id' => $category->id,
                    'path' => $data['cover_photo'],
                    'order' => 0,
                ]);
            }
        }
    }
}
