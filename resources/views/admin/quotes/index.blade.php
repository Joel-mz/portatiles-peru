@extends('layouts.admin')

@section('title', 'Gestión de Cotizaciones')
@section('page_title', 'Cotizaciones')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Solicitudes de Cotización
            </h1>
            <p class="text-xs text-slate-500">Administra solicitudes formales de empresas y clientes particulares (COT-XXXXXX).</p>
        </div>
    </div>

    <!-- Quotes Table -->
    <div class="rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Cotización #</th>
                        <th class="py-3.5 px-4">Fecha</th>
                        <th class="py-3.5 px-4">Cliente / Empresa</th>
                        <th class="py-3.5 px-4">Teléfono / Email</th>
                        <th class="py-3.5 px-4">Total Estimado</th>
                        <th class="py-3.5 px-4">Estado</th>
                        <th class="py-3.5 px-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @foreach($quotes as $quote)
                        @php $badge = $quote->statusBadge(); @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                #{{ $quote->quote_number }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 text-[11px]">
                                {{ $quote->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 space-y-0.5">
                                <span class="font-bold text-slate-800 dark:text-white block">{{ $quote->customer_name }}</span>
                                @if($quote->company_name)
                                    <span class="text-[10px] text-slate-400 block">{{ $quote->company_name }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 space-y-0.5 text-slate-500">
                                <div>{{ $quote->customer_phone }}</div>
                                <div class="text-[10px]">{{ $quote->customer_email }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-cyan-500">
                                S/ {{ number_format($quote->total, 2) }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold {{ $badge['bg'] }} inline-flex items-center gap-1">
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a 
                                    href="{{ route('admin.quotes.show', $quote->id) }}"
                                    class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs inline-flex items-center gap-1 transition"
                                >
                                    <span>Gestionar</span>
                                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $quotes->links() }}
        </div>
    </div>

</div>
@endsection
