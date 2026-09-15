<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Combo;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $banners = Banner::where('is_active', true)->orderBy('sort_order')->get();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $featuredProducts = Product::with(['brand', 'category'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        $offers = Product::with(['brand', 'category'])
            ->where('is_active', true)
            ->where('is_offer', true)
            ->take(4)
            ->get();

        $combos = Combo::with('items.product')
            ->where('is_active', true)
            ->take(3)
            ->get();

        $brands = Brand::where('is_active', true)
            ->where('is_featured', true)
            ->get();

        return view('pages.home', compact(
            'banners',
            'categories',
            'featuredProducts',
            'offers',
            'combos',
            'brands'
        ));
    }
}
