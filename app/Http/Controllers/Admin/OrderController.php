<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('items');

        if ($status = $request->input('status')) {
            $query->where('payment_status', $status);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all' => Order::count(),
            'pending_review' => Order::where('payment_status', 'pending_review')->count(),
            'pending' => Order::where('payment_status', 'pending')->count(),
            'paid' => Order::where('payment_status', 'paid')->count(),
            'rejected' => Order::where('payment_status', 'rejected')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'verifiedBy', 'stockMovements']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * CRITICAL ACTION: Confirms payment and triggers transactional stock deduction
     */
    public function confirmPayment(Order $order, InventoryService $inventoryService): RedirectResponse
    {
        try {
            $inventoryService->confirmOrderPayment($order, auth()->user());

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', "¡Pago del pedido #{$order->order_number} confirmado! El inventario ha sido descontado y el movimiento registrado en el Kardex.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al confirmar pago: '.$e->getMessage());
        }
    }

    /**
     * Rejects payment proof. Stock remains untouched.
     */
    public function rejectPayment(Request $request, Order $order, InventoryService $inventoryService): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:255'],
        ]);

        $inventoryService->rejectOrderPayment($order, $request->input('rejection_reason'), auth()->user());

        return redirect()->route('admin.orders.show', $order->id)
            ->with('warning', "El pago del pedido #{$order->order_number} ha sido rechazado. El inventario permanece inalterado.");
    }

    /**
     * Update general order status (processing, completed, cancelled)
     */
    public function updateOrderStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'order_status' => ['required', 'in:pending,processing,completed,cancelled'],
        ]);

        $order->update(['order_status' => $request->input('order_status')]);

        return back()->with('success', 'Estado del pedido actualizado.');
    }
}
