<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\WhatsAppService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function show(): View
    {
        $settings = CompanySetting::current();

        return view('pages.checkout', compact('settings'));
    }

    public function process(Request $request, OrderService $orderService): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_document_type' => ['required', 'string', 'in:DNI,RUC,CE'],
            'customer_document_number' => ['nullable', 'string', 'max:20'],
            'department' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:250'],
            'reference' => ['nullable', 'string', 'max:250'],
            'shipping_type' => ['required', 'string', 'in:delivery,pickup'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'string', 'in:yape,plin,bcp,bbva,card'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'cart_items_json' => ['required', 'string'],
            'payment_proof' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'customer_name.required' => 'Por favor ingresa tu nombre completo.',
            'customer_email.required' => 'Por favor ingresa un correo electrónico válido.',
            'customer_phone.required' => 'El teléfono de contacto / WhatsApp es obligatorio.',
            'address.required' => 'Ingresa la dirección exacta de entrega.',
            'payment_proof.image' => 'El comprobante debe ser un archivo de imagen válido (JPG, PNG, WEBP).',
            'payment_proof.max' => 'La imagen del comprobante no debe superar los 5MB.',
        ]);

        $cartItems = json_decode($validated['cart_items_json'], true);

        if (empty($cartItems) || ! is_array($cartItems)) {
            return back()->withInput()->with('error', 'Tu carrito de compras está vacío.');
        }

        $proofFile = $request->file('payment_proof');

        $order = $orderService->createOrder($validated, $cartItems, $proofFile);

        return redirect()->route('order.success', ['orderNumber' => $order->order_number])
            ->with('success', '¡Tu pedido ha sido registrado con éxito!');
    }

    public function success(string $orderNumber, WhatsAppService $whatsAppService): View
    {
        $order = Order::with('items.product')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $settings = CompanySetting::current();
        $whatsAppOrderUrl = $whatsAppService->orderPaymentNoticeLink($order);

        return view('pages.order-success', compact(
            'order',
            'settings',
            'whatsAppOrderUrl'
        ));
    }

    public function uploadProof(Request $request, string $orderNumber, OrderService $orderService): RedirectResponse
    {
        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'payment_proof.required' => 'Por favor selecciona la imagen de tu captura o voucher de pago.',
            'payment_proof.image' => 'El archivo debe ser una imagen válida.',
            'payment_proof.max' => 'El comprobante no debe superar los 5MB.',
        ]);

        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $orderService->attachProof($order, $request->file('payment_proof'));

        return back()->with('success', 'Comprobante adjuntado con éxito. El pedido ha pasado a estado "En Revisión".');
    }
}
