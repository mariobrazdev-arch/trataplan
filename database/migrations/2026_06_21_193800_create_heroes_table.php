<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heroes', function (Blueprint $table) {
            $table->id();
            $table->string('layout');
            $table->string('title_pt');
            $table->string('title_en')->nullable();
            $table->string('subtitle_pt')->nullable();
            $table->string('subtitle_en')->nullable();
            $table->string('cta_label_pt')->nullable();
            $table->string('cta_label_en')->nullable();
            $table->string('main_image')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Migra o banner atual (Setting::banner_*) para o primeiro hero, já ativo,
        // garantindo que a Home não fique sem nenhum hero após esta migration.
        \DB::table('heroes')->insert([
            'layout'       => 'full_image',
            'title_pt'     => Setting::get('banner_title_pt', 'Madeira Tratada que Dura Gerações.'),
            'title_en'     => Setting::get('banner_title_en', 'Treated Wood that Lasts Generations.'),
            'subtitle_pt'  => Setting::get('banner_subtitle_pt', 'Soluções em Eucalipto e Pinus Autoclavado para Construção, Cercas e Lazer com a robustez que seu projeto exige.'),
            'subtitle_en'  => Setting::get('banner_subtitle_en', 'Eucalyptus and Autoclaved Pine solutions for Construction, Fencing and Leisure with the robustness your project demands.'),
            'cta_label_pt' => 'Falar no WhatsApp',
            'cta_label_en' => 'Chat on WhatsApp',
            'main_image'   => Setting::get('banner_image'),
            'order'        => 1,
            'is_active'    => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('heroes');
    }
};
