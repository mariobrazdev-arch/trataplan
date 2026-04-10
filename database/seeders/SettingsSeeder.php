<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'whatsapp_number' => '554999600329',
            'telegram_link' => 'https://t.me/trataplan',
            'contact_email' => 'trataplan@trataplan.com.br',
            'contact_phone' => '+55 49 9960-0329',
            // Legacy single-field; kept for public site backward compat (synced automatically on save)
            'contact_address' => 'SC-283, Planalto Alegre - SC, 89882-000',
            // Dynamic units list (JSON)
            'company_units' => json_encode([
                ['label' => 'Matriz (Sede)', 'address' => 'SC-283, Planalto Alegre - SC, 89882-000'],
            ]),
            'instagram_url' => '',
            'facebook_url' => '',
            'linkedin_url' => '',
            'company_tagline_pt' => 'Madeira Tratada que Dura Gerações.',
            'company_tagline_en' => 'Treated Wood that Lasts Generations.',
            'footer_text_pt' => 'Referência em tratamento de madeiras no Oeste Catarinense. Qualidade e compromisso com o meio ambiente em cada peça produzida.',
            'footer_text_en' => 'Reference in wood treatment in Western Santa Catarina. Quality and commitment to the environment in every piece produced.',
            'banner_title_pt' => 'Madeira Tratada que Dura Gerações',
            'banner_title_en' => 'Treated Wood that Lasts Generations',
            'banner_subtitle_pt' => 'Eucalipto e Pinus autoclavado com qualidade e compromisso ambiental.',
            'banner_subtitle_en' => 'Autoclaved eucalyptus and pine with quality and environmental commitment.',
            'banner_image' => '',
            'logo_transparent' => '',
            'logo_white_bg' => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
