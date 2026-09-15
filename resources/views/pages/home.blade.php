@extends('layouts.app')

@section('title', 'NEXORA | Catálogo Virtual y Tecnología en el Perú')

@section('content')
<div class="space-y-10 pb-12">

    <!-- Hero Slider Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div 
            x-data="{
                currentSlide: 0,
                slides: {{ Js::from($banners) }},
                autoplayTimer: null,
                next() {
                    this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                },
                prev() {
                    this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
                },
                init() {
                    this.autoplayTimer = setInterval(() => this.next(), 6500);
                }
            }"
            class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-[#061a39] via-[#062b65] to-[#0b4db9] border border-blue-300/25 shadow-xl shadow-blue-950/20 min-h-[310px] md:min-h-[350px] flex items-center"
        >
            <!-- Background Image with Overlay Glow -->
            <template x-for="(slide, index) in slides" :key="slide.id">
                <div 
                    x-show="currentSlide === index"
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 scale-105"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute inset-0"
                >
                    <img :src="slide.image" :alt="slide.title" class="w-full h-full object-cover object-center opacity-55 mix-blend-screen">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#06162f] via-[#061a39]/85 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#041127]/70 via-transparent to-transparent"></div>
                </div>
            </template>

            <!-- Slide Content Overlay -->
            <div class="relative z-10 max-w-xl px-6 sm:px-10 py-9 space-y-5">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-bold tracking-wide uppercase">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                    <span x-text="slides[currentSlide]?.badge || 'Tecnología de Vanguardia'"></span>
                </div>

                <!-- Headline -->
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-[1.05] uppercase">
                    <span x-text="slides[currentSlide]?.title"></span>
                </h1>

                <!-- Subtitle -->
                <p class="text-sm text-slate-200 leading-relaxed max-w-xl" x-text="slides[currentSlide]?.subtitle"></p>

                <!-- Action CTA Buttons -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a 
                        :href="slides[currentSlide]?.link_url || '{{ route('catalog') }}'"
                        class="px-7 py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-600 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-sm shadow-xl shadow-cyan-500/25 transition transform hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-2"
                    >
                        <span x-text="slides[currentSlide]?.button_text || 'Ver Productos'"></span>
                        <x-icon name="chevron-right" class="w-4 h-4" />
                    </a>

                    <a 
                        href="{{ route('quotes.create') }}"
                        class="px-6 py-3.5 rounded-2xl bg-slate-900/80 hover:bg-slate-800 text-slate-200 hover:text-white font-bold text-sm border border-slate-700/80 transition flex items-center gap-2 backdrop-blur-md"
                    >
                        <x-icon name="box" class="w-4 h-4 text-cyan-400" />
                        <span>Cotizar a Medida</span>
                    </a>
                </div>

                <!-- Value Props tags -->
                <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-400 pt-4 border-t border-slate-800/60">
                    <span class="flex items-center gap-1.5 text-slate-300">
                        <x-icon name="truck" class="w-4 h-4 text-cyan-400" /> Envío a todo el Perú
                    </span>
                    <span class="flex items-center gap-1.5 text-slate-300">
                        <x-icon name="shield" class="w-4 h-4 text-emerald-400" /> Garantía de Fábrica
                    </span>
                    <span class="flex items-center gap-1.5 text-slate-300">
                        <x-icon name="whatsapp" class="w-4 h-4 text-emerald-400" /> Compra asistida por WhatsApp
                    </span>
                </div>
            </div>

            <!-- Carousel navigation dots & arrows -->
            <div class="absolute bottom-6 right-6 z-20 flex items-center gap-3">
                <button 
                    type="button" 
                    @click="prev()" 
                    class="p-2.5 rounded-full bg-slate-900/80 hover:bg-slate-800 text-white border border-slate-700/80 backdrop-blur-md transition"
                >
                    <x-icon name="chevron-left" class="w-4 h-4" />
                </button>
                <div class="flex items-center gap-1.5">
                    <template x-for="(slide, idx) in slides" :key="idx">
                        <button 
                            type="button" 
                            @click="currentSlide = idx" 
                            class="h-2 rounded-full transition-all duration-300"
                            :class="currentSlide === idx ? 'w-8 bg-cyan-400' : 'w-2 bg-slate-600 hover:bg-slate-400'"
                        ></button>
                    </template>
                </div>
                <button 
                    type="button" 
                    @click="next()" 
                    class="p-2.5 rounded-full bg-slate-900/80 hover:bg-slate-800 text-white border border-slate-700/80 backdrop-blur-md transition"
                >
                    <x-icon name="chevron-right" class="w-4 h-4" />
                </button>
            </div>
        </div>
    </section>

    <!-- Categories Grid (Matching Mockup with 10 icons) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    Explora por Categorías
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Encuentra equipos garantizados y componentes especializados
                </p>
            </div>
            <a href="{{ route('catalog') }}" class="text-xs font-bold text-cyan-600 dark:text-cyan-400 hover:underline flex items-center gap-1">
                <span>Ver todo el catálogo</span>
                <x-icon name="chevron-right" class="w-3.5 h-3.5" />
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-8 gap-3 sm:gap-4">
            @foreach($categories as $category)
                <a 
                    href="{{ route('catalog', ['categoria' => $category->slug]) }}"
                    class="group p-3 rounded-xl bg-white dark:bg-[#0F172A]/70 hover:bg-blue-50 dark:hover:bg-[#15203B] border border-slate-200 dark:border-slate-800/80 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col items-center text-center space-y-2 transform hover:-translate-y-1"
                >
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-slate-800/90 text-blue-600 dark:text-cyan-400 flex items-center justify-center group-hover:scale-110 transition duration-200 shadow-inner">
                        <x-icon :name="$category->icon ?? 'box'" class="w-6 h-6" />
                    </div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition">
                        {{ $category->name }}
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Featured Products Section (Matching Mockup with live cards) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        Productos Destacados
                    </h2>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Los equipos más solicitados y con mejores valoraciones de clientes
                </p>
            </div>
            <a href="{{ route('catalog') }}" class="text-xs font-bold text-cyan-600 dark:text-cyan-400 hover:underline flex items-center gap-1">
                <span>Ver todos</span>
                <x-icon name="chevron-right" class="w-3.5 h-3.5" />
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $prod)
                @include('components.product-card', ['product' => $prod])
            @endforeach
        </div>
    </section>

    <!-- Promo Banners Section: Ofertas Especiales & Combos Tecnológicos (Matching Mockup) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Banner 1: Ofertas Especiales -->
            <div class="rounded-3xl bg-gradient-to-br from-rose-950/80 via-slate-900 to-[#0F172A] border border-rose-500/20 p-6 sm:p-8 flex flex-col justify-between relative overflow-hidden group shadow-xl">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="space-y-3 relative z-10">
                    <span class="px-2.5 py-1 rounded-full bg-rose-500/20 text-rose-400 font-extrabold text-[10px] tracking-wider uppercase border border-rose-500/30">
                        OFERTAS ESPECIALES
                    </span>
                    <h3 class="text-2xl font-black text-white leading-tight">
                        Hasta <span class="text-rose-400">30% dscto.</span> en accesorios seleccionados
                    </h3>
                    <p class="text-xs text-slate-300">
                        Mouse gamer, teclados mecánicos y audífonos con iluminación RGB.
                    </p>
                </div>
                <div class="pt-6 relative z-10">
                    <a href="{{ route('catalog', ['ofertas' => 1]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg shadow-rose-600/30 transition">
                        <span>Ver ofertas</span>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>

            <!-- Banner 2: Combos Tecnológicos -->
            <div class="rounded-3xl bg-gradient-to-br from-blue-950/80 via-slate-900 to-[#0F172A] border border-blue-500/20 p-6 sm:p-8 flex flex-col justify-between relative overflow-hidden group shadow-xl">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="space-y-3 relative z-10">
                    <span class="px-2.5 py-1 rounded-full bg-blue-500/20 text-blue-400 font-extrabold text-[10px] tracking-wider uppercase border border-blue-500/30">
                        COMBOS TECNOLÓGICOS
                    </span>
                    <h3 class="text-2xl font-black text-white leading-tight">
                        Laptops + Accesorios desde <span class="text-cyan-400">S/ 2,599</span>
                    </h3>
                    <p class="text-xs text-slate-300">
                        Lleva paquetes completos listos para estudiar, trabajar o crear contenido.
                    </p>
                </div>
                <div class="pt-6 relative z-10">
                    <a href="{{ route('catalog', ['combos' => 1]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition">
                        <span>Ver combos</span>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>

            <!-- Banner 3: Envíos y Garantía Perú -->
            <div class="rounded-3xl bg-gradient-to-br from-emerald-950/80 via-slate-900 to-[#0F172A] border border-emerald-500/20 p-6 sm:p-8 flex flex-col justify-between relative overflow-hidden group shadow-xl">
                <div class="space-y-3 relative z-10">
                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] tracking-wider uppercase border border-emerald-500/30">
                        COMPRA 100% SEGURA
                    </span>
                    <h3 class="text-2xl font-black text-white leading-tight">
                        Envíos a todo el Perú & Asesoría Directa
                    </h3>
                    <p class="text-xs text-slate-300">
                        Atención personalizada por WhatsApp para confirmar disponibilidad, entrega y compra.
                    </p>
                </div>
                <div class="pt-6 relative z-10">
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition">
                        <span>Conoce Nexora</span>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- Official Brands Carousel -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="p-6 rounded-3xl bg-white dark:bg-[#0B1120] border border-slate-200 dark:border-slate-800/80 shadow-md">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-extrabold tracking-wider text-slate-400 uppercase">Marcas Oficiales Garantizadas</span>
                <a href="{{ route('catalog') }}" class="text-xs font-semibold text-cyan-500 hover:underline">Ver todas</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-5 md:grid-cols-10 gap-4 items-center justify-items-center">
                @foreach($brands as $brand)
                    <a 
                        href="{{ route('catalog', ['marca' => $brand->slug]) }}"
                        class="p-2 text-center text-xs font-black text-slate-500 dark:text-slate-400 hover:text-cyan-400 tracking-wider transition uppercase"
                        title="{{ $brand->name }}"
                    >
                        {{ $brand->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

</div>
@endsection
