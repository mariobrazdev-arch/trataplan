<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PortfolioServicePhoto extends Model
{
    protected $fillable = ['service_id', 'path', 'order'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(PortfolioService::class, 'service_id');
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
            return $this->path;
        }

        return Storage::url($this->path);
    }
}
