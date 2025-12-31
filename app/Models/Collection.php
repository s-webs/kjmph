<?php

namespace App\Models;

use App\Models\Concerns\HasLocaleColumns;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasLocaleColumns;
    protected $fillable = [
        'title_en', 'title_ru', 'title_kk',
        'description_en', 'description_ru', 'description_kk',
        'slug_en', 'slug_ru', 'slug_kk',
        'cover', 'file', 'url_id', 'doi', 'published_at',
        'volume', 'number', 'year', 'created_at', 'updated_at'
    ];
}



