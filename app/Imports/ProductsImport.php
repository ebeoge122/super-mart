<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (empty($row['name']) || empty($row['slug'])) {
            return null;
        }

        $categoryName = $row['category'] ?? 'uncategorized';
        $categorySlug = Str::slug($categoryName);

        $category = Category::firstOrCreate(
            ['slug' => $categorySlug],
            ['name' => $categoryName]
        );

        return new Item([
            'category_id' => $category->id,
            'name' => $row['name'] ?? null,
            'slug' => $row['slug'] ?? null,
            'description' => $row['description'] ?? null,
            'image' => $row['image'] ?? null,
            'price' => $row['price'] ?? null,
            'sale_price' => $row['sale_price'] ?? null,
            'currency' => $row['currency'] ?? null,
            'is_clearance' => !empty($row['is_clearance']) ? (int) $row['is_clearance'] : 0,
            'is_featured' => !empty($row['is_featured']) ? (int) $row['is_featured'] : 0,
        ]);
    }
}
