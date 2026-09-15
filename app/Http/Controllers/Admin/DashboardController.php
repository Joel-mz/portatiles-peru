<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Quote;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalOrders = Order::count();
        $pendingReviewOrdersCount = Order::where('payment_status', 'pending_review')->count();
        $paidOrdersTotal = Order::where('payment_status', 'paid')->sum('total');
        $pendingQuotesCount = Quote::where('status', 'pending')->count();

        $lowStockProducts = Product::where('stock', '<=', 3)
            ->where('is_active', true)
            ->orderBy('stock')
            ->take(6)
            ->get();

        $pendingReviewOrders = Order::with('items')
            ->where('payment_status', 'pending_review')
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = Order::with('items')
            ->latest()
            ->take(6)
            ->get();

        $recentQuotes = Quote::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalOrders',
            'pendingReviewOrdersCount',
            'paidOrdersTotal',
            'pendingQuotesCount',
            'lowStockProducts',
            'pendingReviewOrders',
            'recentOrders',
            'recentQuotes'
        ));
    }
}
