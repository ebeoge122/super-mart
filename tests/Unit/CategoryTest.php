<?php

namespace Tests\Unit;

use App\Models\Category;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    public function test_hero_image_url_falls_back_to_default_image_when_no_category_image_exists(): void
    {
        $category = new Category(['heroimg' => null]);

        $this->assertSame(asset('images/categories/default.png'), $category->hero_image_url);
    }
}
