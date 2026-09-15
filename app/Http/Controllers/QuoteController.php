<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Quote;
use App\Services\QuoteService;
use App\Services\WhatsAppService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function create(Request $request): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $selectedProductId = $request->query('producto_id');

        return view('pages.quote-create', compact('products', 'selectedProductId'));
    }

    public function store(Request $request, QuoteService $quoteService): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'company_ruc' => ['nullable', 'string', 'max:20'],
            'message' => ['nullable', 'string', 'max:1000'],
            'items_json' => ['required', 'string'],
        ], [
            'customer_name.required' => 'Ingresa tu nombre o persona de contacto.',
            'customer_email.required' => 'Ingresa tu correo para recibir la cotización.',
            'customer_phone.required' => 'El teléfono / WhatsApp es necesario para coordinar la cotización.',
            'items_json.required' => 'Debes agregar al menos un producto a la solicitud de cotización.',
        ]);

        $items = json_decode($validated['items_json'], true);

        if (empty($items) || ! is_array($items)) {
            return back()->withInput()->with('error', 'Selecciona al menos un producto para cotizar.');
        }

        $quote = $quoteService->createQuote($validated, $items);

        return redirect()->route('quotes.show', ['quoteNumber' => $quote->quote_number])
            ->with('success', '¡Cotización generada exitosamente!');
    }

    public function show(string $quoteNumber, WhatsAppService $whatsAppService): View
    {
        $quote = Quote::with('items.product')
            ->where('quote_number', $quoteNumber)
            ->firstOrFail();

        $whatsAppQuoteUrl = $whatsAppService->quoteInquiryLink($quote);

        return view('pages.quote-show', compact('quote', 'whatsAppQuoteUrl'));
    }
}
