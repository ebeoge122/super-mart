<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'heroimg'];

    /**
     * Get the items for the category.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Full URL to this category's hero/tile image.
     * Falls back to a generic image if no heroimg has been set yet,
     * so templates never render a broken <img> tag.
     */
    public function getHeroImageUrlAttribute(): string
    {
        if (!empty($this->heroimg) && file_exists(public_path('images/categories/' . $this->heroimg))) {
            return asset('images/categories/' . $this->heroimg);
        }

        return asset('images/categories/default.png');
    }

    /**
     * Whether this category has its own dedicated image (vs the fallback).
     */
    public function getHasHeroImageAttribute(): bool
    {
        return !empty($this->heroimg) && file_exists(public_path('images/categories/' . $this->heroimg));
    }
}