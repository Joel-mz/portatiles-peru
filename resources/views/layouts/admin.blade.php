<!DOCTYPE html>
<html lang="es" x-data :class="{ 'dark': $store.theme.dark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | NEXORA Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('nexora_theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-[#f6f9ff] dark:bg-[#070B14] text-slate-800 dark:text-slate-100 font-sans antialiased min-h-screen flex selection:bg-cyan-500 selection:text-black">

    @php
        $settings = \App\Models\CompanySetting::current();
        $pendingReviewsCount = \App\Models\Order::where('payment_status', 'pending_review')->count();
        $lowStockCount = \App\Models\Product::where('stock', '<=', 3)->where('is_active', true)->count();
    @endphp

    <!-- Sidebar Container -->
    <div 
        x-data="{ sidebarOpen: false }"
        class="flex-shrink-0"
    >
        <!-- Mobile backdrop -->
        <div 
            x-show="sidebarOpen" 
            x-cloak
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"
        ></div>

        <!-- Sidebar element -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white dark:bg-[#0B1120] border-r border-slate-200 dark:border-slate-800/80 flex flex-col justify-between transition-transform duration-300 ease-in-out shadow-xl lg:shadow-none"
        >
            <div class="space-y-6">
                <!-- Brand header -->
                <div class="p-6 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-cyan-500 to-emerald-400 p-0.5 shadow-md shadow-cyan-500/20">
                            <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500 text-lg">
                                N
                            </div>
                        </div>
                        <div>
                            <span class="font-black text-sm tracking-wider text-slate-900 dark:text-white uppercase block">
                                {{ $settings->name }}
                            </span>
                            <span class="text-[10px] font-mono text-cyan-500 font-bold uppercase tracking-widest block">
                                SaaS Panel
                            </span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Navigation links -->
                <nav class="px-4 space-y-1 text-xs font-semibold">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 dark:text-slate-500 px-3 block mb-2 tracking-wider">
                        Principal
                    </span>
                    <a 
                        href="{{ route('admin.dashboard') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        <x-icon name="grid" class="w-4 h-4" />
                        <span>Dashboard KPIs</span>
                    </a>

                    <span class="text-[10px] font-extrabold uppercase text-slate-400 dark:text-slate-500 px-3 block pt-4 mb-2 tracking-wider">
                        Catálogo
                    </span>
                    <a 
                        href="{{ route('admin.products.index') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.products.*') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        <x-icon name="laptop" class="w-4 h-4" />
                        <span>Productos</span>
                    </a>
                    <a href="{{ route('admin.catalog.lookup', 'categories') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->is('admin/catalogo/categories') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"><x-icon name="grid" class="w-4 h-4" /><span>Categorías</span></a>
                    <a href="{{ route('admin.catalog.lookup', 'subcategories') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->is('admin/catalogo/subcategories') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"><x-icon name="list" class="w-4 h-4" /><span>Subcategorías</span></a>
                    <a href="{{ route('admin.catalog.lookup', 'brands') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->is('admin/catalogo/brands') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"><x-icon name="tag" class="w-4 h-4" /><span>Marcas</span></a>
                    <a href="{{ route('admin.products.index', ['offers' => 1]) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request('offers') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"><x-icon name="tag" class="w-4 h-4" /><span>Ofertas / Promociones</span></a>
                    <a href="{{ route('admin.catalog.lookup', 'combos') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->is('admin/catalogo/combos') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"><x-icon name="box" class="w-4 h-4" /><span>Combos</span></a>
                    <a href="{{ route('admin.catalog.lookup', 'banners') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->is('admin/catalogo/banners') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"><x-icon name="desktop" class="w-4 h-4" /><span>Banners / Slider</span></a>

                    <span class="text-[10px] font-extrabold uppercase text-slate-400 dark:text-slate-500 px-3 block pt-4 mb-2 tracking-wider">
                        Comercial & Pagos
                    </span>
                    <a 
                        href="{{ route('admin.orders.index') }}" 
                        class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.orders.*') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        <div class="flex items-center gap-3">
                            <x-icon name="shopping-bag" class="w-4 h-4" />
                            <span>Pedidos & Pagos</span>
                        </div>
                        @if($pendingReviewsCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] animate-pulse">
                                {{ $pendingReviewsCount }}
                            </span>
                        @endif
                    </a>
                    <a 
                        href="{{ route('admin.quotes.index') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.quotes.*') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        <x-icon name="box" class="w-4 h-4" />
                        <span>Cotizaciones</span>
                    </a>
                    <a href="{{ route('admin.catalog.lookup', 'clients') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white"><x-icon name="user" class="w-4 h-4" /><span>Clientes</span></a>

                    <span class="text-[10px] font-extrabold uppercase text-slate-400 dark:text-slate-500 px-3 block pt-4 mb-2 tracking-wider">
                        Inventario
                    </span>
                    <a 
                        href="{{ route('admin.inventory.index') }}" 
                        class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.inventory.*') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        <div class="flex items-center gap-3">
                            <x-icon name="refresh-cw" class="w-4 h-4" />
                            <span>Kardex & Movimientos</span>
                        </div>
                        @if($lowStockCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/40 text-[10px] font-mono">
                                {{ $lowStockCount }}
                            </span>
                        @endif
                    </a>

                    <span class="text-[10px] font-extrabold uppercase text-slate-400 dark:text-slate-500 px-3 block pt-4 mb-2 tracking-wider">
                        Ajustes
                    </span>
                    <a 
                        href="{{ route('admin.settings.index') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.settings.*') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        <x-icon name="edit" class="w-4 h-4" />
                        <span>Configuración Empresa</span>
                    </a>
                    <a href="{{ route('admin.settings.index', ['tab' => 'whatsapp']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white"><x-icon name="whatsapp" class="w-4 h-4" /><span>Asesorías WhatsApp</span></a>
                    <a href="{{ route('admin.catalog.lookup', 'users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white"><x-icon name="user" class="w-4 h-4" /><span>Usuarios</span></a>
                    <a href="{{ route('admin.catalog.lookup', 'roles') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white"><x-icon name="shield" class="w-4 h-4" /><span>Roles y permisos</span></a>
                    @if(auth()->user()?->role === 'superadmin')
                        <a href="{{ route('admin.backups.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.backups.*') ? 'bg-cyan-500/10 text-cyan-500 font-bold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"><x-icon name="download" class="w-4 h-4" /><span>Copias de seguridad</span></a>
                    @endif
                </nav>
            </div>

            <!-- User profile widget -->
            <div class="p-4 border-t border-slate-100 dark:border-slate-800/80 m-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        {{ substr(auth()->user()?->name ?? 'Admin', 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <span class="font-bold text-xs text-slate-900 dark:text-white truncate block">
                            {{ auth()->user()?->name ?? 'Admin Nexora' }}
                        </span>
                        <span class="text-[10px] text-cyan-500 font-mono font-semibold block uppercase">
                            {{ auth()->user()?->role ?? 'SuperAdmin' }}
                        </span>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-500/10 hover:text-rose-500" title="Cerrar sesión">
                        <span class="text-xs font-bold">Salir</span>
                    </button>
                </form>
            </div>
        </aside>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- Admin Header -->
        <header class="h-16 bg-white/90 dark:bg-[#0B1120]/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/80 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button 
                    @click="sidebarOpen = true"
                    class="lg:hidden p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200"
                >
                    <x-icon name="menu" class="w-5 h-5" />
                </button>
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400">
                    <span>Panel de Administración</span>
                    <span>/</span>
                    <span class="font-bold text-slate-700 dark:text-slate-200 capitalize">@yield('page_title', 'Dashboard')</span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                
                <!-- Dark / Light mode -->
                <button 
                    type="button" 
                    @click="$store.theme.toggle()"
                    class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition"
                    title="Alternar modo"
                >
                    <span x-show="$store.theme.dark"><x-icon name="sun" class="w-4 h-4 text-amber-400" /></span>
                    <span x-show="!$store.theme.dark"><x-icon name="moon" class="w-4 h-4 text-slate-700" /></span>
                </button>

                <!-- View Storefront Link -->
                <a 
                    href="{{ route('home') }}" 
                    target="_blank"
                    class="px-3.5 py-2 rounded-xl bg-blue-600/10 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-500/30 transition text-xs font-bold flex items-center gap-1.5"
                >
                    <span>Ver Tienda Pública</span>
                    <x-icon name="external-link" class="w-3.5 h-3.5" />
                </a>

            </div>
        </header>

        <!-- Flash messages -->
        <div class="px-4 sm:px-8 pt-6">
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold flex items-center gap-2 shadow-sm mb-4">
                    <x-icon name="check-circle" class="w-4 h-4 shrink-0" />
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 text-xs font-bold flex items-center gap-2 shadow-sm mb-4">
                    <x-icon name="clock" class="w-4 h-4 shrink-0" />
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs font-bold flex items-center gap-2 shadow-sm mb-4">
                    <x-icon name="x-circle" class="w-4 h-4 shrink-0" />
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>

        <!-- Admin View Slot -->
        <main class="flex-1 p-4 sm:p-8">
            @yield('content')
        </main>

    </div>

</body>
</html>
