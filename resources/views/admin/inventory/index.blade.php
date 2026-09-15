@extends('layouts.admin')

@section('title', 'Kardex e Inventario')
@section('page_title', 'Inventario y Kardex')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Control de Inventario y Kardex
            </h1>
            <p class="text-xs text-slate-500">Auditoría completa de movimientos de stock: ventas, entradas, ajustes manuales y devoluciones.</p>
        </div>
    </div>

    <!-- Quick Manual Adjustment Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm space-y-4">
        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
            <x-icon name="edit" class="w-4 h-4 text-cyan-500" />
            <span>Ajuste Manual de Inventario</span>
        </h3>

        <form action="{{ route('admin.inventory.adjust') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Producto *</label>
                <select name="product_id" required class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                    <option value="">Selecciona un producto...</option>
                    @foreach($allProducts as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Actual: {{ $p->stock }})</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Nuevo Stock Físico *</label>
                <input type="number" name="new_stock" min="0" required placeholder="0" class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 font-mono font-bold text-slate-900 dark:text-white">
            </div>

            <div class="space-y-1 sm:col-span-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Motivo del Ajuste *</label>
                <input type="text" name="reason" required placeholder="Ej. Conteo físico, ingreso de mercadería..." class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>

            <div class="sm:pt-5">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md transition">
                    Registrar Movimiento
                </button>
            </div>
        </form>
    </div>

    <!-- Kardex Log Table -->
    <div class="rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm overflow-hidden space-y-4">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                Historial de Movimientos de Kardex
            </h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.inventory.index') }}" class="text-xs text-slate-400 hover:text-white font-bold">Todos</a>
                <span class="text-slate-600">•</span>
                <a href="{{ route('admin.inventory.index', ['type' => 'sale']) }}" class="text-xs text-cyan-400 hover:underline font-bold">Ventas</a>
                <span class="text-slate-600">•</span>
                <a href="{{ route('admin.inventory.index', ['type' => 'entry']) }}" class="text-xs text-emerald-400 hover:underline font-bold">Entradas</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4">Producto</th>
                        <th class="py-3 px-4">Tipo</th>
                        <th class="py-3 px-4">Cantidad</th>
                        <th class="py-3 px-4">Stock Antes / Después</th>
                        <th class="py-3 px-4">Motivo / Pedido</th>
                        <th class="py-3 px-4">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @foreach($movements as $mv)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <td class="py-3 px-4 font-mono text-slate-400 text-[11px]">
                                {{ $mv->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                                {{ $mv->product?->name }}
                            </td>
                            <td class="py-3 px-4">
                                @if($mv->type === 'sale')
                                    <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 font-bold text-[10px]">Venta</span>
                                @elseif($mv->type === 'entry')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 font-bold text-[10px]">Ingreso</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-bold text-[10px]">Ajuste</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono font-bold {{ $mv->quantity < 0 ? 'text-rose-500' : 'text-emerald-500' }}">
                                {{ $mv->quantity > 0 ? '+' : '' }}{{ $mv->quantity }} uds.
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-400">
                                {{ $mv->stock_before }} → <strong class="text-slate-800 dark:text-white">{{ $mv->stock_after }}</strong>
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                {{ $mv->reason }}
                            </td>
                            <td class="py-3 px-4 text-slate-400 text-[11px]">
                                {{ $mv->user?->name ?? 'Sistema' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $movements->links() }}
        </div>
    </div>

</div>
@endsection
