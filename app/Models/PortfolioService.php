<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class PortfolioService extends Model
{
    protected $fillable = [
        'name_pt', 'name_en',
        'description_pt', 'description_en',
        'cover_photo',
        'order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function photos(): HasMany
    {
        return $this->hasMany(PortfolioServicePhoto::class, 'service_id')->orderBy('order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
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
