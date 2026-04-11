<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class AboutPageVersion extends Model
{
    protected $fillable = [
        'is_active', 'version_label',
        'hero_title_pt', 'hero_title_en',
        'hero_subtitle_pt', 'hero_subtitle_en',
        'hero_photo', 'hero_badge_value',
        'hero_badge_label_pt', 'hero_badge_label_en',
        'history_title_pt', 'history_title_en',
        'history_content_pt', 'history_content_en',
        'history_photo', 'history_items',
        'quality_title_pt', 'quality_title_en',
        'quality_subtitle_pt', 'quality_subtitle_en',
        'quality_items',
        'cta_section_name',
        'cta_title_pt', 'cta_title_en',
        'cta_subtitle_pt', 'cta_subtitle_en',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'history_items' => 'array',
        'quality_items' => 'array',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getHeroPhotoUrlAttribute(): ?string
    {
        return $this->hero_photo ? Storage::url($this->hero_photo) : null;
    }

    public function getHistoryPhotoUrlAttribute(): ?string
    {
        return $this->history_photo ? Storage::url($this->history_photo) : null;
    }
}
