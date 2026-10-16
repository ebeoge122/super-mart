<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class ImportProductController extends Controller
{
    public function admin()
    {
        return view('admin');
    }

    public function store(Request $request)
    {
        // Log whether files are present to help debug upload problems
        Log::info('Import upload request received', [
            'has_excel' => $request->hasFile('excel'),
            'has_images' => $request->hasFile('images'),
            '_FILES' => isset($_FILES) ? $_FILES : null,
        ]);

        $request->validate([
            'excel' => 'nullable|file|mimes:xlsx,xls,csv',
            // Accept either a single file or multiple files named images[]
            'images' => 'nullable',
            'images.*' => 'file|image|max:5120',
        ]);

        try {
            if ($request->hasFile('excel')) {
                // import directly from uploaded file
                Excel::import(new ProductsImport(), $request->file('excel'));
            }

            if ($request->hasFile('images')) {
                $imagesPath = $this->uploadImages($request);
            }

            return redirect()->route('admin')->with('success', 'Products uploaded successfully.');
        } catch (\Exception $e) {
            Log::error('Admin import error: ' . $e->getMessage());
            return redirect()->route('admin')->withErrors('Failed to process upload.');
        }
    }

    public function uploadImages(Request $request)
    {
        if (! $request->hasFile('images')) {
            return null;
        }

        $files = $request->file('images');
        // Normalize to array
        if (! is_array($files)) {
            $files = [$files];
        }

        $destination = public_path('images/items');
        if (! file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $paths = [];
        foreach ($files as $file) {
            if (! $file) continue;
            $originalName = $file->getClientOriginalName();
            $originalName = basename($originalName);
            try {
                $file->move($destination, $originalName);
                $paths[] = 'images/items/' . $originalName;
            } catch (\Exception $e) {
                Log::error('Failed to move uploaded image', ['file' => $originalName, 'error' => $e->getMessage()]);
            }
        }

        return $paths;
    }

    public function uploadExcel(Request $request){
        $request->validate([
            'excel' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('excel');

        try {
            // Save any uploaded product images alongside the spreadsheet import.
            // (This method is the live admin.upload handler, so it owns both jobs.)
            if ($request->hasFile('images')) {
                $this->uploadImages($request);
            }

            // Read the file into arrays (sheets -> rows -> columns)
            $sheets = Excel::toArray([], $file);

            if (empty($sheets) || !isset($sheets[0])) {
                return redirect()->back()->withErrors('Excel file is empty or unreadable.');
            }

            $rows = $sheets[0];

            // Detect heading row
            $hasHeading = false;
            if (!empty($rows)) {
                $first = $rows[0];
                $firstLower = array_map(function ($v) {
                    return is_string($v) ? strtolower(trim($v)) : $v;
                }, $first);

                $hasHeading = in_array('name', $firstLower, true) || in_array('category', $firstLower, true) || in_array('slug', $firstLower, true);
            }

            $startIndex = $hasHeading ? 1 : 0;
            $inserted = 0;

            DB::beginTransaction();

            for ($i = $startIndex; $i < count($rows); $i++) {
                $r = $rows[$i];

                // Map row to associative array depending on whether headings exist
                if ($hasHeading) {
                    $assoc = [];
                    foreach ($rows[0] as $idx => $heading) {
                        $key = is_string($heading) ? strtolower(trim($heading)) : $heading;
                        $assoc[$key] = $r[$idx] ?? null;
                    }
                } else {
                    // expected column order: id, category_id, name, slug, description, image, price, sale_price, currency, is_clearance, is_featured
                    $assoc = [
                        'id' => $r[0] ?? null,
                        'category_id' => $r[1] ?? null,
                        'name' => $r[2] ?? null,
                        'slug' => $r[3] ?? null,
                        'description' => $r[4] ?? null,
                        'image' => $r[5] ?? null,
                        'price' => $r[6] ?? null,
                        'sale_price' => $r[7] ?? null,
                        'currency' => $r[8] ?? null,
                        'is_clearance' => $r[9] ?? null,
                        'is_featured' => $r[10] ?? null,
                    ];
                }

                // Skip empty rows or rows without a name
                if (empty($assoc['name'])) {
                    continue;
                }

                // Resolve category
                $categoryId = null;
                if (!empty($assoc['category_id']) && is_numeric($assoc['category_id'])) {
                    $cat = Category::find((int) $assoc['category_id']);
                    if ($cat) $categoryId = $cat->id;
                }

                if (empty($categoryId)) {
                    // Some sheets put the category name (not a numeric id) in the
                    // category_id column, e.g. "Meats". Fall back to it as a name.
                    $catName = $assoc['category']
                        ?? $assoc['category_name']
                        ?? (is_string($assoc['category_id'] ?? null) ? $assoc['category_id'] : null)
                        ?? 'uncategorized';
                    $catSlug = Str::slug($catName ?: 'uncategorized');
                    $cat = Category::firstOrCreate(['slug' => $catSlug], ['name' => $catName]);
                    $categoryId = $cat->id;
                }

                $slug = $assoc['slug'] ?? Str::slug($assoc['name']);

                $parseNumeric = function ($value) {
                    if ($value === null || $value === '') return null;
                    $v = (string) $value;
                    $v = str_replace([',', ' ', "\xc2\xa0", '$', '£', '€', '₦'], '', $v);
                    $v = preg_replace('/[^0-9.\-]/', '', $v);
                    return is_numeric($v) ? (float) $v : null;
                };

                $toBoolInt = function ($val) {
                    if ($val === null || $val === '') return 0;
                    $v = strtolower((string) $val);
                    return in_array($v, ['1', 'true', 'yes', 'y'], true) ? 1 : 0;
                };

                // firstOrCreate so re-importing the same sheet skips rows whose
                // slug already exists instead of aborting (unique constraint) or
                // silently duplicating them.
                $item = Item::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'category_id' => $categoryId,
                        'name' => $assoc['name'],
                        'description' => $assoc['description'] ?? null,
                        'image' => $assoc['image'] ?? null,
                        'price' => $parseNumeric($assoc['price'] ?? null),
                        'sale_price' => $parseNumeric($assoc['sale_price'] ?? null),
                        'currency' => $assoc['currency'] ?? null,
                        'is_clearance' => $toBoolInt($assoc['is_clearance'] ?? null),
                        'is_featured' => $toBoolInt($assoc['is_featured'] ?? null),
                ]);

                if ($item) $inserted++;
            }

            DB::commit();

            return redirect()->back()->with('success', "Imported {$inserted} items.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Excel import failed: ' . $e->getMessage());
            return redirect()->back()->withErrors('Import failed: ' . $e->getMessage());
        }
    }
}
