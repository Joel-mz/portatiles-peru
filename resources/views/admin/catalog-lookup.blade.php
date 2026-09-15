@extends('layouts.admin')

@section('title', $title)
@section('page_title', $title)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div><h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ $title }}</h1><p class="text-xs text-slate-500 mt-1">{{ $description }}</p></div>
        @if($section === 'coupons')<a href="{{ route('admin.products.index', ['offers' => 1]) }}" class="px-4 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs">Ver productos en oferta</a>@endif
    </div>
    @if(in_array($section, ['categories', 'subcategories', 'brands', 'combos', 'banners', 'coupons', 'users'], true))
        <form action="{{ route('admin.catalog.store', $section) }}" method="POST" class="p-5 rounded-2xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-3 gap-3">
            @csrf
            @if($section === 'subcategories')<select name="category_id" required class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700">@foreach(\App\Models\Category::orderBy('name')->get() as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>@endif
            @if($section === 'banners')<input name="title" required placeholder="Título del banner" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700"><input name="image" required type="url" placeholder="URL de imagen" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700"><input name="link_url" type="url" placeholder="Enlace opcional" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700">
            @elseif($section === 'coupons')<input name="code" required placeholder="Código" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700"><select name="type" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700"><option value="percentage">Porcentaje</option><option value="fixed">Monto fijo</option></select><input name="value" type="number" min="0" step="0.01" required placeholder="Valor" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700">
            @elseif($section === 'users')<input name="name" required placeholder="Nombre" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700"><input name="email" type="email" required placeholder="Correo" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700"><input name="password" type="password" required placeholder="Contraseña (8 caracteres)" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700"><select name="role" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700"><option value="seller">Vendedor</option><option value="editor">Editor</option><option value="admin">Administrador</option><option value="superadmin">Superadministrador</option></select>
            @else<input name="name" required placeholder="Nombre" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700">@if($section === 'categories')<input name="description" placeholder="Descripción opcional" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700">@endif @if($section === 'combos')<input name="price" type="number" min="0" step="0.01" required placeholder="Precio" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700">@endif
            @endif
            <button class="sm:col-span-full px-4 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs">Agregar nuevo</button>
        </form>
    @endif
    <div class="rounded-3xl overflow-hidden bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($records as $record)
                <div class="p-4 flex items-center justify-between gap-4"><div class="min-w-0"><p class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $record->{$label} }}</p><p class="text-xs text-slate-500 mt-1">@if(isset($record->customer_email)) {{ $record->customer_email }} · {{ $record->customer_phone }} · {{ $record->total_orders }} pedido(s) @elseif(isset($record->category)) {{ $record->category?->name }} @elseif(isset($record->email)) {{ $record->email }} · {{ $record->role }} @elseif(isset($record->products_count)) {{ $record->products_count }} productos @elseif(isset($record->items_count)) {{ $record->items_count }} productos incluidos @elseif(isset($record->value)) Valor: {{ $record->value }} @elseif(isset($record->type)) {{ $record->type }} @endif</p></div><div class="flex items-center gap-2"><span class="text-[10px] font-bold px-2 py-1 rounded-full {{ ($record->is_active ?? true) ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-500/10 text-slate-500' }}">{{ ($record->is_active ?? true) ? 'Activo' : 'Inactivo' }}</span>@if(in_array($section, ['categories', 'subcategories', 'brands', 'combos', 'banners', 'coupons', 'users'], true))<form action="{{ route('admin.catalog.destroy', [$section, $record->id]) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?');">@csrf @method('DELETE')<button class="text-rose-500 text-xs font-bold">Eliminar</button></form>@endif</div></div>
            @empty <div class="p-8 text-center text-sm text-slate-500">No hay registros todavía.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
