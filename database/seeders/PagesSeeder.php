<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'title_pt' => 'Sobre a Trataplan',
                'title_en' => 'About Trataplan',
                'content_pt' => '<h2>Quem Somos</h2><p>A Trataplan é uma empresa especializada no tratamento de madeiras, localizada em Planalto Alegre, Santa Catarina. Fundada com o compromisso de oferecer produtos de alta qualidade e durabilidade, nos tornamos referência no Oeste Catarinense.</p><h2>Nossa Missão</h2><p>Fornecer madeira tratada de excelência, aliando tecnologia de ponta ao compromisso com a sustentabilidade e o reflorestamento.</p><h2>Nossos Diferenciais</h2><ul><li>Tratamento em autoclave com garantia de 15 anos</li><li>Madeira 100% de áreas de reflorestamento</li><li>Diversidade de produtos para todos os segmentos</li><li>Atendimento personalizado</li></ul>',
                'content_en' => '<h2>Who We Are</h2><p>Trataplan is a company specialized in wood treatment, located in Planalto Alegre, Santa Catarina. Founded with the commitment to offer high quality and durable products, we have become a reference in Western Santa Catarina.</p><h2>Our Mission</h2><p>To provide excellent treated wood, combining cutting-edge technology with a commitment to sustainability and reforestation.</p><h2>Our Differentials</h2><ul><li>Autoclave treatment with 15-year guarantee</li><li>100% wood from reforestation areas</li><li>Diverse products for all segments</li><li>Personalized service</li></ul>',
                'meta_title_pt' => 'Sobre a Trataplan – Madeira Tratada SC',
                'meta_title_en' => 'About Trataplan – Treated Wood SC',
                'meta_description_pt' => 'Conheça a Trataplan, referência em madeira tratada no Oeste Catarinense. 15 anos de garantia, eucalipto e pinus de reflorestamento.',
                'meta_description_en' => 'Meet Trataplan, reference in treated wood in Western Santa Catarina. 15-year guarantee, reforestation eucalyptus and pine.',
                'is_published' => true,
                'order' => 1,
            ],
            [
                'slug' => 'products',
                'title_pt' => 'Nossos Produtos',
                'title_en' => 'Our Products',
                'content_pt' => '<h2>Linha Completa de Madeira Tratada</h2><p>A Trataplan oferece uma ampla linha de produtos tratados em autoclave, garantindo durabilidade e resistência em diversas aplicações.</p><h3>Decks e Pergolados</h3><p>Madeiras nobres tratadas para áreas externas. Resistentes à umidade, cupins e fungos. Ideal para piscinas, varandas e jardins.</p><h3>Construção Civil</h3><p>Vigas, barrotes, assoalhos, forros e madeiras estruturais com alta resistência. Certificadas para construção residencial e comercial.</p><h3>Cercas e Currais</h3><p>Palanques, mourões e réguas dimensionadas especificamente para o setor agropecuário. Alta durabilidade no campo.</p><h3>Mangueiras e Currais</h3><p>Estruturas completas para manejo de bovinos e outros animais. Resistência comprovada às condições do campo.</p><h3>Jardinagem e Paisagismo</h3><p>Madeiras e produtos para integrar sua residência à natureza. Soluções orgânicas e sustentáveis.</p><h3>Quiosques e Chalés</h3><p>Kits e materiais para construção de estruturas turísticas e de lazer com madeira tratada de alta qualidade.</p>',
                'content_en' => '<h2>Complete Treated Wood Line</h2><p>Trataplan offers a complete line of autoclave-treated products, ensuring durability and resistance in various applications.</p><h3>Decks and Pergolas</h3><p>Noble treated woods for outdoor areas. Resistant to moisture, termites and fungi. Ideal for pools, verandas and gardens.</p><h3>Civil Construction</h3><p>Beams, joists, floors, ceilings and structural wood with high resistance. Certified for residential and commercial construction.</p><h3>Fences and Pens</h3><p>Stakes, posts and boards specifically sized for the agricultural sector. High durability in the field.</p><h3>Handling Pens</h3><p>Complete structures for handling cattle and other animals. Proven resistance to field conditions.</p><h3>Gardening and Landscaping</h3><p>Wood and products to integrate your home with nature. Organic and sustainable solutions.</p><h3>Kiosks and Chalets</h3><p>Kits and materials for building tourist and leisure structures with high-quality treated wood.</p>',
                'meta_title_pt' => 'Produtos – Madeira Tratada Trataplan',
                'meta_title_en' => 'Products – Trataplan Treated Wood',
                'meta_description_pt' => 'Decks, pergolados, cercas, construção civil e mais. Madeira tratada em autoclave com 15 anos de garantia.',
                'meta_description_en' => 'Decks, pergolas, fences, civil construction and more. Autoclave treated wood with 15-year guarantee.',
                'is_published' => true,
                'order' => 2,
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
