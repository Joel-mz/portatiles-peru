@extends('layouts.admin')
@section('title', 'Copias de seguridad')
@section('page_title', 'Copias de seguridad')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-black dark:text-white">Copias de seguridad</h1>
        <p class="text-xs text-slate-500 mt-1">Descarga o restaura los datos del catálogo, usuarios, pedidos y configuración.</p>
    </div>
    
    @if(session('error'))
        <div class="p-4 bg-red-100 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="p-4 bg-green-100 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-100 text-red-700 rounded-xl text-sm">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="p-6 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800 space-y-4">
        <h2 class="font-bold dark:text-white">Crear respaldo</h2>
        <p class="text-xs text-slate-500">Guarda este archivo en un lugar privado. Incluye información de usuarios y clientes.</p>
        <a href="{{ route('admin.backups.download') }}" class="inline-block px-5 py-3 rounded-xl bg-blue-600 text-white text-xs font-bold">Descargar copia de seguridad</a>
    </div>
    
    <form action="{{ route('admin.backups.restore') }}" method="POST" enctype="multipart/form-data" class="p-6 rounded-3xl bg-rose-500/5 border border-rose-500/30 space-y-4">
        @csrf
        <h2 class="font-bold text-rose-600">Restaurar respaldo</h2>
        <p class="text-xs text-slate-600 dark:text-slate-300">Esta acción elimina y reemplaza los datos actuales con los del archivo. No se puede deshacer.</p>
        <input type="file" name="backup" accept=".json,application/json" required class="block w-full text-xs">
        <label class="flex gap-2 text-xs">
            <input type="checkbox" name="confirmation" value="1" required> Confirmo que deseo reemplazar todos los datos actuales.
        </label>
        <button class="px-5 py-3 rounded-xl bg-rose-600 text-white text-xs font-bold">Restaurar ahora</button>
    </form>
</div>
@endsection
