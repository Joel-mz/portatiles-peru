@extends('layouts.admin')

@section('title', 'Dashboard SaaS')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-8">

    <!-- KPI Metric Cards Grid (Matching mockup) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Products -->
        <div class="admin-kpi dark:bg-[#0F172A] dark:border-slate-800/80 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Productos</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 font-extrabold text-[10px]">+12%</span>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $totalProducts }}</span>
                <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-500">
                    <x-icon name="laptop" class="w-5 h-5" />
                </div>
            </div>
            <span class="text-[11px] text-slate-400 block">En catálogo público</span>
        </div>

        <!-- Categories -->
        <div class="admin-kpi dark:bg-[#0F172A] dark:border-slate-800/80 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Categorías</span>
                <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-500 font-extrabold text-[10px]">+5%</span>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $totalCategories }}</span>
                <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-500">
                    <x-icon name="grid" class="w-5 h-5" />
                </div>
            </div>
            <span class="text-[11px] text-slate-400 block">Líneas activas</span>
        </div>

        <!-- Orders -->
        <div class="admin-kpi dark:bg-[#0F172A] dark:border-slate-800/80 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pedidos</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 font-extrabold text-[10px]">+18%</span>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $totalOrders }}</span>
                <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-500">
                    <x-icon name="shopping-bag" class="w-5 h-5" />
                </div>
            </div>
            <span class="text-[11px] text-slate-400 block">Total registrados</span>
        </div>

        <!-- Verified Sales Total -->
        <div class="admin-kpi dark:bg-[#0F172A] dark:border-slate-800/80 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ventas Confirmadas</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 font-extrabold text-[10px]">Real</span>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-cyan-500 font-mono">S/ {{ number_format($paidOrdersTotal, 2) }}</span>
                <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-500">
                    <x-icon name="check-circle" class="w-5 h-5" />
                </div>
            </div>
            <span class="text-[11px] text-slate-400 block">Abonos validados</span>
        </div>

    </div>

    <!-- CRITICAL VERIFICATION ALERT BOX (When orders have pending review proof) -->
    @if($pendingReviewOrders->isNotEmpty())
        <div class="p-6 rounded-3xl bg-amber-500/10 border border-amber-500/30 shadow-lg space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center font-bold">
                        <x-icon name="clock" class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-black text-amber-600 dark:text-amber-400">
                            {{ $pendingReviewOrdersCount }} Comprobantes de Pago Pendientes de Verificación
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300">
                            Recuerda: Los clientes han adjuntado comprobantes Yape/Plin/Transferencia. El stock NO se descuenta hasta que hagas clic en "Confirmar Pago".
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.orders.index', ['status' => 'pending_review']) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-md transition">
                    Revisar Todos
                </a>
            </div>

            <!-- List of pending review orders -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
                @foreach($pendingReviewOrders as $pOrder)
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#0B1120] border border-amber-500/20 shadow-sm flex items-center justify-between text-xs">
                        <div class="space-y-1">
                            <span class="font-mono font-bold text-slate-900 dark:text-white">#{{ $pOrder->order_number }}</span>
                            <span class="text-slate-500 block">{{ $pOrder->customer_name }}</span>
                            <span class="text-cyan-500 font-mono font-bold">S/ {{ number_format($pOrder->total, 2) }}</span>
                        </div>
                        <a 
                            href="{{ route('admin.orders.show', $pOrder->id) }}"
                            class="px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-[11px] shadow-sm transition"
                        >
                            Ver Evidencia
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Charts & Analytics Section (Matching Mockup with Graphic Line) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Sales & Quotes Activity Chart (8 cols) -->
        <div class="lg:col-span-8 admin-surface p-5 sm:p-6 rounded-2xl dark:bg-[#0F172A] dark:border-slate-800/80 space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Ventas y Solicitudes de los Últimos 7 Días</h3>
                    <p class="text-xs text-slate-500">Tendencia de actividad de pedidos y consultas</p>
                </div>
                <span class="text-xs font-mono font-bold text-cyan-400 bg-cyan-500/10 px-3 py-1 rounded-full border border-cyan-500/20">
                    En Tiempo Real
                </span>
            </div>

            <!-- Sleek SVG Vector Graph -->
            <div class="h-64 w-full flex items-end pt-8 pb-4 relative">
                <!-- SVG Area Curve -->
                <svg class="w-full h-full overflow-visible" viewBox="0 0 700 200" fill="none">
                    <defs>
                        <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#00F0FF" stop-opacity="0.3"/>
                            <stop offset="100%" stop-color="#00F0FF" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <!-- Grid Lines -->
                    <line x1="0" y1="50" x2="700" y2="50" stroke="currentColor" class="text-slate-200 dark:text-slate-800" stroke-dasharray="4"/>
                    <line x1="0" y1="100" x2="700" y2="100" stroke="currentColor" class="text-slate-200 dark:text-slate-800" stroke-dasharray="4"/>
                    <line x1="0" y1="150" x2="700" y2="150" stroke="currentColor" class="text-slate-200 dark:text-slate-800" stroke-dasharray="4"/>
                    
                    <!-- Area Fill -->
                    <path d="M0,160 Q100,140 200,80 T400,100 T600,40 T700,60 L700,200 L0,200 Z" fill="url(#chartGrad)"/>
                    
                    <!-- Path Stroke -->
                    <path d="M0,160 Q100,140 200,80 T400,100 T600,40 T700,60" stroke="#00F0FF" stroke-width="3" fill="none"/>
                    
                    <!-- Dots -->
                    <circle cx="200" cy="80" r="5" fill="#00F0FF" class="animate-ping"/>
                    <circle cx="200" cy="80" r="5" fill="#00F0FF"/>
                    <circle cx="600" cy="40" r="5" fill="#00F0FF"/>
                </svg>
            </div>

            <!-- Graph Days Labels -->
            <div class="flex justify-between text-[11px] font-mono text-slate-400 border-t border-slate-100 dark:border-slate-800 pt-3">
                <span>Lun</span>
                <span>Mar</span>
                <span>Mié</span>
                <span>Jue</span>
                <span>Vie</span>
                <span>Sáb</span>
                <span>Dom</span>
            </div>
        </div>

        <!-- Inventory Alerts & Stock Bajo (4 cols) -->
        <div class="lg:col-span-4 admin-surface p-5 sm:p-6 rounded-2xl dark:bg-[#0F172A] dark:border-slate-800/80 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <x-icon name="refresh-cw" class="w-4 h-4 text-rose-500" />
                    <span>Alertas de Inventario</span>
                </h3>
                <a href="{{ route('admin.inventory.index') }}" class="text-xs text-cyan-500 hover:underline font-bold">Kardex</a>
            </div>

            @if($lowStockProducts->isEmpty())
                <div class="text-center py-8 text-xs text-slate-400 space-y-2">
                    <x-icon name="check-circle" class="w-8 h-8 text-emerald-500 mx-auto" />
                    <p>Todos los productos tienen niveles óptimos de stock.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($lowStockProducts as $lowP)
                        <div class="p-3 rounded-2xl bg-rose-500/5 dark:bg-rose-950/20 border border-rose-500/20 flex items-center justify-between text-xs">
                            <div class="space-y-0.5 min-w-0 pr-2">
                                <h4 class="font-bold text-slate-900 dark:text-white truncate">{{ $lowP->name }}</h4>
                                <span class="text-[10px] font-mono text-slate-400">SKU: {{ $lowP->sku }}</span>
                            </div>
                            <span class="px-2.5 py-1 rounded-xl bg-rose-500 text-white font-mono font-black text-xs shrink-0">
                                {{ $lowP->stock }} uds.
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- Recent Orders Table -->
    <div class="admin-surface p-5 sm:p-6 rounded-2xl dark:bg-[#0F172A] dark:border-slate-800/80 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Últimos Pedidos Registrados</h3>
                <p class="text-xs text-slate-500">Transacciones comerciales generadas desde la tienda web</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-cyan-500 hover:underline">
                Ver todos los pedidos
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-bold uppercase">
                        <th class="py-3 px-4">Pedido #</th>
                        <th class="py-3 px-4">Cliente</th>
                        <th class="py-3 px-4">Método</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Estado Pago</th>
                        <th class="py-3 px-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @foreach($recentOrders as $order)
                        @php $badge = $order->statusBadge(); @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                #{{ $order->order_number }}
                            </td>
                            <td class="py-3.5 px-4 space-y-0.5">
                                <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $order->customer_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $order->customer_phone }}</span>
                            </td>
                            <td class="py-3.5 px-4 uppercase font-bold text-slate-600 dark:text-slate-400">
                                {{ $order->payment_method }}
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
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="p-2 text-cyan-500 hover:text-cyan-400 transition font-bold inline-flex items-center gap-1">
                                    <span>Detalle</span>
                                    <x-icon name="chevron-right" class="w-3 h-3" />
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
