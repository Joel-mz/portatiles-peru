@props(['product'])

@php
    $whatsAppService = app(\App\Services\WhatsAppService::class);
    $whatsAppUrl = $whatsAppService->productInquiryLink($product);
@endphp

<div class="group relative rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 hover:border-cyan-500/50 dark:hover:border-cyan-500/50 shadow-sm hover:shadow-xl hover:shadow-cyan-500/10 transition-all duration-300 flex flex-col overflow-hidden">
    
    <!-- Badges Row -->
    <div class="absolute top-3 left-3 right-3 z-10 flex items-center justify-between pointer-events-none">
        <div class="flex flex-wrap gap-1.5">
            @if($product->discount_percentage > 0)
                <span class="px-2 py-0.5 rounded-md bg-rose-600 text-white font-black text-[10px] tracking-wider shadow-sm">
                    -{{ $product->discount_percentage }}%
                </span>
            @endif

            @if($product->is_new)
                <span class="px-2 py-0.5 rounded-md bg-cyan-600 text-white font-black text-[10px] tracking-wider shadow-sm">
                    Nuevo
                </span>
            @endif
        </div>

        @if($product->in_stock)
            <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold text-[10px] border border-emerald-500/20 backdrop-blur-md">
                En stock
            </span>
        @else
            <span class="px-2 py-0.5 rounded-md bg-slate-500/10 text-slate-500 font-bold text-[10px] border border-slate-500/20 backdrop-blur-md">
                Agotado
            </span>
        @endif
    </div>

    <!-- Image Container with Hover Zoom -->
    <a href="{{ route('product.detail', $product->slug) }}" class="relative block aspect-[4/3] bg-slate-100 dark:bg-slate-900/50 overflow-hidden p-6 flex items-center justify-center">
        <img 
            src="{{ $product->main_image }}" 
            alt="{{ $product->name }}" 
            class="w-full h-full object-contain object-center group-hover:scale-108 transition-transform duration-500 ease-out"
            loading="lazy"
        >
    </a>

    <!-- Product Details -->
    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
        <div class="space-y-1.5">
            <!-- Brand & Category -->
            <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
                <span>{{ $product->brand?->name ?? 'Nexora' }}</span>
                <span class="text-slate-400 dark:text-slate-600 font-mono text-[10px]">{{ $product->sku }}</span>
            </div>

            <!-- Title -->
            <h3 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors line-clamp-2 leading-snug">
                <a href="{{ route('product.detail', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h3>

            <!-- Rating Stars -->
            <div class="flex items-center gap-1.5 pt-0.5 text-xs">
                <div class="flex text-amber-400">
                    @for($i = 0; $i < 5; $i++)
                        <x-icon name="star" class="w-3.5 h-3.5 fill-current" />
                    @endfor
                </div>
                <span class="text-[11px] text-slate-400 dark:text-slate-500">
                    ({{ $product->reviews_count }} op.)
                </span>
            </div>
        </div>

        <!-- Price and Actions -->
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 space-y-3">
            <div class="flex items-baseline gap-2">
                <span class="text-xl font-black text-slate-900 dark:text-white font-mono tracking-tight">
                    {{ $product->formattedPrice() }}
                </span>
                @if($product->formattedOriginalPrice())
                    <span class="text-xs text-slate-400 line-through font-mono">
                        {{ $product->formattedOriginalPrice() }}
                    </span>
                @endif
            </div>

            <!-- Action Buttons Grid -->
            <div class="grid grid-cols-5 gap-2">
                <!-- Add to Cart (Alpine.js) -->
                <button 
                    type="button" 
                    @click="$store.cart.addItem({
                        id: {{ $product->id }},
                        name: '{{ addslashes($product->name) }}',
                        sku: '{{ $product->sku }}',
                        price: {{ $product->effective_price }},
                        image: '{{ $product->main_image }}',
                        slug: '{{ $product->slug }}',
                        stock: {{ $product->stock }}
                    })"
                    class="col-span-3 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-1.5"
                    title="Agregar al carrito"
                >
                    <x-icon name="shopping-cart" class="w-3.5 h-3.5" />
                    <span>Agregar</span>
                </button>

                <!-- WhatsApp Quick Buy -->
                <a 
                    href="{{ $whatsAppUrl }}" 
                    target="_blank"
                    class="col-span-2 py-2 px-2 rounded-xl bg-emerald-600/10 hover:bg-emerald-600 text-emerald-600 hover:text-white border border-emerald-500/30 transition text-xs font-bold flex items-center justify-center gap-1"
                    title="Consultar por WhatsApp"
                >
                    <x-icon name="whatsapp" class="w-3.5 h-3.5" />
                    <span>WhatsApp</span>
                </a>
            </div>
        </div>

    </div>

</div>
