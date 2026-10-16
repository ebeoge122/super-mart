<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The `items` migration declares `$table->string('slug')->unique()`, but the
 * unique index was never created in the live database (schema drift). As a
 * result re-importing the same spreadsheet silently inserted exact-duplicate
 * rows instead of rejecting the duplicate slug.
 *
 * This migration creates the missing unique index on `items.slug`. It assumes
 * duplicate slugs have already been cleaned up; if any remain, the index
 * creation will fail by design so the duplicates surface instead of being
 * hidden again.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Skip if the index already exists (idempotent).
        $hasUnique = collect(DB::select("PRAGMA index_list(items)"))
            ->contains(fn ($i) => $i->name === 'items_slug_unique');

        if (! $hasUnique) {
            Schema::table('items', function (Blueprint $table) {
                $table->unique('slug');
            });
        }
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropUnique('items_slug_unique');
        });
    }
};
