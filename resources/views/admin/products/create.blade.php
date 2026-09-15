@extends('layouts.admin')

@section('title', 'Crear Nuevo Producto')
@section('page_title', 'Nuevo Producto')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{
    tab: 'info',
    specs: [
        { key: 'Pantalla', value: '' },
        { key: 'Procesador', value: '' },
        { key: 'Memoria RAM', value: '' },
        { key: 'Almacenamiento', value: '' }
    ],
    addSpec() {
        this.specs.push({ key: '', value: '' });
    },
    removeSpec(index) {
        this.specs.splice(index, 1);
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Agregar Nuevo Producto
            </h1>
            <p class="text-xs text-slate-500">Ingresa los datos del producto, fotos y ficha técnica.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-slate-400 hover:text-white">
            ← Volver a productos
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-500 text-xs font-bold space-y-1">
            <span class="block">Por favor corrige los errores señalados:</span>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="admin-form p-5 sm:p-7 rounded-2xl dark:bg-[#0F172A] dark:border-slate-800/80 shadow-xl space-y-6">
        @csrf

        <!-- Tabs Navigation -->
        <div class="flex items-center space-x-6 border-b border-slate-200 dark:border-slate-800 pb-3 text-xs font-bold">
            <button type="button" @click="tab = 'info'" class="admin-tab" :class="tab === 'info' ? 'text-blue-600 bg-blue-50 border-b-2 border-blue-600 -mb-3.5' : 'text-slate-400'">
                Información General
            </button>
            <button type="button" @click="tab = 'pricing'" class="admin-tab" :class="tab === 'pricing' ? 'text-blue-600 bg-blue-50 border-b-2 border-blue-600 -mb-3.5' : 'text-slate-400'">
                Precios y Stock
            </button>
            <button type="button" @click="tab = 'images'" class="admin-tab" :class="tab === 'images' ? 'text-blue-600 bg-blue-50 border-b-2 border-blue-600 -mb-3.5' : 'text-slate-400'">
                Imágenes y Galería
            </button>
            <button type="button" @click="tab = 'specs'" class="admin-tab" :class="tab === 'specs' ? 'text-blue-600 bg-blue-50 border-b-2 border-blue-600 -mb-3.5' : 'text-slate-400'">
                Especificaciones Técnicas
            </button>
        </div>

        <!-- Tab 1: Info -->
        <div x-show="tab === 'info'" class="space-y-4 text-xs">
            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Nombre del Producto *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ej. Laptop Lenovo IdeaPad 3 15.6''" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Categoría *</label>
                    <select name="category_id" required class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                        <option value="">Seleccionar...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Marca *</label>
                    <select name="brand_id" required class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                        <option value="">Seleccionar...</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Modelo</label>
                    <input type="text" name="model" value="{{ old('model') }}" placeholder="Ej. 15IAU7" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Descripción Corta</label>
                <input type="text" name="short_description" value="{{ old('short_description') }}" placeholder="Resumen clave para tarjetas de catálogo" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>

            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Descripción Completa</label>
                <textarea name="description" rows="5" placeholder="Detalles de garantía, características, puertos y rendimiento" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">{{ old('description') }}</textarea>
            </div>

            <div class="flex flex-wrap items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded text-cyan-500">
                    <span class="font-bold">Producto Destacado en Inicio</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_offer" value="1" class="rounded text-rose-500">
                    <span class="font-bold text-rose-500">En Oferta</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_new" value="1" checked class="rounded text-cyan-500">
                    <span class="font-bold text-cyan-500">Badge 'Nuevo'</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-emerald-500">
                    <span class="font-bold text-emerald-500">Visible en Tienda</span>
                </label>
            </div>
        </div>

        <!-- Tab 2: Pricing & Stock -->
        <div x-show="tab === 'pricing'" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">SKU Único *</label>
                    <input type="text" name="sku" required value="{{ old('sku') }}" placeholder="LAP-LEN-001" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 uppercase font-mono font-bold text-slate-900 dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Precio Regular (S/) *</label>
                    <input type="number" step="0.01" name="price" required value="{{ old('price') }}" placeholder="2499.00" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 font-mono font-bold text-slate-900 dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Precio Anterior / Tachado (S/)</label>
                    <input type="number" step="0.01" name="original_price" value="{{ old('original_price') }}" placeholder="2999.00" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 font-mono text-slate-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Precio de Oferta Especial (S/)</label>
                    <input type="number" step="0.01" name="offer_price" value="{{ old('offer_price') }}" placeholder="2399.00" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 font-mono text-rose-500">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Stock Físico Actual *</label>
                    <input type="number" name="stock" required value="{{ old('stock', 10) }}" placeholder="10" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 font-mono font-bold text-emerald-500">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Stock Mínimo de Alerta</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', 2) }}" placeholder="2" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 font-mono text-amber-500">
                </div>
            </div>
        </div>

        <!-- Tab 3: Cover and gallery images -->
        <div x-show="tab === 'images'" class="space-y-4 text-xs">
            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Imagen de portada</label>
                <p class="text-[11px] text-slate-500">Es la primera imagen que verán los clientes en el catálogo.</p>
                <input type="file" name="main_image_file" accept="image/*" class="w-full text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-blue-600 file:text-white file:font-semibold">
            </div>

            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">O ingresar URL Externa de Imagen</label>
                <input type="url" name="main_image" value="{{ old('main_image') }}" placeholder="https://images.unsplash.com/..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>

            <div class="space-y-1 pt-3 border-t border-slate-200 dark:border-slate-800">
                <label class="font-bold text-slate-700 dark:text-slate-300">Galería de imágenes adicionales</label>
                <p class="text-[11px] text-slate-500">Puedes seleccionar hasta 8 fotos para que el cliente vea más ángulos del producto.</p>
                <input type="file" name="gallery_images[]" accept="image/*" multiple class="w-full text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-cyan-600 file:text-white file:font-semibold">
            </div>
        </div>

        <!-- Tab 4: Dynamic Technical Specs -->
        <div x-show="tab === 'specs'" class="space-y-4 text-xs">
            <div class="flex items-center justify-between">
                <span class="font-bold text-slate-700 dark:text-slate-300">Atributos Clave - Valor</span>
                <button type="button" @click="addSpec()" class="px-3 py-1 rounded-lg bg-cyan-600 text-white font-bold">
                    + Agregar Atributo
                </button>
            </div>

            <div class="space-y-2">
                <template x-for="(spec, index) in specs" :key="index">
                    <div class="flex items-center gap-3">
                        <input type="text" name="specs_keys[]" x-model="spec.key" placeholder="Ej. Procesador" class="w-1/3 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                        <input type="text" name="specs_values[]" x-model="spec.value" placeholder="Ej. Intel Core i5-1235U" class="flex-1 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                        <button type="button" @click="removeSpec(index)" class="p-2 text-slate-400 hover:text-rose-500">
                            <x-icon name="trash" class="w-4 h-4" />
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-500/20 transition">
                Guardar Producto
            </button>
        </div>

    </form>
</div>
@endsection
