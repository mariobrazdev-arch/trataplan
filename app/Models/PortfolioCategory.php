<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class PortfolioCategory extends Model
{
    protected $fillable = [
        'name_pt', 'name_en',
        'description_pt', 'description_en',
        'cover_photo',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function products(): HasMany
    {
        return $this->hasMany(PortfolioProduct::class, 'category_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PortfolioCategoryPhoto::class, 'category_id')->orderBy('order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderByDesc('id');
    }

    public function getName(string $locale = 'pt'): string
    {
        return $this->{"name_{$locale}"} ?? $this->name_pt;
    }

    public function getDescription(string $locale = 'pt'): ?string
    {
        return $this->{"description_{$locale}"} ?? $this->description_pt;
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->cover_photo) {
            return null;
        }

        if (str_starts_with($this->cover_photo, 'http://') || str_starts_with($this->cover_photo, 'https://')) {
            return $this->cover_photo;
        }

        return Storage::url($this->cover_photo);
    }
}
