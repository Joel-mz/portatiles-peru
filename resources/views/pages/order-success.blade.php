@extends('layouts.app')

@section('title', 'Pedido #' . $order->order_number . ' | NEXORA')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- Flash message -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-bold text-xs flex items-center gap-2 shadow-md">
            <x-icon name="check-circle" class="w-5 h-5 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Top Card: Order Header & Status Badge -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-xl space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-6">
            <div>
                <span class="text-xs font-mono uppercase tracking-widest text-slate-400">Detalle del Pedido</span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono tracking-tight">
                    #{{ $order->order_number }}
                </h1>
                <p class="text-xs text-slate-500">Registrado el {{ $order->created_at->format('d/m/Y H:i A') }}</p>
            </div>

            <!-- Current Payment Status Badge -->
            @php $badge = $order->statusBadge(); @endphp
            <div class="flex flex-col items-start sm:items-end">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Estado de Pago:</span>
                <span class="px-4 py-2 rounded-2xl font-black text-xs {{ $badge['bg'] }} flex items-center gap-2 shadow-sm">
                    <x-icon :name="$badge['icon']" class="w-4 h-4" />
                    <span>{{ $badge['label'] }}</span>
                </span>
            </div>
        </div>

        <!-- Visual Timeline -->
        <div class="py-2">
            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-4 uppercase tracking-wider">Línea de Vida del Pedido</h4>
            <div class="grid grid-cols-4 gap-2 text-center text-xs">
                
                <!-- Step 1: Registrado -->
                <div class="space-y-1.5">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center mx-auto text-xs">
                        1
                    </div>
                    <span class="font-bold block text-slate-800 dark:text-slate-200 text-[11px]">Registrado</span>
                    <span class="text-[10px] text-slate-400 block">Correcto</span>
                </div>

                <!-- Step 2: En Revisión -->
                <div class="space-y-1.5">
                    <div class="w-8 h-8 rounded-full {{ in_array($order->payment_status, ['pending_review', 'paid']) ? 'bg-amber-500 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }} font-bold flex items-center justify-center mx-auto text-xs">
                        2
                    </div>
                    <span class="font-bold block text-slate-800 dark:text-slate-200 text-[11px]">En Revisión</span>
                    <span class="text-[10px] text-slate-400 block">Comprobante</span>
                </div>

                <!-- Step 3: Pago Confirmado -->
                <div class="space-y-1.5">
                    <div class="w-8 h-8 rounded-full {{ $order->payment_status === 'paid' ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }} font-bold flex items-center justify-center mx-auto text-xs">
                        3
                    </div>
                    <span class="font-bold block text-slate-800 dark:text-slate-200 text-[11px]">Pago Confirmado</span>
                    <span class="text-[10px] text-slate-400 block">Stock Descontado</span>
                </div>

                <!-- Step 4: Despacho -->
                <div class="space-y-1.5">
                    <div class="w-8 h-8 rounded-full {{ $order->order_status === 'completed' ? 'bg-cyan-500 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }} font-bold flex items-center justify-center mx-auto text-xs">
                        4
                    </div>
                    <span class="font-bold block text-slate-800 dark:text-slate-200 text-[11px]">Despacho / Entrega</span>
                    <span class="text-[10px] text-slate-400 block">Envío Nacional</span>
                </div>

            </div>
        </div>

        <!-- Status Explanation Banner -->
        @if($order->payment_status === 'pending_review')
            <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 text-xs space-y-1">
                <div class="flex items-center gap-2 font-bold">
                    <x-icon name="clock" class="w-4 h-4 shrink-0" />
                    <span>Comprobante recibido - En espera de validación humana</span>
                </div>
                <p class="text-[11px] text-slate-600 dark:text-slate-300">
                    Tu captura de pago se encuentra en nuestra bandeja de verificación. Nuestro equipo administrativo validará la transacción bancaria y confirmará el pedido para proceder con el empaque y despacho.
                </p>
            </div>
        @elseif($order->payment_status === 'paid')
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs space-y-1">
                <div class="flex items-center gap-2 font-bold">
                    <x-icon name="check-circle" class="w-4 h-4 shrink-0" />
                    <span>¡Pago Confirmado y Validado!</span>
                </div>
                <p class="text-[11px] text-slate-600 dark:text-slate-300">
                    Tu pago ha sido validado satisfactoriamente el {{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i A') : 'recientemente' }}. Tu pedido está en fase de preparación para despacho.
                </p>
            </div>
        @elseif($order->payment_status === 'rejected')
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs space-y-1">
                <div class="flex items-center gap-2 font-bold">
                    <x-icon name="x-circle" class="w-4 h-4 shrink-0" />
                    <span>Comprobante Observado o Rechazado</span>
                </div>
                <p class="text-[11px] text-slate-600 dark:text-slate-300">
                    Motivo: {{ $order->rejection_reason ?? 'No se pudo verificar el abono en cuenta bancaria. Por favor comunícate por WhatsApp.' }}
                </p>
            </div>
        @else
            <div class="p-4 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-600 dark:text-cyan-400 text-xs space-y-1">
                <div class="flex items-center gap-2 font-bold">
                    <x-icon name="credit-card" class="w-4 h-4 shrink-0" />
                    <span>Pendiente de Pago</span>
                </div>
                <p class="text-[11px] text-slate-600 dark:text-slate-300">
                    Realiza tu abono mediante {{ strtoupper($order->payment_method) }} y sube la captura a continuación para iniciar la validación.
                </p>
            </div>
        @endif

        <!-- Action: Send to WhatsApp -->
        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <a 
                href="{{ $whatsAppOrderUrl }}" 
                target="_blank"
                class="flex-1 py-3 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs text-center shadow-lg shadow-emerald-600/25 transition flex items-center justify-center gap-2"
            >
                <x-icon name="whatsapp" class="w-4 h-4" />
                <span>Enviar Resumen y Comprobante por WhatsApp</span>
            </a>

            <a 
                href="{{ route('catalog') }}" 
                class="py-3 px-6 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold text-xs text-center transition"
            >
                Seguir Comprando
            </a>
        </div>

    </div>

    <!-- Upload or View Payment Proof Section -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm space-y-4">
        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
            <x-icon name="upload" class="w-4 h-4 text-cyan-500" />
            <span>Comprobante de Pago</span>
        </h3>

        @if($order->payment_proof)
            <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                <img src="{{ $order->payment_proof }}" alt="Comprobante de pago" class="w-24 h-24 object-cover rounded-xl bg-slate-200 dark:bg-slate-800 border shrink-0">
                <div class="space-y-1 text-xs">
                    <span class="font-bold text-slate-800 dark:text-white block">Comprobante adjuntado correctamente</span>
                    <span class="text-slate-500 block">Subido el: {{ $order->proof_uploaded_at?->format('d/m/Y H:i A') }}</span>
                    <a href="{{ $order->payment_proof }}" target="_blank" class="text-cyan-500 hover:underline font-bold inline-flex items-center gap-1 pt-1">
                        <span>Ver comprobante completo</span>
                        <x-icon name="external-link" class="w-3 h-3" />
                    </a>
                </div>
            </div>
        @else
            <form action="{{ route('order.uploadProof', $order->order_number) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <p class="text-xs text-slate-500">¿Ya realizaste tu transferencia o Yape? Adjunta la captura aquí:</p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input 
                        type="file" 
                        name="payment_proof" 
                        required
                        accept="image/png, image/jpeg, image/webp"
                        class="flex-1 text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"
                    >
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shrink-0">
                        Subir Comprobante
                    </button>
                </div>
            </form>
        @endif
    </div>

    <!-- Order Items and Financial Breakdown -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm space-y-6">
        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
            Productos Solicitados
        </h3>

        <div class="divide-y divide-slate-100 dark:divide-slate-800/60">
            @foreach($order->items as $item)
                <div class="py-3 flex items-center justify-between text-xs">
                    <div class="space-y-0.5">
                        <h4 class="font-bold text-slate-900 dark:text-white">{{ $item->product_name }}</h4>
                        <span class="text-[10px] font-mono text-slate-400">SKU: {{ $item->product_sku }} • Cant: {{ $item->quantity }}</span>
                    </div>
                    <span class="font-mono font-bold text-slate-900 dark:text-white">
                        S/ {{ number_format($item->subtotal, 2) }}
                    </span>
                </div>
            @endforeach
        </div>

        <!-- Amounts summary -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2 text-xs">
            <div class="flex justify-between text-slate-500">
                <span>Subtotal:</span>
                <span class="font-mono font-bold">S/ {{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->discount > 0)
                <div class="flex justify-between text-emerald-500 font-bold">
                    <span>Descuento:</span>
                    <span class="font-mono">-S/ {{ number_format($order->discount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between text-slate-500">
                <span>IGV (18% incluido):</span>
                <span class="font-mono">S/ {{ number_format($order->igv, 2) }}</span>
            </div>
            <div class="flex justify-between text-base font-black text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-700">
                <span>Total:</span>
                <span class="text-xl font-mono text-cyan-500">S/ {{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

</div>
@endsection
