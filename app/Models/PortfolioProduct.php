<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortfolioProduct extends Model
{
    protected $fillable = [
        'category_id', 'name_pt', 'name_en',
        'description_pt', 'description_en',
        'order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'category_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PortfolioProductPhoto::class, 'product_id')->orderBy('order');
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
}
