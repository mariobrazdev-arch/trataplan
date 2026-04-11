<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Seeder;

class PostCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name_pt' => 'Últimos Serviços', 'name_en' => 'Latest Services',   'order' => 1],
            ['name_pt' => 'Novidades',         'name_en' => 'News',              'order' => 2],
            ['name_pt' => 'Notícias',          'name_en' => 'Updates',           'order' => 3],
            ['name_pt' => 'Institucional',     'name_en' => 'Institutional',     'order' => 4],
        ];

        foreach ($categories as $cat) {
            PostCategory::firstOrCreate(
                ['name_pt' => $cat['name_pt']],
                $cat
            );
        }
    }
}
