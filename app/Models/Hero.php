<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Hero extends Model
{
    public const LAYOUT_FULL_IMAGE = 'full_image';
    public const LAYOUT_SPLIT_SCREEN = 'split_screen';
    public const LAYOUT_PHOTO_GRID = 'photo_grid';

    public const CTA_LINKS = [
        'whatsapp' => ['pt' => null, 'en' => null],
        'home' => ['pt' => '/', 'en' => '/en'],
        'about' => ['pt' => '/sobre', 'en' => '/en/about'],
        'portfolio' => ['pt' => '/portfolio', 'en' => '/en/portfolio'],
        'blog' => ['pt' => '/blog', 'en' => '/en/blog'],
        'partners' => ['pt' => '/parceiros', 'en' => '/en/partners'],
        'contact' => ['pt' => '/contato', 'en' => '/en/contact'],
    ];

    protected $fillable = [
        'layout',
        'title_pt', 'title_en',
        'subtitle_pt', 'subtitle_en',
        'cta_label_pt', 'cta_label_en',
        'cta_link',
        'main_image',
        'order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function photos(): HasMany
    {
        return $this->hasMany(HeroPhoto::class)->orderBy('order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function getTitle(string $locale = 'pt'): string
    {
        return $this->{"title_{$locale}"} ?? $this->title_pt;
    }

    public function getSubtitle(string $locale = 'pt'): ?string
    {
        return $this->{"subtitle_{$locale}"} ?? $this->subtitle_pt;
    }

    public function getCtaLabel(string $locale = 'pt'): ?string
    {
        return $this->{"cta_label_{$locale}"} ?? $this->cta_label_pt;
    }

    public function getCtaUrl(string $locale = 'pt'): ?string
    {
        return self::CTA_LINKS[$this->cta_link ?? 'whatsapp'][$locale]
            ?? self::CTA_LINKS['whatsapp'][$locale];
    }

    public function getMainImageUrlAttribute(): ?string
    {
        if (!$this->main_image) {
            return null;
        }

        if (str_starts_with($this->main_image, 'http://') || str_starts_with($this->main_image, 'https://')) {
            return $this->main_image;
        }

        return Storage::url($this->main_image);
    }
}
