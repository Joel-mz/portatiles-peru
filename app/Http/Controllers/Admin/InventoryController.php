<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $movementsQuery = StockMovement::with(['product', 'order', 'user']);

        if ($type = $request->input('type')) {
            $movementsQuery->where('type', $type);
        }

        if ($productId = $request->input('product_id')) {
            $movementsQuery->where('product_id', $productId);
        }

        $movements = $movementsQuery->latest()->paginate(20)->withQueryString();

        $lowStockProducts = Product::where('stock', '<=', 3)
            ->where('is_active', true)
            ->orderBy('stock')
            ->get();

        $allProducts = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.inventory.index', compact(
            'movements',
            'lowStockProducts',
            'allProducts'
        ));
    }

    public function adjust(Request $request, InventoryService $inventoryService): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'new_stock' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $inventoryService->adjustStock(
            $product,
            (int) $validated['new_stock'],
            $validated['reason'],
            auth()->user()
        );

        return back()->with('success', "Inventario del producto {$product->name} actualizado a {$validated['new_stock']} unidades.");
    }
}
