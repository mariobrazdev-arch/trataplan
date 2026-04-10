<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Partner extends Model
{
    protected $fillable = [
        'name', 'logo', 'website',
        'description_pt', 'description_en',
        'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) return null;
        return Storage::url($this->logo);
    }

    public function getDescription(string $locale = 'pt'): ?string
    {
        return $this->{"description_{$locale}"} ?? $this->description_pt;
    }
}
