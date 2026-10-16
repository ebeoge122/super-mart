<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'spices'         => 'spices.png',
            'snacks'         => 'snacks.png',
            'meats'          => 'meats.png',
            'grainsandpasta' => 'grains_and_pasta.png',
            'fruits'         => 'fruits.png',
            'flouretc'       => 'flour_and_baking_ingredients.png',
            'drinks'         => 'drinks.png',
            'dairy'          => 'dairy.png',
            'cookingoils'    => 'cooking_oils.png',
            'vegetables'     => 'vegetables.png',
            'supplies'       => 'supplies.png',
            'pastries'       => 'pastries.png',
        ];

        foreach ($map as $slug => $filename) {
            DB::table('categories')
                ->where('slug', $slug)
                ->update(['heroimg' => $filename]);
        }
    }

    public function down(): void
    {
        DB::table('categories')
            ->whereIn('slug', [
                'spices', 'snacks', 'meats', 'grainsandpasta', 'fruits',
                'flouretc', 'drinks', 'dairy', 'cookingoils', 'vegetables',
                'supplies', 'pastries',
            ])
            ->update(['heroimg' => null]);
    }
};