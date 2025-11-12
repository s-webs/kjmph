<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'logo', 'name', 'options'
    ];

    protected $casts = [
        'options' => 'array'
    ];
}
