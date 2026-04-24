<?php

namespace App\Http\Controllers;

use App\Models\AboutPageVersion;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function show(): Response
    {
        $version = AboutPageVersion::active()->latest()->first();

        return Inertia::render('About', [
            'about' => $version ? [
                'hero_title_pt'       => $version->hero_title_pt,
                'hero_title_en'       => $version->hero_title_en,
                'hero_subtitle_pt'    => $version->hero_subtitle_pt,
                'hero_subtitle_en'    => $version->hero_subtitle_en,
                'hero_photo_url'      => $version->hero_photo_url,
                'hero_badge_value'    => $version->hero_badge_value,
                'hero_badge_label_pt' => $version->hero_badge_label_pt,
                'hero_badge_label_en' => $version->hero_badge_label_en,
                'history_title_pt'    => $version->history_title_pt,
                'history_title_en'    => $version->history_title_en,
                'history_content_pt'  => $version->history_content_pt,
                'history_content_en'  => $version->history_content_en,
                'history_photo_url'   => $version->history_photo_url,
                'history_youtube_url' => $version->history_youtube_url,
                'history_items'       => $version->history_items ?? [],
                'quality_title_pt'    => $version->quality_title_pt,
                'quality_title_en'    => $version->quality_title_en,
                'quality_subtitle_pt' => $version->quality_subtitle_pt,
                'quality_subtitle_en' => $version->quality_subtitle_en,
                'quality_items'       => $version->quality_items ?? [],
                'cta_title_pt'        => $version->cta_title_pt,
                'cta_title_en'        => $version->cta_title_en,
                'cta_subtitle_pt'     => $version->cta_subtitle_pt,
                'cta_subtitle_en'     => $version->cta_subtitle_en,
                'cta_photo_url'       => $version->cta_photo_url,
            ] : null,
            'seo' => [
                'title'       => 'Sobre – Trataplan',
                'description' => 'Conheça a Trataplan, empresa especializada em madeira tratada de alta qualidade no Oeste de Santa Catarina.',
            ],
        ]);
    }
}
