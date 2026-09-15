<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Combo;
use App\Models\CompanySetting;
use App\Models\Product;
use App\Services\WhatsAppService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['brand', 'category', 'subcategory'])
            ->where('is_active', true);

        // Search text
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter Category
        if ($categorySlug = $request->input('categoria')) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Filter Brand
        if ($brandSlug = $request->input('marca')) {
            $query->whereHas('brand', function ($q) use ($brandSlug) {
                $q->where('slug', $brandSlug);
            });
        }

        // Filter Min/Max Price
        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', (float) $minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        // Filter Only In Stock
        if ($request->boolean('stock')) {
            $query->where('stock', '>', 0);
        }

        // Filter Offers
        if ($request->boolean('ofertas')) {
            $query->where('is_offer', true);
        }

        // Sorting
        $sort = $request->input('orden', 'destacados');
        match ($sort) {
            'precio_asc' => $query->orderBy('price', 'asc'),
            'precio_desc' => $query->orderBy('price', 'desc'),
            'nuevos' => $query->latest(),
            'rating' => $query->orderBy('rating', 'desc'),
            default => $query->orderBy('is_featured', 'desc')->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        $brands = Brand::where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->get();

        $combos = null;
        if ($request->boolean('combos')) {
            $combos = Combo::with('items.product')->where('is_active', true)->get();
        }

        return view('pages.catalog', compact(
            'products',
            'categories',
            'brands',
            'combos'
        ));
    }

    public function show(string $slug, WhatsAppService $whatsAppService): View
    {
        $product = Product::with(['brand', 'category', 'subcategory', 'images'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $product->increment('views_count');

        $relatedProducts = Product::with(['brand', 'category'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $whatsAppBuyUrl = $whatsAppService->productInquiryLink($product);

        return view('pages.product-detail', compact(
            'product',
            'relatedProducts',
            'whatsAppBuyUrl'
        ));
    }

    public function pdf(): View
    {
        $settings = CompanySetting::current();
        $products = Product::with(['category', 'brand'])
            ->where('is_active', true)
            ->orderBy('category_id')
            ->get();

        return view('pages.catalog-pdf', compact('settings', 'products'));
    }
}
