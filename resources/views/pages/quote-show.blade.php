@extends('layouts.app')

@section('title', 'Cotización #' . $quote->quote_number . ' | NEXORA')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-bold text-xs flex items-center gap-2 shadow-md">
            <x-icon name="check-circle" class="w-5 h-5 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-xl space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-6">
            <div>
                <span class="text-xs font-mono uppercase tracking-widest text-slate-400">Cotización Formal</span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono tracking-tight">
                    #{{ $quote->quote_number }}
                </h1>
                <p class="text-xs text-slate-500">Fecha de emisión: {{ $quote->created_at->format('d/m/Y') }} • Válida hasta: {{ $quote->valid_until ? $quote->valid_until->format('d/m/Y') : '15 días' }}</p>
            </div>

            @php $badge = $quote->statusBadge(); @endphp
            <div>
                <span class="px-4 py-2 rounded-2xl font-black text-xs {{ $badge['bg'] }} flex items-center gap-2 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-current animate-pulse"></span>
                    <span>{{ $badge['label'] }}</span>
                </span>
            </div>
        </div>

        <!-- Customer & Company details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
            <div>
                <span class="text-slate-400 font-semibold block">Cliente:</span>
                <span class="font-bold text-slate-800 dark:text-white text-sm">{{ $quote->customer_name }}</span>
                <span class="block text-slate-500">{{ $quote->customer_email }} • {{ $quote->customer_phone }}</span>
            </div>
            @if($quote->company_name)
                <div>
                    <span class="text-slate-400 font-semibold block">Empresa / Razón Social:</span>
                    <span class="font-bold text-slate-800 dark:text-white text-sm">{{ $quote->company_name }}</span>
                    @if($quote->company_ruc)
                        <span class="block font-mono text-slate-500">RUC: {{ $quote->company_ruc }}</span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Quotation Items Table -->
        <div class="space-y-3">
            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Equipos Cotizados</h3>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($quote->items as $item)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div>
                            <h4 class="font-bold text-slate-800 dark:text-slate-200">{{ $item->product_name }}</h4>
                            <span class="text-[10px] text-slate-400 font-mono">SKU: {{ $item->product_sku ?? 'N/A' }} • Cantidad: {{ $item->quantity }}</span>
                        </div>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">
                            S/ {{ number_format($item->subtotal, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-slate-200 dark:border-slate-800 text-sm">
                <span class="font-bold text-slate-700 dark:text-slate-300">Total Cotizado:</span>
                <span class="text-2xl font-black font-mono text-cyan-500">S/ {{ number_format($quote->total, 2) }}</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <a 
                href="{{ $whatsAppQuoteUrl }}" 
                target="_blank"
                class="flex-1 py-3.5 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs text-center shadow-lg shadow-emerald-600/25 transition flex items-center justify-center gap-2"
            >
                <x-icon name="whatsapp" class="w-4 h-4" />
                <span>Enviar Cotización a Asesor Comercial por WhatsApp</span>
            </a>

            <a 
                href="{{ route('catalog.pdf') }}" 
                target="_blank"
                class="py-3.5 px-6 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold text-xs text-center transition flex items-center justify-center gap-2"
            >
                <x-icon name="download" class="w-4 h-4" />
                <span>Ver Catálogo General</span>
            </a>
        </div>

    </div>

</div>
@endsection
