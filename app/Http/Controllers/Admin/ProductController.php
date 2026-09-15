<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'brand']);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($catId = $request->input('category_id')) {
            $query->where('category_id', $catId);
        }

        if ($brandId = $request->input('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        if ($request->boolean('offers')) {
            $query->where('is_offer', true);
        }

        if ($request->input('stock_status') === 'low') {
            $query->where('stock', '<=', 3);
        } elseif ($request->input('stock_status') === 'out') {
            $query->where('stock', '<=', 0);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create(): View
    {
        $categories = Category::with('subcategories')->where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'model' => ['nullable', 'string', 'max:100'],
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'offer_price' => ['nullable', 'numeric', 'min:0'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'main_image' => ['nullable', 'string'],
            'main_image_file' => ['nullable', 'image', 'max:5120'],
            'gallery_images' => ['nullable', 'array', 'max:8'],
            'gallery_images.*' => ['image', 'max:5120'],
            'is_featured' => ['nullable', 'boolean'],
            'is_offer' => ['nullable', 'boolean'],
            'is_new' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'specs_keys' => ['nullable', 'array'],
            'specs_values' => ['nullable', 'array'],
        ]);

        // Build technical specs JSON
        $technicalSpecs = [];
        if (! empty($validated['specs_keys']) && ! empty($validated['specs_values'])) {
            foreach ($validated['specs_keys'] as $index => $key) {
                $val = $validated['specs_values'][$index] ?? null;
                if (! empty($key) && ! empty($val)) {
                    $technicalSpecs[trim($key)] = trim($val);
                }
            }
        }

        // Handle image upload or external URL
        $mainImagePath = $validated['main_image'] ?? 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80';
        if ($request->hasFile('main_image_file')) {
            $path = $request->file('main_image_file')->store('products', 'public');
            $mainImagePath = '/storage/'.$path;
        }

        $slug = Str::slug($validated['name']);
        if (Product::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::lower(Str::random(4));
        }

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'] ?? null,
            'brand_id' => $validated['brand_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'sku' => strtoupper(trim($validated['sku'])),
            'model' => $validated['model'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'technical_specs' => $technicalSpecs,
            'price' => $validated['price'],
            'original_price' => $validated['original_price'] ?? null,
            'offer_price' => $validated['offer_price'] ?? null,
            'min_price' => $validated['min_price'] ?? null,
            'stock' => $validated['stock'],
            'min_stock' => $validated['min_stock'] ?? 2,
            'main_image' => $mainImagePath,
            'is_featured' => $request->boolean('is_featured'),
            'is_offer' => $request->boolean('is_offer'),
            'is_new' => $request->boolean('is_new', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Add main image to gallery
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $mainImagePath,
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        foreach ($request->file('gallery_images', []) as $index => $image) {
            $product->images()->create([
                'image_path' => '/storage/'.$image->store('products', 'public'),
                'is_primary' => false,
                'sort_order' => $index + 1,
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Producto {$product->name} creado exitosamente.");
    }

    public function edit(Product $product): View
    {
        $product->load(['category', 'subcategory', 'brand', 'images']);
        $categories = Category::with('subcategories')->where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,'.$product->id],
            'model' => ['nullable', 'string', 'max:100'],
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'offer_price' => ['nullable', 'numeric', 'min:0'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'main_image' => ['nullable', 'string'],
            'main_image_file' => ['nullable', 'image', 'max:5120'],
            'gallery_images' => ['nullable', 'array', 'max:8'],
            'gallery_images.*' => ['image', 'max:5120'],
            'specs_keys' => ['nullable', 'array'],
            'specs_values' => ['nullable', 'array'],
        ]);

        $technicalSpecs = [];
        if (! empty($validated['specs_keys']) && ! empty($validated['specs_values'])) {
            foreach ($validated['specs_keys'] as $index => $key) {
                $val = $validated['specs_values'][$index] ?? null;
                if (! empty($key) && ! empty($val)) {
                    $technicalSpecs[trim($key)] = trim($val);
                }
            }
        }

        $mainImagePath = $validated['main_image'] ?: $product->main_image;
        if ($request->hasFile('main_image_file')) {
            $path = $request->file('main_image_file')->store('products', 'public');
            $mainImagePath = '/storage/'.$path;
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'] ?? null,
            'brand_id' => $validated['brand_id'],
            'name' => $validated['name'],
            'sku' => strtoupper(trim($validated['sku'])),
            'model' => $validated['model'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'technical_specs' => $technicalSpecs,
            'price' => $validated['price'],
            'original_price' => $validated['original_price'] ?? null,
            'offer_price' => $validated['offer_price'] ?? null,
            'min_price' => $validated['min_price'] ?? null,
            'stock' => $validated['stock'],
            'min_stock' => $validated['min_stock'] ?? 2,
            'main_image' => $mainImagePath,
            'is_featured' => $request->boolean('is_featured'),
            'is_offer' => $request->boolean('is_offer'),
            'is_new' => $request->boolean('is_new'),
            'is_active' => $request->boolean('is_active'),
        ]);

        foreach ($request->file('gallery_images', []) as $index => $image) {
            $product->images()->create([
                'image_path' => '/storage/'.$image->store('products', 'public'),
                'is_primary' => false,
                'sort_order' => $product->images()->max('sort_order') + $index + 1,
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Producto {$product->name} actualizado exitosamente.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Producto {$name} eliminado.");
    }

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Read header line

        $imported = 0;
        $category = Category::first();
        $brand = Brand::first();

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) {
                $name = trim($row[0]);
                $sku = trim($row[1]);
                $price = (float) trim($row[2]);
                $stock = isset($row[3]) ? (int) trim($row[3]) : 5;

                if (! empty($name) && ! empty($sku) && ! Product::where('sku', $sku)->exists()) {
                    Product::create([
                        'category_id' => $category->id,
                        'brand_id' => $brand->id,
                        'name' => $name,
                        'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
                        'sku' => $sku,
                        'price' => $price,
                        'stock' => $stock,
                        'main_image' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80',
                    ]);
                    $imported++;
                }
            }
        }
        fclose($handle);

        return back()->with('success', "Se importaron {$imported} productos exitosamente.");
    }

    public function downloadCsvTemplate(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nombre', 'SKU', 'Precio', 'Stock']);
            fputcsv($handle, ['Laptop Ejemplo', 'LAP-EJE-001', '2499.00', '10']);
            fclose($handle);
        }, 'plantilla-productos-nexora.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function massDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $count = Product::whereIn('id', $request->product_ids)->delete();

        return back()->with('success', "Se han eliminado {$count} productos correctamente.");
    }
}
