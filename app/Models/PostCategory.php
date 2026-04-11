<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    protected $fillable = ['name_pt', 'name_en', 'description_pt', 'description_en', 'order'];

    public function getName(string $locale = 'pt'): string
    {
        return $this->{"name_{$locale}"} ?? $this->name_pt;
    }

    public function getDescription(string $locale = 'pt'): ?string
    {
        return $this->{"description_{$locale}"} ?? $this->description_pt;
    }
}
