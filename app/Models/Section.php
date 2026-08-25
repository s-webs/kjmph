<?php

namespace App\Models;

use App\Models\Concerns\HasLocaleColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasLocaleColumns;

    protected $fillable = [
        'name_en', 'name_ru', 'name_kk',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'section_id');
    }
}
