<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Services\QuoteService;
use App\Services\WhatsAppService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index(Request $request): View
    {
        $query = Quote::with('items');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $quotes = $query->latest()->paginate(15)->withQueryString();

        return view('admin.quotes.index', compact('quotes'));
    }

    public function show(Quote $quote, WhatsAppService $whatsAppService): View
    {
        $quote->load('items.product');
        $whatsAppQuoteUrl = $whatsAppService->quoteInquiryLink($quote);

        return view('admin.quotes.show', compact('quote', 'whatsAppQuoteUrl'));
    }

    public function updateStatus(Request $request, Quote $quote, QuoteService $quoteService): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,reviewing,sent,accepted,rejected,expired'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $quoteService->updateStatus($quote, $request->input('status'), $request->input('admin_notes'));

        return back()->with('success', "Estado de cotización #{$quote->quote_number} actualizado a: ".strtoupper($request->input('status')));
    }
}
