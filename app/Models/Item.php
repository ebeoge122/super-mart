<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'price',
        'sale_price',
        'currency',
        'is_clearance',
        'is_featured',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}

