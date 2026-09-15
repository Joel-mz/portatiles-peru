<!DOCTYPE html>
<html lang="es" x-data :class="{ 'dark': $store.theme.dark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NEXORA | Catálogo Virtual y Tecnología en el Perú')</title>
    <meta name="description" content="@yield('meta_description', 'Encuentra las mejores laptops, computadoras gamer, periféricos, componentes y cámaras de seguridad con garantía oficial y envíos a todo el Perú.')">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine theme check before render -->
    <script>
        if (localStorage.getItem('nexora_theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-[#090D16] text-slate-800 dark:text-slate-100 min-h-screen flex flex-col font-sans antialiased selection:bg-cyan-500 selection:text-black">

    @php
        $settings = \App\Models\CompanySetting::current();
        $whatsappService = app(\App\Services\WhatsAppService::class);
    @endphp

    <!-- Top Announcement Bar -->
    <div class="bg-[#06182f] text-slate-300 text-xs border-b border-blue-400/15 py-1.5 px-4 hidden md:block">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <span class="flex items-center gap-1.5 text-cyan-400 font-medium">
                    <x-icon name="truck" class="w-3.5 h-3.5" /> Envíos seguros a todo el Perú
                </span>
                <span class="flex items-center gap-1.5 text-slate-400">
                    <x-icon name="map-pin" class="w-3.5 h-3.5 text-slate-500" /> {{ $settings->address }}
                </span>
                <span class="flex items-center gap-1.5 text-slate-400">
                    <x-icon name="clock" class="w-3.5 h-3.5 text-slate-500" /> {{ $settings->schedule_weekdays }}
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ $whatsappService->generalContactLink() }}" target="_blank" class="hover:text-emerald-400 transition flex items-center gap-1">
                    <x-icon name="whatsapp" class="w-3.5 h-3.5 text-emerald-400" /> {{ $settings->whatsapp_number }}
                </a>
                <span class="text-slate-700">|</span>
                <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-cyan-400 transition flex items-center gap-1">
                    <x-icon name="lock" class="w-3.5 h-3.5" /> Panel Admin
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-40 bg-[#062044]/95 dark:bg-[#0B1120]/95 backdrop-blur-md border-b border-blue-300/20 transition-colors shadow-lg shadow-blue-950/15">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18 gap-4">
                
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-cyan-500 to-emerald-400 p-0.5 shadow-lg shadow-cyan-500/20 group-hover:shadow-cyan-500/40 transition">
                        <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                            <span class="font-extrabold text-xl tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">N</span>
                        </div>
                    </div>
                    <div>
                        <span class="font-black tracking-wider text-xl uppercase bg-gradient-to-r from-white via-cyan-200 to-blue-300 bg-clip-text text-transparent block leading-tight">
                            {{ $settings->name }}
                        </span>
                        <span class="text-[10px] tracking-widest text-cyan-200/70 font-semibold uppercase block -mt-0.5">
                            {{ $settings->tagline }}
                        </span>
                    </div>
                </a>

                <!-- Search Bar -->
                <div class="flex-1 max-w-xl hidden lg:block">
                    <form action="{{ route('catalog') }}" method="GET" class="relative">
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ request('q') }}"
                            placeholder="Buscar laptops, procesadores, mouse, monitores..." 
                            class="w-full bg-white border border-blue-100 rounded-lg py-2.5 pl-11 pr-24 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-400/60 focus:border-cyan-400 transition shadow-inner"
                        >
                        <x-icon name="search" class="w-4 h-4 text-slate-400 absolute left-4 top-3.5" />
                        <button type="submit" class="absolute right-1.5 top-1.5 px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-xs font-semibold shadow-sm transition">
                            Buscar
                        </button>
                    </form>
                </div>

                <!-- Action Nav -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    
                    <!-- Dark/Light Mode Toggle -->
                    <button 
                        type="button"
                        @click="$store.theme.toggle()" 
                        class="p-2.5 rounded-xl text-slate-200 hover:bg-white/10 border border-transparent transition"
                        title="Cambiar modo oscuro / claro"
                    >
                        <span x-show="$store.theme.dark"><x-icon name="sun" class="w-5 h-5 text-amber-400" /></span>
                        <span x-show="!$store.theme.dark"><x-icon name="moon" class="w-5 h-5 text-slate-700" /></span>
                    </button>

                    <!-- PDF Catalog Link -->
                    <a 
                        href="{{ route('catalog.pdf') }}" 
                        target="_blank"
                        class="hidden md:flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-200 hover:bg-white/10 border border-white/10 transition"
                        title="Descargar Catálogo Completo"
                    >
                        <x-icon name="download" class="w-4 h-4 text-blue-500" />
                        <span>Catálogo PDF</span>
                    </a>

                    @auth
                        <a href="{{ route('account.orders') }}" class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-200 hover:bg-white/10 border border-white/10 transition"><x-icon name="user" class="w-4 h-4" /><span>Mi cuenta</span></a>
                    @else
                        <a href="{{ route('customer.login') }}" class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-200 hover:bg-white/10 border border-white/10 transition"><x-icon name="user" class="w-4 h-4" /><span>Ingresar</span></a>
                    @endauth

                    <!-- Cart Trigger Button -->
                    <button 
                        type="button"
                        @click="$store.cart.isOpen = true"
                        class="relative p-2.5 rounded-xl bg-white/10 text-white hover:bg-white/20 border border-white/10 transition flex items-center gap-2"
                        id="cart-trigger-btn"
                    >
                        <x-icon name="shopping-bag" class="w-5 h-5 text-cyan-500" />
                        <span class="hidden sm:inline font-semibold text-xs">Carrito</span>
                        <span 
                            x-show="$store.cart.count > 0" 
                            x-text="$store.cart.count"
                            class="px-1.5 py-0.5 text-[11px] font-extrabold bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-full min-w-[20px] text-center"
                        ></span>
                    </button>

                    <!-- WhatsApp CTA -->
                    <a 
                        href="{{ $whatsappService->generalContactLink() }}" 
                        target="_blank"
                        class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/20 hover:shadow-emerald-600/30 transition"
                    >
                        <x-icon name="whatsapp" class="w-4 h-4" />
                        <span>WhatsApp</span>
                    </a>

                    <!-- Mobile Menu Trigger -->
                    <div x-data="{ mobileOpen: false }" class="lg:hidden">
                        <button @click="mobileOpen = !mobileOpen" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">
                            <x-icon name="menu" class="w-5 h-5" />
                        </button>

                        <!-- Mobile dropdown -->
                        <div 
                            x-show="mobileOpen" 
                            @click.away="mobileOpen = false"
                            class="absolute top-18 left-0 w-full bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 p-4 shadow-xl z-50 space-y-3"
                            x-transition
                        >
                            <form action="{{ route('catalog') }}" method="GET" class="relative">
                                <input 
                                    type="text" 
                                    name="q" 
                                    placeholder="Buscar productos..." 
                                    class="w-full bg-slate-100 dark:bg-slate-800 rounded-lg p-2.5 text-sm"
                                >
                            </form>
                            <nav class="flex flex-col space-y-2 font-medium text-sm">
                                <a href="{{ route('home') }}" class="py-2 px-3 rounded hover:bg-slate-100 dark:hover:bg-slate-800">Inicio</a>
                                <a href="{{ route('catalog') }}" class="py-2 px-3 rounded hover:bg-slate-100 dark:hover:bg-slate-800">Catálogo Completo</a>
                                <a href="{{ route('catalog', ['ofertas' => 1]) }}" class="py-2 px-3 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-rose-500">Ofertas Especiales</a>
                                <a href="{{ route('catalog', ['combos' => 1]) }}" class="py-2 px-3 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-cyan-400">Combos Pro</a>
                                <a href="{{ route('quotes.create') }}" class="py-2 px-3 rounded hover:bg-slate-100 dark:hover:bg-slate-800">Solicitar Cotización</a>
                                <a href="{{ route('catalog.pdf') }}" target="_blank" class="py-2 px-3 rounded hover:bg-slate-100 dark:hover:bg-slate-800">Descargar PDF</a>
                                <a href="{{ route('admin.dashboard') }}" class="py-2 px-3 rounded bg-blue-600/10 text-blue-400 font-semibold">Acceso Admin</a>
                            </nav>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Categories bar -->
            <div class="hidden lg:flex items-center space-x-7 py-2.5 border-t border-white/10 text-xs font-semibold text-slate-200">
                <a href="{{ route('home') }}" class="hover:text-cyan-400 transition {{ request()->routeIs('home') ? 'text-cyan-400' : '' }}">Inicio</a>
                <a href="{{ route('catalog') }}" class="hover:text-cyan-400 transition {{ request()->routeIs('catalog') && !request('ofertas') && !request('combos') ? 'text-cyan-400' : '' }}">Todos los Productos</a>
                <a href="{{ route('catalog', ['categoria' => 'laptops']) }}" class="hover:text-cyan-400 transition">Laptops</a>
                <a href="{{ route('catalog', ['categoria' => 'pc-de-escritorio']) }}" class="hover:text-cyan-400 transition">PC de Escritorio</a>
                <a href="{{ route('catalog', ['categoria' => 'monitores']) }}" class="hover:text-cyan-400 transition">Monitores</a>
                <a href="{{ route('catalog', ['categoria' => 'componentes']) }}" class="hover:text-cyan-400 transition">Componentes</a>
                <a href="{{ route('catalog', ['categoria' => 'accesorios']) }}" class="hover:text-cyan-400 transition">Accesorios</a>
                <a href="{{ route('catalog', ['ofertas' => 1]) }}" class="text-rose-500 hover:text-rose-400 font-bold transition flex items-center gap-1">
                    <x-icon name="tag" class="w-3.5 h-3.5" /> Ofertas
                </a>
                <a href="{{ route('catalog', ['combos' => 1]) }}" class="text-cyan-400 hover:text-cyan-300 font-bold transition flex items-center gap-1">
                    <x-icon name="box" class="w-3.5 h-3.5" /> Combos
                </a>
                <a href="{{ route('quotes.create') }}" class="hover:text-cyan-400 transition ml-auto">Cotizaciones</a>
                <a href="{{ route('contact') }}" class="hover:text-cyan-400 transition">Nosotros</a>
            </div>
        </div>
    </header>

    <!-- Main View Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Cart Slide-Over Drawer -->
    <div 
        x-show="$store.cart.isOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-hidden" 
        aria-labelledby="slide-over-title" 
        role="dialog" 
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div 
            x-show="$store.cart.isOpen"
            x-transition:enter="ease-in-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in-out duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="$store.cart.isOpen = false"
            class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"
        ></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div 
                x-show="$store.cart.isOpen"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-md bg-white dark:bg-[#0D1526] border-l border-slate-200 dark:border-slate-800 shadow-2xl flex flex-col"
            >
                <!-- Drawer Header -->
                <div class="p-5 border-b border-slate-200 dark:border-slate-800/80 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-icon name="shopping-bag" class="w-5 h-5 text-cyan-500" />
                        <h2 class="text-base font-bold dark:text-white" id="slide-over-title">
                            Tu Carrito (<span x-text="$store.cart.count"></span>)
                        </h2>
                    </div>
                    <button 
                        type="button" 
                        @click="$store.cart.isOpen = false"
                        class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white transition"
                    >
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Items List -->
                <div class="flex-1 overflow-y-auto p-5 space-y-4">
                    <!-- Empty State -->
                    <template x-if="$store.cart.items.length === 0">
                        <div class="text-center py-16 space-y-4">
                            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800/80 text-slate-400 flex items-center justify-center mx-auto">
                                <x-icon name="shopping-cart" class="w-8 h-8" />
                            </div>
                            <h3 class="text-base font-bold text-slate-700 dark:text-slate-200">Tu carrito está vacío</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs mx-auto">
                                Explora nuestras laptops, computadoras y componentes con los mejores precios del mercado.
                            </p>
                            <a 
                                href="{{ route('catalog') }}" 
                                @click="$store.cart.isOpen = false"
                                class="inline-block px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition"
                            >
                                Explorar Catálogo
                            </a>
                        </div>
                    </template>

                    <!-- Items Loop -->
                    <template x-for="item in $store.cart.items" :key="item.id">
                        <div class="flex gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 items-center">
                            <img :src="item.image" :alt="item.name" class="w-16 h-16 object-cover rounded-xl bg-slate-200 dark:bg-slate-800 shrink-0">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" x-text="item.name"></h4>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono block" x-text="'SKU: ' + item.sku"></span>
                                <span class="text-sm font-extrabold text-cyan-600 dark:text-cyan-400" x-text="'S/ ' + (item.price * item.quantity).toFixed(2)"></span>
                                
                                <!-- Quantity controls -->
                                <div class="flex items-center gap-2 mt-1.5">
                                    <div class="flex items-center border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 overflow-hidden">
                                        <button 
                                            type="button" 
                                            @click="$store.cart.updateQuantity(item.id, item.quantity - 1)" 
                                            class="px-2 py-0.5 text-xs text-slate-500 hover:text-white hover:bg-blue-600 transition"
                                        >-</button>
                                        <span class="px-2.5 py-0.5 text-xs font-bold dark:text-white" x-text="item.quantity"></span>
                                        <button 
                                            type="button" 
                                            @click="$store.cart.updateQuantity(item.id, item.quantity + 1)" 
                                            class="px-2 py-0.5 text-xs text-slate-500 hover:text-white hover:bg-blue-600 transition"
                                        >+</button>
                                    </div>
                                    <button 
                                        type="button" 
                                        @click="$store.cart.removeItem(item.id)" 
                                        class="text-slate-400 hover:text-rose-500 transition p-1"
                                        title="Eliminar producto"
                                    >
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Drawer Footer: purchase is completed directly in WhatsApp. -->
                <template x-if="$store.cart.items.length > 0">
                    <div class="p-5 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/40 space-y-3">
                        <div class="space-y-1.5 text-xs">
                            <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                <span>Subtotal:</span>
                                <span class="font-mono font-semibold" x-text="'S/ ' + ($store.cart.subtotal / 1.18).toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                <span>IGV (18% inc.):</span>
                                <span class="font-mono font-semibold" x-text="'S/ ' + ($store.cart.subtotal - ($store.cart.subtotal / 1.18)).toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-base font-extrabold text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-800">
                                <span>Total estimado:</span>
                                <span class="text-cyan-600 dark:text-cyan-400 text-lg font-mono font-black" x-text="'S/ ' + $store.cart.total.toFixed(2)"></span>
                            </div>
                        </div>

                        <!-- WhatsApp Direct Order Button -->
                        <button 
                            type="button" 
                            @click="
                                let msg = '¡Hola Nexora! Deseo comprar los siguientes productos de mi carrito:%0A%0A';
                                $store.cart.items.forEach(i => {
                                    msg += `• ${i.quantity}x ${i.name} (SKU: ${i.sku}) - S/ ${(i.price * i.quantity).toFixed(2)}%0A`;
                                });
                                msg += `%0A*Total:* S/ ${$store.cart.total.toFixed(2)}%0A%0A¿Podrían confirmar disponibilidad para envío?`;
                                window.open('https://wa.me/{{ $whatsappService->getCleanPhone() }}?text=' + msg, '_blank');
                            "
                            class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm text-center shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2"
                        >
                            <x-icon name="whatsapp" class="w-4 h-4" />
                            <span>Continuar compra por WhatsApp</span>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Floating WhatsApp Action Widget -->
    <div 
        x-data="{ openTooltip: false }" 
        class="fixed bottom-6 right-6 z-40 flex flex-col items-end"
    >
        <!-- Popup balloon on hover / click -->
        <div 
            x-show="openTooltip" 
            x-transition
            @click.away="openTooltip = false"
            class="mb-3 w-72 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-2xl space-y-2 text-xs"
        >
            <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold">
                    <x-icon name="whatsapp" class="w-4 h-4" />
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 dark:text-white">Asesoría Nexora</h4>
                    <span class="text-[10px] text-emerald-500 font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> En línea ahora
                    </span>
                </div>
            </div>
            <p class="text-slate-600 dark:text-slate-300">
                ¡Hola! 👋 ¿Necesitas ayuda para elegir tu laptop o cotizar componentes? Escríbenos directamente.
            </p>
            <a 
                href="{{ $whatsappService->generalContactLink() }}" 
                target="_blank"
                class="block w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-center rounded-xl font-bold transition shadow-sm"
            >
                Abrir chat de WhatsApp
            </a>
        </div>

        <!-- Pulse button -->
        <button 
            type="button" 
            @click="openTooltip = !openTooltip"
            class="w-14 h-14 rounded-full bg-gradient-to-tr from-emerald-600 to-emerald-400 text-white flex items-center justify-center shadow-xl shadow-emerald-500/30 hover:scale-105 transition active:scale-95 group relative"
            title="Hablar por WhatsApp"
        >
            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500 border-2 border-white dark:border-slate-900"></span>
            </span>
            <x-icon name="whatsapp" class="w-7 h-7" />
        </button>
    </div>

    <!-- Global Toast Container -->
    <div 
        x-data="{ toasts: [] }" 
        @toast.window="
            const id = Date.now();
            toasts.push({ id, message: $event.detail.message, type: $event.detail.type || 'info' });
            setTimeout(() => toasts = toasts.filter(t => t.id !== id), 3500);
        "
        class="fixed top-20 right-5 z-50 space-y-2 max-w-sm pointer-events-none"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div 
                class="pointer-events-auto p-4 rounded-2xl shadow-xl flex items-center gap-3 text-xs font-semibold backdrop-blur-md border transition-all duration-300"
                :class="{
                    'bg-emerald-950/90 text-emerald-200 border-emerald-500/40': toast.type === 'success',
                    'bg-amber-950/90 text-amber-200 border-amber-500/40': toast.type === 'warning',
                    'bg-rose-950/90 text-rose-200 border-rose-500/40': toast.type === 'error',
                    'bg-slate-900/90 text-cyan-200 border-cyan-500/40': toast.type === 'info',
                }"
            >
                <x-icon name="check-circle" class="w-4 h-4 shrink-0" />
                <span x-text="toast.message" class="flex-1"></span>
            </div>
        </template>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 dark:bg-[#070A12] border-t border-slate-200 dark:border-slate-800 text-slate-400 text-xs mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                
                <!-- Brand Info -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center font-black text-white text-sm">
                            N
                        </div>
                        <span class="font-extrabold text-lg text-white tracking-wider">{{ $settings->name }}</span>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed">
                        Líderes en venta de tecnología, computadoras de alto rendimiento, laptops y periféricos en el Perú. Garantía oficial de fábrica y soporte técnico calificado.
                    </p>
                    <div class="text-[11px] space-y-1 text-slate-500">
                        <p><strong class="text-slate-400">Razón Social:</strong> {{ $settings->legal_name }}</p>
                        <p><strong class="text-slate-400">RUC:</strong> {{ $settings->ruc }}</p>
                    </div>
                </div>

                <!-- Categorías Rápidas -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider">Categorías Top</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('catalog', ['categoria' => 'laptops']) }}" class="hover:text-cyan-400 transition">Laptops de Trabajo y Gamer</a></li>
                        <li><a href="{{ route('catalog', ['categoria' => 'pc-de-escritorio']) }}" class="hover:text-cyan-400 transition">PC de Escritorio & Ensambles</a></li>
                        <li><a href="{{ route('catalog', ['categoria' => 'monitores']) }}" class="hover:text-cyan-400 transition">Monitores IPS 144Hz+</a></li>
                        <li><a href="{{ route('catalog', ['categoria' => 'componentes']) }}" class="hover:text-cyan-400 transition">Tarjetas de Video & Procesadores</a></li>
                        <li><a href="{{ route('catalog', ['categoria' => 'seguridad']) }}" class="hover:text-cyan-400 transition">Cámaras de Seguridad Hikvision</a></li>
                    </ul>
                </div>

                <!-- Atención al Cliente & Horario -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider">Contacto & Horario</h4>
                    <div class="space-y-2 text-xs">
                        <p class="flex items-center gap-2">
                            <x-icon name="map-pin" class="w-4 h-4 text-cyan-400 shrink-0" />
                            <span>{{ $settings->address }}</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <x-icon name="phone" class="w-4 h-4 text-cyan-400 shrink-0" />
                            <span>{{ $settings->phone }}</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <x-icon name="mail" class="w-4 h-4 text-cyan-400 shrink-0" />
                            <span>{{ $settings->email }}</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <x-icon name="clock" class="w-4 h-4 text-cyan-400 shrink-0" />
                            <span>{{ $settings->schedule_weekdays }}</span>
                        </p>
                    </div>
                </div>

            </div>

            <!-- Bottom bar -->
            <div class="pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-4">
                <p>&copy; {{ date('Y') }} {{ $settings->legal_name }}. Todos los derechos reservados.</p>
                <div class="flex items-center space-x-6">
                    <a href="{{ route('catalog.pdf') }}" class="hover:text-cyan-400 transition">Descargar Catálogo PDF</a>
                    <a href="{{ route('quotes.create') }}" class="hover:text-cyan-400 transition">Solicitar Cotización</a>
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-400 transition">Ingreso Personal</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
