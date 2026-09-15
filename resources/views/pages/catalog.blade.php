@extends('layouts.app')

@section('title', 'Catálogo de Tecnología | NEXORA')
@section('meta_description', 'Explora nuestro catálogo completo de computadoras, laptops, accesorios y componentes con filtros por marca, precio y disponibilidad.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8" x-data="{ mobileFiltersOpen: false, viewMode: 'grid' }">

    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-6">
        <div>
            <nav class="flex items-center space-x-2 text-xs text-slate-500 mb-2">
                <a href="{{ route('home') }}" class="hover:text-cyan-500">Inicio</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-300 font-semibold">Catálogo</span>
                @if(request('categoria'))
                    <span>/</span>
                    <span class="text-cyan-500 font-bold uppercase">{{ request('categoria') }}</span>
                @endif
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                Catálogo de Tecnología
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Mostrando {{ $products->total() }} productos encontrados
            </p>
        </div>

        <!-- Right Controls (Sorting + View Switch + Mobile filter toggle) -->
        <div class="flex items-center gap-3">
            <!-- Mobile Filter button -->
            <button 
                type="button" 
                @click="mobileFiltersOpen = true"
                class="lg:hidden px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white text-xs font-bold flex items-center gap-2 border border-slate-200 dark:border-slate-700"
            >
                <x-icon name="filter" class="w-4 h-4 text-cyan-500" />
                <span>Filtros</span>
            </button>

            <!-- Sort Form -->
            <form action="{{ route('catalog') }}" method="GET" class="flex items-center gap-2">
                @foreach(request()->except(['orden', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <label for="orden" class="text-xs text-slate-400 hidden sm:inline">Ordenar por:</label>
                <select 
                    name="orden" 
                    id="orden"
                    onchange="this.form.submit()"
                    class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-cyan-500"
                >
                    <option value="destacados" {{ request('orden') === 'destacados' ? 'selected' : '' }}>Destacados</option>
                    <option value="precio_asc" {{ request('orden') === 'precio_asc' ? 'selected' : '' }}>Menor precio</option>
                    <option value="precio_desc" {{ request('orden') === 'precio_desc' ? 'selected' : '' }}>Mayor precio</option>
                    <option value="nuevos" {{ request('orden') === 'nuevos' ? 'selected' : '' }}>Más recientes</option>
                    <option value="rating" {{ request('orden') === 'rating' ? 'selected' : '' }}>Mejor valorados</option>
                </select>
            </form>

            <!-- Grid / List Switcher -->
            <div class="hidden sm:flex items-center p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700/60">
                <button 
                    type="button" 
                    @click="viewMode = 'grid'"
                    :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-700 text-cyan-500 shadow-sm' : 'text-slate-400 hover:text-white'"
                    class="p-1.5 rounded-lg transition"
                    title="Vista en cuadrícula"
                >
                    <x-icon name="grid" class="w-4 h-4" />
                </button>
                <button 
                    type="button" 
                    @click="viewMode = 'list'"
                    :class="viewMode === 'list' ? 'bg-white dark:bg-slate-700 text-cyan-500 shadow-sm' : 'text-slate-400 hover:text-white'"
                    class="p-1.5 rounded-lg transition"
                    title="Vista en lista"
                >
                    <x-icon name="list" class="w-4 h-4" />
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (Sidebar Filters + Products Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar Filters (Desktop) -->
        <aside class="hidden lg:block lg:col-span-1 space-y-6">
            <div class="p-6 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm space-y-6">
                
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <x-icon name="filter" class="w-4 h-4 text-cyan-500" />
                        <span>Filtros</span>
                    </h3>
                    @if(request()->hasAny(['categoria', 'marca', 'min_price', 'max_price', 'stock', 'ofertas', 'combos', 'q']))
                        <a href="{{ route('catalog') }}" class="text-[11px] text-rose-500 hover:underline font-semibold">
                            Limpiar todo
                        </a>
                    @endif
                </div>

                <!-- Filter Form -->
                <form action="{{ route('catalog') }}" method="GET" class="space-y-6">
                    @if(request('orden'))
                        <input type="hidden" name="orden" value="{{ request('orden') }}">
                    @endif

                    <!-- Search Input -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Buscar por palabra</label>
                        <div class="relative">
                            <input 
                                type="text" 
                                name="q" 
                                value="{{ request('q') }}"
                                placeholder="Ej. IdeaPad, RTX, G502..."
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl py-2 pl-9 pr-3 text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:border-cyan-500"
                            >
                            <x-icon name="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-3" />
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="space-y-2.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Categorías</label>
                        <div class="space-y-1 max-h-56 overflow-y-auto pr-1">
                            <a 
                                href="{{ route('catalog', array_merge(request()->except(['categoria', 'page']))) }}"
                                class="flex items-center justify-between py-1.5 px-2.5 rounded-lg text-xs {{ !request('categoria') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                            >
                                <span>Todas las categorías</span>
                                <span class="text-[10px] font-mono opacity-70">{{ $products->total() }}</span>
                            </a>
                            @foreach($categories as $cat)
                                <a 
                                    href="{{ route('catalog', array_merge(request()->except(['page']), ['categoria' => $cat->slug])) }}"
                                    class="flex items-center justify-between py-1.5 px-2.5 rounded-lg text-xs {{ request('categoria') === $cat->slug ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                >
                                    <span>{{ $cat->name }}</span>
                                    <span class="text-[10px] font-mono opacity-70">{{ $cat->products_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Brands -->
                    <div class="space-y-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Marcas</label>
                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                            @foreach($brands as $brand)
                                <a 
                                    href="{{ route('catalog', array_merge(request()->except(['page']), ['marca' => request('marca') === $brand->slug ? null : $brand->slug])) }}"
                                    class="flex items-center justify-between py-1 px-2 rounded text-xs transition {{ request('marca') === $brand->slug ? 'text-cyan-500 font-bold bg-cyan-500/10' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}"
                                >
                                    <span>{{ $brand->name }}</span>
                                    <span class="text-[10px] font-mono text-slate-400">{{ $brand->products_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="space-y-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Rango de Precios (S/)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input 
                                type="number" 
                                name="min_price" 
                                value="{{ request('min_price') }}"
                                placeholder="Mínimo"
                                class="bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-xs font-mono"
                            >
                            <input 
                                type="number" 
                                name="max_price" 
                                value="{{ request('max_price') }}"
                                placeholder="Máximo"
                                class="bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl p-2 text-xs font-mono"
                            >
                        </div>
                    </div>

                    <!-- Switches: Stock & Offers -->
                    <div class="space-y-3 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <label class="flex items-center gap-2.5 cursor-pointer text-slate-700 dark:text-slate-300">
                            <input 
                                type="checkbox" 
                                name="stock" 
                                value="1" 
                                {{ request('stock') ? 'checked' : '' }}
                                class="rounded border-slate-400 text-cyan-500 focus:ring-cyan-400"
                            >
                            <span>Solo productos con stock</span>
                        </label>

                        <label class="flex items-center gap-2.5 cursor-pointer text-rose-500 font-bold">
                            <input 
                                type="checkbox" 
                                name="ofertas" 
                                value="1" 
                                {{ request('ofertas') ? 'checked' : '' }}
                                class="rounded border-rose-400 text-rose-500 focus:ring-rose-400"
                            >
                            <span>Solo productos en oferta</span>
                        </label>

                        <label class="flex items-center gap-2.5 cursor-pointer text-cyan-400 font-bold">
                            <input 
                                type="checkbox" 
                                name="combos" 
                                value="1" 
                                {{ request('combos') ? 'checked' : '' }}
                                class="rounded border-cyan-400 text-cyan-500 focus:ring-cyan-400"
                            >
                            <span>Ver combos tecnológicos</span>
                        </label>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition"
                    >
                        Aplicar Filtros
                    </button>
                </form>

            </div>
        </aside>

        <!-- Products Section -->
        <main class="lg:col-span-3 space-y-6">
            
            @if(request('combos') && $combos)
                <!-- Combos View -->
                <div class="space-y-4">
                    <h3 class="text-lg font-black text-cyan-400 flex items-center gap-2">
                        <x-icon name="box" class="w-5 h-5" />
                        <span>Combos Tecnológicos Armados con Descuento</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($combos as $combo)
                            <div class="p-6 rounded-3xl bg-white dark:bg-[#0F172A] border border-cyan-500/30 shadow-lg flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/20 text-cyan-400 font-bold text-[10px] uppercase">Combo Especial</span>
                                        <span class="text-xl font-black text-cyan-400 font-mono">S/ {{ number_format($combo->price, 2) }}</span>
                                    </div>
                                    <h4 class="text-base font-bold text-slate-900 dark:text-white">{{ $combo->name }}</h4>
                                    <p class="text-xs text-slate-400">{{ $combo->description }}</p>
                                    
                                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-1.5 text-xs">
                                        <span class="font-bold text-slate-500 block text-[11px]">Incluye:</span>
                                        @foreach($combo->items as $item)
                                            <div class="flex items-center justify-between text-slate-300">
                                                <span>• {{ $item->product?->name }}</span>
                                                <span class="font-mono text-slate-500">x{{ $item->quantity }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <a 
                                    href="https://wa.me/{{ app(\App\Services\WhatsAppService::class)->getCleanPhone() }}?text={{ urlencode('¡Hola Nexora! Deseo información y compra del combo: ' . $combo->name . ' (S/ ' . $combo->price . ')') }}" 
                                    target="_blank" 
                                    class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs text-center flex items-center justify-center gap-2 shadow-md transition"
                                >
                                    <x-icon name="whatsapp" class="w-4 h-4" />
                                    <span>Comprar Combo por WhatsApp</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Standard Products Listing -->
            @if($products->isEmpty())
                <div class="text-center py-20 bg-white dark:bg-[#0F172A] rounded-3xl border border-slate-200 dark:border-slate-800 p-8 space-y-4">
                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
                        <x-icon name="search" class="w-8 h-8" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">No encontramos productos con estos filtros</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        Intenta ajustar la búsqueda, eliminar filtros de marca o categoría para ver más opciones.
                    </p>
                    <a href="{{ route('catalog') }}" class="inline-block px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs">
                        Ver todos los productos
                    </a>
                </div>
            @else
                <!-- Grid View -->
                <div 
                    x-show="viewMode === 'grid'"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    @foreach($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>

                <!-- List View -->
                <div 
                    x-show="viewMode === 'list'"
                    class="space-y-4"
                >
                    @foreach($products as $product)
                        <div class="p-4 rounded-2xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center gap-5 shadow-sm hover:border-cyan-500/50 transition">
                            <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="w-28 h-28 object-contain rounded-xl bg-slate-100 dark:bg-slate-900 shrink-0">
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 uppercase font-mono">
                                    <span>{{ $product->brand?->name }}</span>
                                    <span>•</span>
                                    <span>{{ $product->sku }}</span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    <a href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a>
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">{{ $product->short_description }}</p>
                            </div>
                            <div class="text-right sm:border-l sm:border-slate-100 dark:sm:border-slate-800 sm:pl-6 space-y-2 shrink-0">
                                <div class="text-xl font-black font-mono text-cyan-400">{{ $product->formattedPrice() }}</div>
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
                                    class="w-full py-2 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs"
                                >
                                    Agregar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pt-6">
                    {{ $products->links() }}
                </div>
            @endif

        </main>
    </div>

    <!-- Mobile Filter Offcanvas Drawer -->
    <div 
        x-show="mobileFiltersOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-hidden lg:hidden"
    >
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="mobileFiltersOpen = false"></div>
        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-xs bg-white dark:bg-slate-900 p-6 overflow-y-auto space-y-6">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                    <h3 class="text-base font-bold dark:text-white">Filtros</h3>
                    <button @click="mobileFiltersOpen = false" class="text-slate-400 hover:text-white">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                <!-- Form clone for mobile -->
                <form action="{{ route('catalog') }}" method="GET" class="space-y-5">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar..." class="w-full bg-slate-100 dark:bg-slate-800 rounded-xl p-2.5 text-xs">
                    
                    <div>
                        <span class="text-xs font-bold block mb-2 dark:text-white">Categorías</span>
                        @foreach($categories as $cat)
                            <a href="{{ route('catalog', ['categoria' => $cat->slug]) }}" class="block text-xs py-1 text-slate-400 hover:text-white">{{ $cat->name }}</a>
                        @endforeach
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-blue-600 text-white rounded-xl font-bold text-xs">
                        Aplicar
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
