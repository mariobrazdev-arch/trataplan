<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model
{
    protected $fillable = [
        'slug', 'title_pt', 'title_en',
        'content_pt', 'content_en',
        'meta_title_pt', 'meta_title_en',
        'meta_description_pt', 'meta_description_en',
        'og_image', 'is_published', 'order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getTitle(string $locale = 'pt'): string
    {
        return $this->{"title_{$locale}"} ?? $this->title_pt;
    }

    public function getContent(string $locale = 'pt'): ?string
    {
        return $this->{"content_{$locale}"} ?? $this->content_pt;
    }

    public function getMetaTitle(string $locale = 'pt'): ?string
    {
        return $this->{"meta_title_{$locale}"} ?? $this->{"meta_title_pt"};
    }

    public function getMetaDescription(string $locale = 'pt'): ?string
    {
        return $this->{"meta_description_{$locale}"} ?? $this->{"meta_description_pt"};
    }
}
