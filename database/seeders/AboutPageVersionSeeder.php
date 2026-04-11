<?php

namespace Database\Seeders;

use App\Models\AboutPageVersion;
use Illuminate\Database\Seeder;

class AboutPageVersionSeeder extends Seeder
{
    public function run(): void
    {
        // Evita duplicação: só insere se ainda não houver nenhuma versão
        if (AboutPageVersion::exists()) {
            $this->command->info('AboutPageVersion: já existem registros, seeder ignorado.');
            return;
        }

        AboutPageVersion::create([
            'is_active'     => true,
            'version_label' => 'Versão inicial',

            // ── Hero ─────────────────────────────────────────────────────────
            'hero_title_pt'       => 'Trataplan: A Estrutura da Inovação Sustentável.',
            'hero_title_en'       => 'Trataplan: The Structure of Sustainable Innovation.',
            'hero_subtitle_pt'    => 'Há mais de uma década transformando madeira em durabilidade, no coração do Oeste Catarinense.',
            'hero_subtitle_en'    => 'For over a decade transforming wood into durability, in the heart of Western Santa Catarina.',
            'hero_photo'          => null, // sem foto local; o frontend usará o fallback de Unsplash
            'hero_badge_value'    => '+15',
            'hero_badge_label_pt' => 'Anos de mercado',
            'hero_badge_label_en' => 'Years in market',

            // ── Nossa História ────────────────────────────────────────────────
            'history_title_pt'   => 'Raízes profundas no Oeste Catarinense',
            'history_title_en'   => 'Deep Roots in Western Santa Catarina',
            'history_content_pt' => "A Trataplan nasceu da visão de oferecer madeira tratada de alta qualidade para o mercado do Oeste Catarinense e de toda a região Sul do Brasil. Nossa missão é entregar durabilidade com responsabilidade ambiental.\n\nUtilizamos exclusivamente madeiras de reflorestamento — Pinus e Eucalipto — tratadas pelo processo de autoclave vácuo-pressão, o mais eficaz para garantir resistência a cupins, fungos e intempéries por décadas.",
            'history_content_en' => "Trataplan was born from the vision of offering high-quality treated wood to the market of Western Santa Catarina and throughout Southern Brazil. Our mission is to deliver durability with environmental responsibility.\n\nWe use exclusively reforestation wood — Pine and Eucalyptus — treated by the vacuum-pressure autoclave process, the most effective for ensuring resistance to termites, fungi and weathering for decades.",
            'history_photo'      => null,
            'history_items'      => [
                ['value' => '15+',  'label_pt' => 'Anos',            'label_en' => 'Years'],
                ['value' => '100%', 'label_pt' => 'Reflorestamento', 'label_en' => 'Reforestation'],
                ['value' => 'SC',   'label_pt' => 'Santa Catarina',  'label_en' => 'Santa Catarina'],
            ],

            // ── Nossa Qualidade ───────────────────────────────────────────────
            'quality_title_pt'    => 'Processo e Qualidade',
            'quality_title_en'    => 'Process and Quality',
            'quality_subtitle_pt' => 'Cada etapa do nosso processo é pensada para entregar a máxima durabilidade e segurança.',
            'quality_subtitle_en' => 'Every step of our process is designed to deliver maximum durability and safety.',
            'quality_items'       => [
                [
                    'icon'     => 'park',
                    'title_pt' => '100% Reflorestamento',
                    'title_en' => '100% Reforestation',
                    'desc_pt'  => 'Trabalhamos exclusivamente com Eucalipto e Pinus oriundos de florestas plantadas e manejadas de forma sustentável.',
                    'desc_en'  => 'We work exclusively with Eucalyptus and Pine from sustainably managed planted forests.',
                ],
                [
                    'icon'     => 'precision_manufacturing',
                    'title_pt' => 'Autoclave Vácuo-Pressão',
                    'title_en' => 'Vacuum-Pressure Autoclave',
                    'desc_pt'  => 'O processo de impregnação sob alta pressão garante penetração total do preservativo em cada fibra da madeira.',
                    'desc_en'  => 'The high-pressure impregnation process ensures total preservative penetration into every wood fiber.',
                ],
                [
                    'icon'     => 'verified_user',
                    'title_pt' => 'Garantia de 15 Anos',
                    'title_en' => '15-Year Guarantee',
                    'desc_pt'  => 'Confiança respaldada por certificação e pelo compromisso com a qualidade que só a experiência pode oferecer.',
                    'desc_en'  => 'Confidence backed by certification and the commitment to quality that only experience can offer.',
                ],
            ],

            // ── Seção Final (CTA) ─────────────────────────────────────────────
            'cta_section_name' => 'Seção Reflorestamento',
            'cta_title_pt'     => 'Madeira 100% de Reflorestamento.',
            'cta_title_en'     => 'Wood 100% from Reforestation.',
            'cta_subtitle_pt'  => 'Nosso compromisso com o planeta é tão sólido quanto a madeira que produzimos. Cada peça vendida representa uma árvore de floresta plantada.',
            'cta_subtitle_en'  => 'Our commitment to the planet is as solid as the wood we produce. Each piece sold represents a tree from a planted forest.',
        ]);

        $this->command->info('AboutPageVersion: versão inicial criada e ativada.');
    }
}
