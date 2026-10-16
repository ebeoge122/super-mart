<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fixes the `categories` table, which was created without a proper primary key
 * or auto-increment on `id` (it was a NOT NULL bigint with no default). As a
 * result every `Category::create()` failed with
 * "NOT NULL constraint failed: categories.id", which broke the admin product
 * import (any new category — e.g. "uncategorized" — aborted the whole import).
 *
 * This migration recreates `categories` with a proper `id` PK while preserving
 * existing rows and their ids, so `items.category_id` references stay valid.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Snapshot existing categories so we can restore them with the same ids.
        $existing = DB::table('categories')->get()->map(fn ($c) => (array) $c)->all();

        Schema::disableForeignKeyConstraints();
        DB::statement('DROP TABLE IF EXISTS categories');

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('heroimg')->nullable();
            $table->timestamps();
        });

        foreach ($existing as $row) {
            DB::table('categories')->insert($row);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // No meaningful rollback: the previous schema was broken by design.
    }
};
