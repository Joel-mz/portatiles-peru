@extends('layouts.app')

@section('title', $product->name . ' | NEXORA')
@section('meta_description', Str::limit($product->short_description ?: $product->description, 150))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12">

    <!-- Breadcrumbs -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-cyan-500">Inicio</a>
        <span>/</span>
        <a href="{{ route('catalog') }}" class="hover:text-cyan-500">Catálogo</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('catalog', ['categoria' => $product->category->slug]) }}" class="hover:text-cyan-500">
                {{ $product->category->name }}
            </a>
        @endif
        <span>/</span>
        <span class="text-slate-800 dark:text-slate-300 font-semibold truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- Main Product Showcase Grid -->
    <div 
        x-data="{
            activeImage: '{{ $product->main_image }}',
            quantity: 1,
            maxStock: {{ $product->stock }},
            activeTab: 'specs',
            increment() {
                if (this.quantity < this.maxStock) this.quantity++;
            },
            decrement() {
                if (this.quantity > 1) this.quantity--;
            }
        }"
        class="grid grid-cols-1 lg:grid-cols-12 gap-10"
    >
        
        <!-- Left: Image Gallery (5 cols) -->
        <div class="lg:col-span-6 flex flex-col-reverse md:flex-row gap-4">
            <!-- Thumbnails Column -->
            <div class="flex md:flex-col gap-3 overflow-x-auto md:overflow-y-auto max-h-[500px] shrink-0">
                <button 
                    type="button" 
                    @click="activeImage = '{{ $product->main_image }}'"
                    :class="activeImage === '{{ $product->main_image }}' ? 'border-cyan-500 ring-2 ring-cyan-500/20' : 'border-slate-200 dark:border-slate-800 opacity-70 hover:opacity-100'"
                    class="w-16 h-16 rounded-xl border bg-white dark:bg-slate-900 p-2 transition shrink-0"
                >
                    <img src="{{ $product->main_image }}" alt="Thumbnail" class="w-full h-full object-contain">
                </button>
                @foreach($product->images as $img)
                    <button 
                        type="button" 
                        @click="activeImage = '{{ $img->image_path }}'"
                        :class="activeImage === '{{ $img->image_path }}' ? 'border-cyan-500 ring-2 ring-cyan-500/20' : 'border-slate-200 dark:border-slate-800 opacity-70 hover:opacity-100'"
                        class="w-16 h-16 rounded-xl border bg-white dark:bg-slate-900 p-2 transition shrink-0"
                    >
                        <img src="{{ $img->image_path }}" alt="Thumbnail" class="w-full h-full object-contain">
                    </button>
                @endforeach
            </div>

            <!-- Main High-Res Image View -->
            <div class="flex-1 aspect-square rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 p-8 flex items-center justify-center relative shadow-sm overflow-hidden group">
                <div class="absolute top-4 left-4 z-10 flex gap-2">
                    @if($product->discount_percentage > 0)
                        <span class="px-2.5 py-1 rounded-lg bg-rose-600 text-white font-extrabold text-xs shadow-md">
                            -{{ $product->discount_percentage }}%
                        </span>
                    @endif
                    @if($product->is_new)
                        <span class="px-2.5 py-1 rounded-lg bg-cyan-600 text-white font-extrabold text-xs shadow-md">
                            Nuevo
                        </span>
                    @endif
                </div>

                <img 
                    :src="activeImage" 
                    alt="{{ $product->name }}" 
                    class="max-w-full max-h-full object-contain transition-transform duration-500 group-hover:scale-110"
                >
            </div>
        </div>

        <!-- Right: Purchasing Specs & Actions (6 cols) -->
        <div class="lg:col-span-6 space-y-6">
            
            <!-- Brand, Model, Title -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-widest text-cyan-600 dark:text-cyan-400">
                        {{ $product->brand?->name }} • {{ $product->category?->name }}
                    </span>
                    <span class="text-xs font-mono text-slate-500 dark:text-slate-400">SKU: {{ $product->sku }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-snug">
                    {{ $product->name }}
                </h1>
                
                <!-- Ratings -->
                <div class="flex items-center gap-2 pt-1 text-xs">
                    <div class="flex text-amber-400">
                        @for($i = 0; $i < 5; $i++)
                            <x-icon name="star" class="w-4 h-4 fill-current" />
                        @endfor
                    </div>
                    <span class="font-bold text-slate-700 dark:text-slate-300">4.9</span>
                    <span class="text-slate-400">({{ $product->reviews_count }} opiniones de clientes)</span>
                </div>
            </div>

            <!-- Price & Stock Availability -->
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 space-y-3">
                <div class="flex items-baseline gap-3">
                    <span class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white font-mono tracking-tight">
                        {{ $product->formattedPrice() }}
                    </span>
                    @if($product->formattedOriginalPrice())
                        <span class="text-sm sm:text-base text-slate-400 line-through font-mono">
                            {{ $product->formattedOriginalPrice() }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-rose-500/10 text-rose-500 text-xs font-bold border border-rose-500/20">
                            Ahorras S/ {{ number_format($product->original_price - $product->effective_price, 2) }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-4 text-xs">
                    @if($product->in_stock)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 font-bold border border-emerald-500/20">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            En stock ({{ $product->stock }} unidades disponibles)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/10 text-rose-500 font-bold border border-rose-500/20">
                            Agotado temporalmente
                        </span>
                    @endif
                    <span class="text-slate-500 dark:text-slate-400">Garantía oficial de 12 meses</span>
                </div>
            </div>

            <!-- Quick Specs highlights (from json or model) -->
            @if(!empty($product->technical_specs))
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Ficha Rápida</span>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        @foreach(array_slice($product->technical_specs, 0, 6, true) as $specKey => $specVal)
                            <div class="p-2.5 rounded-xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-semibold">{{ $specKey }}</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 truncate block">{{ $specVal }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Add to Cart & WhatsApp Purchase Controls -->
            <div class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                
                <div class="flex items-center gap-4">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Cantidad:</label>
                    <div class="flex items-center border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
                        <button 
                            type="button" 
                            @click="decrement()"
                            class="px-3.5 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-bold"
                        >-</button>
                        <span class="px-4 py-2 font-mono font-bold text-sm dark:text-white" x-text="quantity"></span>
                        <button 
                            type="button" 
                            @click="increment()"
                            class="px-3.5 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-bold"
                        >+</button>
                    </div>
                    <span class="text-xs text-slate-400">Máx. {{ $product->stock }} uds.</span>
                </div>

                <!-- Primary Action Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    
                    <!-- Add to Cart -->
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
                        }, quantity)"
                        class="w-full py-3.5 px-6 rounded-2xl bg-blue-600 hover:bg-blue-500 active:scale-95 text-white font-bold text-sm shadow-xl shadow-blue-500/20 transition flex items-center justify-center gap-2"
                    >
                        <x-icon name="shopping-bag" class="w-5 h-5" />
                        <span>Agregar al Carrito</span>
                    </button>

                    <!-- Buy via WhatsApp -->
                    <a 
                        href="{{ $whatsAppBuyUrl }}" 
                        target="_blank"
                        class="w-full py-3.5 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-bold text-sm shadow-xl shadow-emerald-600/25 transition flex items-center justify-center gap-2"
                    >
                        <x-icon name="whatsapp" class="w-5 h-5" />
                        <span>Comprar por WhatsApp</span>
                    </a>

                </div>

                <!-- Quote formal button -->
                <div class="pt-1">
                    <a 
                        href="{{ route('quotes.create', ['producto_id' => $product->id]) }}"
                        class="w-full py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold text-center block transition border border-slate-200 dark:border-slate-700/80"
                    >
                        Solicitar Cotización Formal para Empresa / Institución (con RUC)
                    </a>
                </div>

            </div>

            <!-- Value Props Row -->
            <div class="grid grid-cols-3 gap-3 pt-4 border-t border-slate-200 dark:border-slate-800 text-center">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800">
                    <x-icon name="truck" class="w-5 h-5 text-cyan-400 mx-auto mb-1" />
                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block">Envíos a todo el Perú</span>
                    <span class="text-[9px] text-slate-400 block">Olva / Shalom / Shalom Express</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800">
                    <x-icon name="shield" class="w-5 h-5 text-emerald-400 mx-auto mb-1" />
                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block">Garantía Real</span>
                    <span class="text-[9px] text-slate-400 block">1 Año con la marca</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800">
                    <x-icon name="credit-card" class="w-5 h-5 text-purple-400 mx-auto mb-1" />
                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block">Atención por WhatsApp</span>
                    <span class="text-[9px] text-slate-400 block">Sin comisiones extras</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Tabs Section: Descripción, Especificaciones Técnicas, Garantía -->
    <div class="rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm p-6 sm:p-8 space-y-6" x-data="{ tab: 'specs' }">
        <!-- Tab Navigation Header -->
        <div class="flex items-center space-x-6 border-b border-slate-200 dark:border-slate-800 pb-3 text-xs sm:text-sm font-bold">
            <button 
                type="button" 
                @click="tab = 'specs'"
                :class="tab === 'specs' ? 'text-cyan-500 border-b-2 border-cyan-500 pb-3 -mb-3.5' : 'text-slate-400 hover:text-slate-200'"
            >
                Especificaciones Técnicas
            </button>
            <button 
                type="button" 
                @click="tab = 'description'"
                :class="tab === 'description' ? 'text-cyan-500 border-b-2 border-cyan-500 pb-3 -mb-3.5' : 'text-slate-400 hover:text-slate-200'"
            >
                Descripción del Producto
            </button>
            <button 
                type="button" 
                @click="tab = 'warranty'"
                :class="tab === 'warranty' ? 'text-cyan-500 border-b-2 border-cyan-500 pb-3 -mb-3.5' : 'text-slate-400 hover:text-slate-200'"
            >
                Garantía y Entrega
            </button>
        </div>

        <!-- Tab 1: Specs -->
        <div x-show="tab === 'specs'" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @if(!empty($product->technical_specs))
                    @foreach($product->technical_specs as $key => $val)
                        <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800/60 text-xs">
                            <span class="text-slate-400 font-semibold">{{ $key }}:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-100 text-right">{{ $val }}</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-xs text-slate-400">Consultar especificaciones detalladas por WhatsApp.</p>
                @endif
            </div>
        </div>

        <!-- Tab 2: Description -->
        <div x-show="tab === 'description'" class="text-xs sm:text-sm leading-relaxed text-slate-600 dark:text-slate-300 space-y-4">
            {!! nl2br(e($product->description)) !!}
        </div>

        <!-- Tab 3: Warranty -->
        <div x-show="tab === 'warranty'" class="space-y-3 text-xs text-slate-600 dark:text-slate-300">
            <h4 class="font-bold text-sm text-slate-900 dark:text-white">Políticas de Garantía Nexora</h4>
            <p>Todos nuestros productos cuentan con garantía directa de fabricante de 12 meses contra defectos de fábrica.</p>
            <ul class="list-disc pl-5 space-y-1 text-slate-400">
                <li>Conserva tu comprobante o número de pedido para hacer válida la garantía.</li>
                <li>Los despachos a provincias se realizan mediante agencias certificadas con seguimiento en tiempo real.</li>
            </ul>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">
                    Productos Relacionados
                </h3>
                <a href="{{ route('catalog', ['categoria' => $product->category?->slug]) }}" class="text-xs font-bold text-cyan-500 hover:underline">
                    Ver más en {{ $product->category?->name }}
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $rel)
                    @include('components.product-card', ['product' => $rel])
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
