@extends('layouts.app')

@section('title', 'Finalizar pedido por WhatsApp | NEXORA')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-12" x-data>
    <div class="rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800 p-6 sm:p-10 text-center space-y-5 shadow-sm">
        <div class="mx-auto h-14 w-14 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center"><x-icon name="whatsapp" class="w-7 h-7" /></div>
        <div><h1 class="text-2xl font-black text-slate-900 dark:text-white">Finaliza tu pedido por WhatsApp</h1><p class="mt-2 text-sm text-slate-500">No realizamos pagos ni solicitamos comprobantes en la web. Un asesor confirmará disponibilidad, entrega y pago directamente por WhatsApp.</p></div>
        <template x-if="$store.cart.items.length === 0"><a href="{{ route('catalog') }}" class="inline-block px-5 py-3 rounded-xl bg-blue-600 text-white font-bold text-sm">Ver catálogo</a></template>
        <template x-if="$store.cart.items.length > 0"><button type="button" @click="let message = 'Hola, deseo consultar este pedido:%0A%0A'; $store.cart.items.forEach(item => message += `• ${item.quantity}x ${item.name} (SKU: ${item.sku}) - S/ ${(item.price * item.quantity).toFixed(2)}%0A`); message += `%0ATotal estimado: S/ ${$store.cart.total.toFixed(2)}`; window.open('https://wa.me/{{ app(\App\Services\WhatsAppService::class)->getCleanPhone() }}?text=' + message, '_blank')" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm"><x-icon name="whatsapp" class="w-5 h-5" /> Continuar por WhatsApp</button></template>
    </div>
</section>
@endsection
