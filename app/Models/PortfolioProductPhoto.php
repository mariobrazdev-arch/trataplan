<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PortfolioProductPhoto extends Model
{
    protected $fillable = ['product_id', 'path', 'order'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(PortfolioProduct::class, 'product_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
