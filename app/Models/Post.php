<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'slug', 'title_pt', 'title_en',
        'content_pt', 'content_en',
        'excerpt_pt', 'excerpt_en',
        'featured_image',
        'meta_title_pt', 'meta_title_en',
        'meta_description_pt', 'meta_description_en',
        'og_image', 'user_id', 'post_category_id', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function postCategory(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PostPhoto::class)->orderBy('order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getIsPublishedAttribute(): bool
    {
        return !is_null($this->published_at) && $this->published_at->lte(now());
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (!$this->featured_image) return null;
        return Storage::url($this->featured_image);
    }

    public function getTitle(string $locale = 'pt'): string
    {
        return $this->{"title_{$locale}"} ?? $this->title_pt;
    }

    public function getContent(string $locale = 'pt'): ?string
    {
        return $this->{"content_{$locale}"} ?? $this->content_pt;
    }

    public function getExcerpt(string $locale = 'pt'): ?string
    {
        $excerpt = $this->{"excerpt_{$locale}"} ?? $this->{"excerpt_pt"};
        if ($excerpt) return $excerpt;

        $content = $this->getContent($locale);
        return $content ? Str::limit(strip_tags($content), 160) : null;
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
