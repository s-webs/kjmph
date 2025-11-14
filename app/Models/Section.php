<?php

namespace App\Models;

use App\Models\Concerns\HasLocaleColumns;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasLocaleColumns;

    protected $fillable = [
        'name_en', 'name_ru', 'name_kk',
    ];
}
