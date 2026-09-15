@extends('layouts.admin')

@section('title', 'Gestión de Pedidos')
@section('page_title', 'Pedidos')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Pedidos y Validación de Pagos
            </h1>
            <p class="text-xs text-slate-500">Supervisa las órdenes, audita comprobantes Yape/Plin/Transferencia y confirma abonos.</p>
        </div>
    </div>

    <!-- Status Tabs / Counters -->
    <div class="flex flex-wrap gap-2 text-xs">
        <a 
            href="{{ route('admin.orders.index') }}" 
            class="px-4 py-2 rounded-xl border transition font-bold {{ !request('status') ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30' : 'bg-white dark:bg-[#0F172A] border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400' }}"
        >
            Todos ({{ $counts['all'] }})
        </a>

        <a 
            href="{{ route('admin.orders.index', ['status' => 'pending_review']) }}" 
            class="px-4 py-2 rounded-xl border transition font-bold flex items-center gap-1.5 {{ request('status') === 'pending_review' ? 'bg-amber-500/20 text-amber-400 border-amber-500/50' : 'bg-white dark:bg-[#0F172A] border-slate-200 dark:border-slate-800 text-amber-500' }}"
        >
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span>En Revisión ({{ $counts['pending_review'] }})</span>
        </a>

        <a 
            href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
            class="px-4 py-2 rounded-xl border transition font-bold {{ request('status') === 'pending' ? 'bg-blue-500/10 text-blue-400 border-blue-500/30' : 'bg-white dark:bg-[#0F172A] border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400' }}"
        >
            Pendiente de Pago ({{ $counts['pending'] }})
        </a>

        <a 
            href="{{ route('admin.orders.index', ['status' => 'paid']) }}" 
            class="px-4 py-2 rounded-xl border transition font-bold {{ request('status') === 'paid' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-white dark:bg-[#0F172A] border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400' }}"
        >
            Pagados / Validados ({{ $counts['paid'] }})
        </a>

        <a 
            href="{{ route('admin.orders.index', ['status' => 'rejected']) }}" 
            class="px-4 py-2 rounded-xl border transition font-bold {{ request('status') === 'rejected' ? 'bg-rose-500/10 text-rose-400 border-rose-500/30' : 'bg-white dark:bg-[#0F172A] border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400' }}"
        >
            Rechazados ({{ $counts['rejected'] }})
        </a>
    </div>

    <!-- Search filter -->
    <div class="p-4 rounded-2xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex gap-3 text-xs">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input 
                type="text" 
                name="q" 
                value="{{ request('q') }}"
                placeholder="Buscar por número de pedido (PED-...), cliente, email o teléfono..." 
                class="flex-1 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white"
            >
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold">
                Buscar
            </button>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Pedido #</th>
                        <th class="py-3.5 px-4">Fecha</th>
                        <th class="py-3.5 px-4">Cliente</th>
                        <th class="py-3.5 px-4">Método</th>
                        <th class="py-3.5 px-4">Comprobante</th>
                        <th class="py-3.5 px-4">Total</th>
                        <th class="py-3.5 px-4">Estado Pago</th>
                        <th class="py-3.5 px-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @foreach($orders as $order)
                        @php $badge = $order->statusBadge(); @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                #{{ $order->order_number }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 text-[11px]">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 space-y-0.5">
                                <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $order->customer_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $order->customer_phone }}</span>
                            </td>
                            <td class="py-3.5 px-4 uppercase font-bold text-slate-500">
                                {{ $order->payment_method }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($order->payment_proof)
                                    <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-400 font-bold text-[10px] border border-blue-500/30 flex items-center gap-1 w-fit">
                                        <x-icon name="check-circle" class="w-3 h-3" />
                                        <span>Adjunto</span>
                                    </span>
                                @else
                                    <span class="text-slate-500 text-[10px]">Sin archivo</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-cyan-500">
                                S/ {{ number_format($order->total, 2) }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold {{ $badge['bg'] }} inline-flex items-center gap-1">
                                    <x-icon :name="$badge['icon']" class="w-3 h-3" />
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a 
                                    href="{{ route('admin.orders.show', $order->id) }}" 
                                    class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs inline-flex items-center gap-1 transition shadow-sm"
                                >
                                    <span>Auditar</span>
                                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection
