<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_page_versions', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->string('version_label')->nullable();

            // ── Hero ─────────────────────────────────────────────────────
            $table->string('hero_title_pt')->nullable();
            $table->string('hero_title_en')->nullable();
            $table->text('hero_subtitle_pt')->nullable();
            $table->text('hero_subtitle_en')->nullable();
            $table->string('hero_photo')->nullable();
            $table->string('hero_badge_value')->nullable();
            $table->string('hero_badge_label_pt')->nullable();
            $table->string('hero_badge_label_en')->nullable();

            // ── História ─────────────────────────────────────────────────
            $table->string('history_title_pt')->nullable();
            $table->string('history_title_en')->nullable();
            $table->text('history_content_pt')->nullable();
            $table->text('history_content_en')->nullable();
            $table->string('history_photo')->nullable();
            $table->json('history_items')->nullable(); // [{value, label_pt, label_en}]

            // ── Qualidade ────────────────────────────────────────────────
            $table->string('quality_title_pt')->nullable();
            $table->string('quality_title_en')->nullable();
            $table->text('quality_subtitle_pt')->nullable();
            $table->text('quality_subtitle_en')->nullable();
            $table->json('quality_items')->nullable(); // [{icon, title_pt, title_en, desc_pt, desc_en}]

            // ── CTA ──────────────────────────────────────────────────────
            $table->string('cta_section_name')->nullable();
            $table->string('cta_title_pt')->nullable();
            $table->string('cta_title_en')->nullable();
            $table->text('cta_subtitle_pt')->nullable();
            $table->text('cta_subtitle_en')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_page_versions');
    }
};
