@extends('layouts.admin')

@section('title', 'Gestión de Productos')
@section('page_title', 'Productos')

@section('content')
<div class="space-y-6" x-data="{ 
    csvModal: false, 
    selectedProducts: [], 
    selectAll: false,
    toggleAll() {
        this.selectedProducts = this.selectAll ? {{ $products->pluck('id')->toJson() }} : [];
    }
}">
    <!-- Mass Delete Form -->
    <form id="mass-delete-form" action="{{ route('admin.products.massDestroy') }}" method="POST" style="display: none;">
        @csrf
        <template x-for="id in selectedProducts" :key="id">
            <input type="hidden" name="product_ids[]" :value="id">
        </template>
    </form>

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Catálogo de Productos
            </h1>
            <p class="text-xs text-slate-500">Administra el inventario, precios, especificaciones e imágenes de la tienda.</p>
        </div>

        <div class="flex items-center gap-3">
            <button 
                type="button" 
                x-show="selectedProducts.length > 0"
                x-cloak
                @click="if(confirm('¿Estás seguro de eliminar ' + selectedProducts.length + ' productos? Esta acción no se puede deshacer.')) document.getElementById('mass-delete-form').submit()"
                class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg shadow-rose-500/20 transition flex items-center gap-2"
            >
                <x-icon name="trash" class="w-4 h-4" />
                <span x-text="'Eliminar (' + selectedProducts.length + ')'"></span>
            </button>

            <button 
                type="button" 
                @click="csvModal = true"
                class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center gap-2 border border-slate-200 dark:border-slate-700"
            >
                <x-icon name="upload" class="w-4 h-4" />
                <span>Importación Masiva (CSV)</span>
            </button>

            <a 
                href="{{ route('admin.products.create') }}"
                class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-500/20 transition flex items-center gap-2"
            >
                <x-icon name="plus" class="w-4 h-4" />
                <span>Nuevo Producto</span>
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="p-4 rounded-2xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm">
        <form action="{{ route('admin.products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <input 
                type="text" 
                name="q" 
                value="{{ request('q') }}"
                placeholder="Buscar por nombre, SKU, modelo..."
                class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white"
            >

            <select name="category_id" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                <option value="">Todas las Categorías</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="brand_id" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                <option value="">Todas las Marcas</option>
                @foreach($brands as $b)
                    <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>

            <select name="stock_status" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                <option value="">Todo el Inventario</option>
                <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stock Bajo (<= 3)</option>
                <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Agotados (0)</option>
            </select>

            <button type="submit" class="py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">
                Filtrar
            </button>
        </form>
    </div>

    <!-- Products Table -->
    <div class="rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-10">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800">
                        </th>
                        <th class="py-3.5 px-4">Producto</th>
                        <th class="py-3.5 px-4">SKU</th>
                        <th class="py-3.5 px-4">Categoría / Marca</th>
                        <th class="py-3.5 px-4">Precio Normal</th>
                        <th class="py-3.5 px-4">Precio Oferta</th>
                        <th class="py-3.5 px-4">Stock</th>
                        <th class="py-3.5 px-4">Estado</th>
                        <th class="py-3.5 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @foreach($products as $product)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                            <td class="py-3.5 px-4">
                                <input type="checkbox" :value="{{ $product->id }}" x-model="selectedProducts" @change="if(!selectedProducts.includes({{ $product->id }})) selectAll = false" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800">
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="w-10 h-10 object-contain rounded-lg bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shrink-0">
                                    <div class="min-w-0 max-w-xs">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-cyan-400 transition truncate block">
                                            {{ $product->name }}
                                        </a>
                                        <span class="text-[10px] text-slate-400">{{ $product->model }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-500 font-semibold">{{ $product->sku }}</td>
                            <td class="py-3.5 px-4 space-y-0.5">
                                <span class="font-semibold text-slate-800 dark:text-slate-200 block">{{ $product->category?->name }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $product->brand?->name }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                S/ {{ number_format($product->price, 2) }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-rose-500">
                                {{ $product->offer_price ? 'S/ ' . number_format($product->offer_price, 2) : '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($product->stock <= 0)
                                    <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-500 font-mono font-black text-[10px] border border-rose-500/30">
                                        Agotado
                                    </span>
                                @elseif($product->is_low_stock)
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-500 font-mono font-black text-[10px] border border-amber-500/30">
                                        {{ $product->stock }} uds. (Bajo)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 font-mono font-bold text-[10px]">
                                        {{ $product->stock }} uds.
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($product->is_active)
                                    <span class="text-emerald-500 font-bold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                    </span>
                                @else
                                    <span class="text-slate-500 font-semibold">Inactivo</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="p-1.5 text-blue-500 hover:text-blue-400 transition" title="Editar">
                                        <x-icon name="edit" class="w-4 h-4" />
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('¿Seguro de eliminar este producto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-500 transition" title="Eliminar">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $products->links() }}
        </div>
    </div>

    <!-- CSV Import Modal -->
    <div 
        x-show="csvModal" 
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    >
        <div class="w-full max-w-md bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-2xl space-y-4" @click.away="csvModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-sm dark:text-white">Carga Masiva de Productos (CSV)</h3>
                <button @click="csvModal = false" class="text-slate-400 hover:text-white">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>
            
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Selecciona un archivo CSV delimitado por comas con las columnas: <code>Nombre, SKU, Precio, Stock</code>.
            </p>

            <form action="{{ route('admin.products.importCsv') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <a href="{{ route('admin.products.downloadCsvTemplate') }}" class="block text-center py-2.5 rounded-xl border border-cyan-500/40 text-cyan-500 font-bold text-xs hover:bg-cyan-500/10">
                    Descargar plantilla CSV
                </a>
                <input type="file" name="csv_file" accept=".csv,text/csv" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition">
                    Subir y Procesar Archivo
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
