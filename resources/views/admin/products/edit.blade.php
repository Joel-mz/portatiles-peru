@extends('layouts.admin')

@section('title', 'Editar Producto: ' . $product->name)
@section('page_title', 'Editar Producto')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{
    tab: 'info',
    specs: [
        @if(!empty($product->technical_specs))
            @foreach($product->technical_specs as $k => $v)
                { key: '{{ addslashes($k) }}', value: '{{ addslashes($v) }}' },
            @endforeach
        @else
            { key: 'Pantalla', value: '' },
            { key: 'Procesador', value: '' }
        @endif
    ],
    addSpec() {
        this.specs.push({ key: '', value: '' });
    },
    removeSpec(index) {
        this.specs.splice(index, 1);
    }
}">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Editar Producto: {{ $product->name }}
            </h1>
            <p class="text-xs text-slate-500 font-mono">SKU: {{ $product->sku }}</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-slate-400 hover:text-white">
            ← Volver a productos
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="admin-form p-5 sm:p-7 rounded-2xl dark:bg-[#0F172A] dark:border-slate-800/80 shadow-xl space-y-6">
        @csrf
        @method('PUT')

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
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Categoría *</label>
                    <select name="category_id" required class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Marca *</label>
                    <select name="brand_id" required class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ $product->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Modelo</label>
                    <input type="text" name="model" value="{{ old('model', $product->model) }}" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Descripción Corta</label>
                <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>

            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Descripción Completa</label>
                <textarea name="description" rows="5" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="flex flex-wrap items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded text-cyan-500">
                    <span class="font-bold">Producto Destacado en Inicio</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_offer" value="1" {{ $product->is_offer ? 'checked' : '' }} class="rounded text-rose-500">
                    <span class="font-bold text-rose-500">En Oferta</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_new" value="1" {{ $product->is_new ? 'checked' : '' }} class="rounded text-cyan-500">
                    <span class="font-bold text-cyan-500">Badge 'Nuevo'</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded text-emerald-500">
                    <span class="font-bold text-emerald-500">Visible en Tienda</span>
                </label>
            </div>
        </div>

        <!-- Tab 2: Pricing & Stock -->
        <div x-show="tab === 'pricing'" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">SKU Único *</label>
                    <input type="text" name="sku" required value="{{ old('sku', $product->sku) }}" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 uppercase font-mono font-bold text-slate-900 dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Precio Regular (S/) *</label>
                    <input type="number" step="0.01" name="price" required value="{{ old('price', $product->price) }}" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 font-mono font-bold text-slate-900 dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Precio Anterior / Tachado (S/)</label>
                    <input type="number" step="0.01" name="original_price" value="{{ old('original_price', $product->original_price) }}" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 font-mono text-slate-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Precio de Oferta Especial (S/)</label>
                    <input type="number" step="0.01" name="offer_price" value="{{ old('offer_price', $product->offer_price) }}" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 font-mono text-rose-500">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Stock Físico Actual *</label>
                    <input type="number" name="stock" required value="{{ old('stock', $product->stock) }}" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 font-mono font-bold text-emerald-500">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Stock Mínimo de Alerta</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 font-mono text-amber-500">
                </div>
            </div>
        </div>

        <!-- Tab 3: Images -->
        <div x-show="tab === 'images'" class="space-y-4 text-xs">
            <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="w-20 h-20 object-contain rounded-xl bg-white dark:bg-slate-800 border">
                <div>
                    <span class="font-bold text-slate-800 dark:text-white block">Imagen Principal Actual</span>
                    <span class="text-slate-400 text-[10px] break-all">{{ $product->main_image }}</span>
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Cambiar imagen de portada</label>
                <input type="file" name="main_image_file" accept="image/*" class="w-full text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-blue-600 file:text-white file:font-semibold">
            </div>

            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">O URL Externa de Imagen</label>
                <input type="url" name="main_image" value="{{ old('main_image', $product->main_image) }}" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>

            @if($product->images->count() > 0)
                <div class="grid grid-cols-4 sm:grid-cols-6 gap-3 pt-2">
                    @foreach($product->images as $image)
                        <img src="{{ $image->image_path }}" alt="{{ $product->name }}" class="aspect-square rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                    @endforeach
                </div>
            @endif
            <div class="space-y-1 pt-3 border-t border-slate-200 dark:border-slate-800">
                <label class="font-bold text-slate-700 dark:text-slate-300">Agregar imágenes a la galería</label>
                <p class="text-[11px] text-slate-500">Selecciona hasta 8 imágenes adicionales.</p>
                <input type="file" name="gallery_images[]" accept="image/*" multiple class="w-full text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-cyan-600 file:text-white file:font-semibold">
            </div>
        </div>

        <!-- Tab 4: Dynamic Specs -->
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
                Actualizar Producto
            </button>
        </div>

    </form>
</div>
@endsection
