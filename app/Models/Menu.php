<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name_ru',
        'name_kz',
        'name_en',
        'link_ru',
        'link_kz',
        'link_en',
        'sort_order',
        'parent_id',
        'icon',
    ];

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // Загрузка всех уровней
    public function childrenRecursive(): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function scopeRoots($q)
    {
        return $q->whereNull('parent_id')->orderBy('sort_order');
    }

    // Удобные аксессоры для текущей локали
    public function getTitleAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"name_{$locale}"} ?? $this->name_ru ?? $this->name_en;
    }

    public function getUrlAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"link_{$locale}"} ?? $this->link_ru ?? $this->link_en ?? '#';
    }
}
