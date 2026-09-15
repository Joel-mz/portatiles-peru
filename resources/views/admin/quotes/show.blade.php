@extends('layouts.admin')

@section('title', 'Cotización #' . $quote->quote_number)
@section('page_title', 'Detalle Cotización')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-slate-900 dark:text-white font-mono">
                    #{{ $quote->quote_number }}
                </h1>
                @php $badge = $quote->statusBadge(); @endphp
                <span class="px-3 py-1 rounded-xl text-xs font-bold {{ $badge['bg'] }}">
                    {{ $badge['label'] }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Generada el {{ $quote->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <a href="{{ route('admin.quotes.index') }}" class="text-xs font-bold text-slate-400 hover:text-white">
            ← Volver a lista
        </a>
    </div>

    <!-- Status Change Form Card -->
    <div class="p-6 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm space-y-4">
        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Actualizar Estado Comercial</h3>
        <form action="{{ route('admin.quotes.updateStatus', $quote->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Nuevo Estado:</label>
                <select name="status" class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                    <option value="pending" {{ $quote->status === 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="reviewing" {{ $quote->status === 'reviewing' ? 'selected' : '' }}>En Revisión Comercial</option>
                    <option value="sent" {{ $quote->status === 'sent' ? 'selected' : '' }}>Enviada al Cliente</option>
                    <option value="accepted" {{ $quote->status === 'accepted' ? 'selected' : '' }}>Aceptada / Venta Concretada</option>
                    <option value="rejected" {{ $quote->status === 'rejected' ? 'selected' : '' }}>Rechazada</option>
                    <option value="expired" {{ $quote->status === 'expired' ? 'selected' : '' }}>Vencida</option>
                </select>
            </div>

            <div class="space-y-1 sm:col-span-2">
                <label class="font-bold text-slate-700 dark:text-slate-300">Notas Administrativas:</label>
                <div class="flex gap-2">
                    <input type="text" name="admin_notes" value="{{ old('admin_notes', $quote->admin_notes) }}" placeholder="Ej. Se ofreció 5% de descuento adicional por volumen" class="flex-1 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold shrink-0">
                        Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Customer & Items Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Customer Info -->
        <div class="p-6 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800 space-y-3 text-xs">
            <h3 class="font-bold text-sm text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2">
                Datos del Cliente
            </h3>
            <p><strong>Nombre:</strong> {{ $quote->customer_name }}</p>
            <p><strong>Email:</strong> {{ $quote->customer_email }}</p>
            <p><strong>Teléfono:</strong> {{ $quote->customer_phone }}</p>
            @if($quote->company_name)
                <p><strong>Empresa:</strong> {{ $quote->company_name }}</p>
            @endif
            @if($quote->company_ruc)
                <p><strong>RUC:</strong> {{ $quote->company_ruc }}</p>
            @endif
            @if($quote->message)
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 mt-2">
                    <span class="font-bold block mb-1 text-slate-400">Mensaje del cliente:</span>
                    <p class="text-slate-600 dark:text-slate-300">{{ $quote->message }}</p>
                </div>
            @endif

            <div class="pt-3">
                <a href="{{ $whatsAppQuoteUrl }}" target="_blank" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2">
                    <x-icon name="whatsapp" class="w-4 h-4" />
                    <span>Contactar al Cliente por WhatsApp</span>
                </a>
            </div>
        </div>

        <!-- Items Table -->
        <div class="p-6 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800 space-y-4 text-xs">
            <h3 class="font-bold text-sm text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2">
                Equipos Requeridos
            </h3>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($quote->items as $item)
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $item->product_name }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">SKU: {{ $item->product_sku ?? 'N/A' }} • Cant: {{ $item->quantity }}</span>
                        </div>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">
                            S/ {{ number_format($item->subtotal, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800 text-sm">
                <span class="font-bold text-slate-700 dark:text-slate-300">Total Referencial:</span>
                <span class="text-xl font-black font-mono text-cyan-500">S/ {{ number_format($quote->total, 2) }}</span>
            </div>
        </div>

    </div>

</div>
@endsection
