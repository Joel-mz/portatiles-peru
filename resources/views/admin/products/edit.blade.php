@extends('layouts.admin')

@section('title', 'Agregar / Editar Producto')
@section('page_title', 'Agregar / Editar Producto')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
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
    <!-- Header -->
    <div class="flex items-center justify-between text-white mb-2">
        <div>
            <h1 class="text-xl font-bold tracking-tight">
                Agregar / Editar Producto
            </h1>
        </div>
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

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="bg-[#FDFBF7] rounded-xl overflow-hidden shadow-xl text-slate-800 text-sm">
        @csrf
        @method('PUT')

        <!-- Tabs Navigation -->
        <div class="flex items-center gap-2 pt-4 px-6 border-b border-[#F0EBE1]">
            <button type="button" @click="tab = 'info'" class="px-5 py-2.5 rounded-t-xl font-semibold transition" :class="tab === 'info' ? 'bg-white text-blue-600 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] border-t border-l border-r border-[#F0EBE1]' : 'text-slate-500 hover:text-slate-700'">
                Información general
            </button>
            <button type="button" @click="tab = 'images'" class="px-5 py-2.5 rounded-t-xl font-semibold transition" :class="tab === 'images' ? 'bg-white text-blue-600 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] border-t border-l border-r border-[#F0EBE1]' : 'text-slate-500 hover:text-slate-700'">
                Galería de imágenes
            </button>
            <button type="button" @click="tab = 'specs'" class="px-5 py-2.5 rounded-t-xl font-semibold transition" :class="tab === 'specs' ? 'bg-white text-blue-600 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] border-t border-l border-r border-[#F0EBE1]' : 'text-slate-500 hover:text-slate-700'">
                Características
            </button>
        </div>

        <div class="p-6 bg-white">
            <!-- Tab 1: Info -->
            <div x-show="tab === 'info'" class="space-y-5">
                
                <!-- Row 1 -->
                <div class="space-y-1.5">
                    <label class="font-medium text-slate-700 text-xs">Nombre del producto <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required placeholder="Laptop Lenovo IdeaPad 3" class="w-full p-2.5 rounded-lg bg-white border border-slate-200 text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                </div>

                <!-- Row 2 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="font-medium text-slate-700 text-xs">Categoría <span class="text-rose-500">*</span></label>
                        <select name="category_id" required class="w-full p-2.5 rounded-lg bg-white border border-slate-200 text-slate-800 focus:border-blue-500 outline-none">
                            <option value="">Seleccionar...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-medium text-slate-700 text-xs">Subcategoría</label>
                        <select name="subcategory_id" class="w-full p-2.5 rounded-lg bg-white border border-slate-200 text-slate-800 focus:border-blue-500 outline-none">
                            <option value="">Laptops 15"</option>
                            <!-- TODO: Llenar subcategorías dinámicamente -->
                        </select>
                        <input type="hidden" name="sku" value="{{ old('sku', $product->sku) }}">
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="font-medium text-slate-700 text-xs">Marca <span class="text-rose-500">*</span></label>
                        <select name="brand_id" required class="w-full p-2.5 rounded-lg bg-white border border-slate-200 text-slate-800 focus:border-blue-500 outline-none">
                            <option value="">Seleccionar...</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->id }}" {{ $product->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="price" value="{{ old('price', $product->price) }}">
                    </div>
                    <div class="space-y-1.5">
                        <label class="font-medium text-slate-700 text-xs">Precio anterior</label>
                        <input type="number" step="0.01" name="original_price" value="{{ old('original_price', $product->original_price) }}" placeholder="2999.00" class="w-full p-2.5 rounded-lg bg-white border border-slate-200 text-slate-800 focus:border-blue-500 outline-none">
                    </div>
                </div>

                <!-- Row 4 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div class="space-y-1.5">
                        <label class="font-medium text-slate-700 text-xs">Stock <span class="text-rose-500">*</span></label>
                        <input type="number" name="stock" required value="{{ old('stock', $product->stock) }}" placeholder="10" class="w-full p-2.5 rounded-lg bg-white border border-slate-200 text-slate-800 focus:border-blue-500 outline-none">
                    </div>
                    <div class="space-y-3 pt-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_offer" value="1" {{ $product->is_offer ? 'checked' : '' }} class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                            <span class="text-slate-700 font-medium text-sm">Producto en oferta</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                            <span class="text-slate-700 font-medium text-sm">Producto destacado</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_new" value="1" {{ $product->is_new ? 'checked' : '' }} class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                            <span class="text-slate-700 font-medium text-sm">Producto nuevo</span>
                        </label>
                        <input type="hidden" name="is_active" value="1">
                    </div>
                </div>

                <!-- Row 5 -->
                <div class="space-y-1.5">
                    <label class="font-medium text-slate-700 text-xs">Descripción <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="3" required placeholder="Laptop ideal para el trabajo y estudio..." class="w-full p-2.5 rounded-lg bg-white border border-slate-200 text-slate-800 focus:border-blue-500 outline-none resize-none">{{ old('description', $product->description) }}</textarea>
                </div>

                <!-- Row 6: Mini Images preview inside info tab as per mockup -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <div>
                        <label class="font-medium text-slate-700 text-xs block mb-2">Imagen principal</label>
                        <div class="flex items-center gap-3">
                            <div class="w-16 h-16 rounded-lg bg-slate-900 flex items-center justify-center overflow-hidden shrink-0 border border-slate-200">
                                @if($product->main_image)
                                    <img src="{{ $product->main_image }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-white text-xs">Img</span>
                                @endif
                            </div>
                            <div class="space-y-2">
                                <div class="flex gap-2">
                                    <label class="px-4 py-1.5 rounded-full border border-blue-200 bg-blue-50 text-blue-600 text-xs font-semibold cursor-pointer hover:bg-blue-100 transition">
                                        Subir imagen
                                        <input type="file" name="main_image_file" accept="image/*" class="hidden">
                                    </label>
                                    <button type="button" class="px-4 py-1.5 rounded-full border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                                        Subir URL
                                    </button>
                                </div>
                                <input type="url" name="main_image" value="{{ old('main_image', $product->main_image) }}" placeholder="URL de imagen" class="w-full text-xs p-1.5 rounded-md border border-slate-200 text-slate-600 outline-none">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="font-medium text-slate-700 text-xs block mb-2">Galería de imágenes</label>
                        <div class="flex items-center gap-2">
                            @foreach($product->images->take(3) as $img)
                            <div class="w-14 h-14 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                                <img src="{{ $img->image_path }}" class="w-full h-full object-cover">
                            </div>
                            @endforeach
                            @for($i = $product->images->count(); $i < 3; $i++)
                            <div class="w-14 h-14 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                                <span class="text-slate-400 text-[10px]">Img {{ $i + 1 }}</span>
                            </div>
                            @endfor
                            <label class="w-14 h-14 rounded-lg border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 hover:text-blue-500 hover:border-blue-400 cursor-pointer transition">
                                <x-icon name="plus" class="w-5 h-5" />
                                <input type="file" name="gallery_images[]" accept="image/*" multiple class="hidden">
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Tab 2: Images (Extended) -->
            <div x-show="tab === 'images'" class="space-y-4" x-cloak>
                <div class="p-8 text-center text-slate-500 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                    <x-icon name="image" class="w-8 h-8 mx-auto mb-2 text-slate-400" />
                    <p class="text-sm font-medium">Las imágenes se han integrado en la pestaña de Información General según el nuevo diseño.</p>
                </div>
            </div>

            <!-- Tab 3: Specs -->
            <div x-show="tab === 'specs'" class="space-y-4" x-cloak>
                <div class="flex items-center justify-between">
                    <span class="font-medium text-slate-700">Atributos Clave - Valor</span>
                    <button type="button" @click="addSpec()" class="px-3 py-1.5 rounded-lg border border-blue-200 text-blue-600 text-xs font-semibold hover:bg-blue-50 transition">
                        + Agregar
                    </button>
                </div>

                <div class="space-y-2">
                    <template x-for="(spec, index) in specs" :key="index">
                        <div class="flex items-center gap-3">
                            <input type="text" name="specs_keys[]" x-model="spec.key" placeholder="Ej. Procesador" class="w-1/3 p-2.5 rounded-lg bg-white border border-slate-200 text-slate-800 focus:border-blue-500 outline-none">
                            <input type="text" name="specs_values[]" x-model="spec.value" placeholder="Ej. Intel Core i5" class="flex-1 p-2.5 rounded-lg bg-white border border-slate-200 text-slate-800 focus:border-blue-500 outline-none">
                            <button type="button" @click="removeSpec(index)" class="p-2 text-slate-400 hover:text-rose-500">
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- Footer Buttons -->
        <div class="p-6 bg-white border-t border-slate-100 flex items-center gap-3">
            <button type="submit" class="px-8 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold shadow-[0_4px_14px_0_rgba(37,99,235,0.39)] hover:shadow-[0_6px_20px_rgba(37,99,235,0.23)] transition">
                Guardar
            </button>
            <a href="{{ route('admin.products.index') }}" class="px-8 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition">
                Cancelar
            </a>
        </div>

    </form>
</div>

<style>
/* Forzar estilos si el tema oscuro global está activo, ya que este diseño es forzosamente claro */
.bg-\[\#FDFBF7\] { background-color: #FDFBF7 !important; }
.bg-white { background-color: #ffffff !important; }
.text-slate-800 { color: #1e293b !important; }
.text-slate-700 { color: #334155 !important; }
.border-slate-200 { border-color: #e2e8f0 !important; }
.border-\[\#F0EBE1\] { border-color: #F0EBE1 !important; }
</style>
@endsection
