@extends('layouts.admin')

@section('title', 'Configuración del Sistema')
@section('page_title', 'Configuración')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ tab: '{{ request('tab', 'general') }}' }">

    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Configuración del Sistema
        </h1>
        <p class="text-xs text-slate-500">Administra los datos de la empresa y la atención por WhatsApp.</p>
    </div>

    <section class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-5 items-start">
        <aside class="admin-surface rounded-2xl p-5 space-y-5">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-tr from-blue-700 to-cyan-400 text-white flex items-center justify-center text-2xl font-black">N</div>
                <div><h2 class="font-black text-blue-700">{{ $settings->name }}</h2><p class="text-[10px] text-slate-500">RUC: {{ $settings->ruc }}</p></div>
            </div>
            <div class="space-y-3 text-xs text-slate-600">
                <p class="flex gap-2"><x-icon name="map-pin" class="w-4 h-4 text-blue-600 shrink-0" />{{ $settings->address }}</p>
                <p class="flex gap-2"><x-icon name="phone" class="w-4 h-4 text-blue-600 shrink-0" />{{ $settings->phone }}</p>
                <p class="flex gap-2"><x-icon name="mail" class="w-4 h-4 text-blue-600 shrink-0" />{{ $settings->email }}</p>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 p-4 text-white text-xs space-y-1"><strong class="block text-sm">Horario de atención</strong><p>{{ $settings->schedule_weekdays }}</p><p>{{ $settings->schedule_weekends }}</p></div>
        </aside>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="admin-form p-5 sm:p-7 rounded-2xl dark:bg-[#0F172A] dark:border-slate-800/80 shadow-xl space-y-6">
        @csrf
        @method('PUT')

        <!-- Tabs -->
        <div class="flex items-center space-x-6 border-b border-slate-200 dark:border-slate-800 pb-3 text-xs font-bold">
            <button type="button" @click="tab = 'general'" class="admin-tab" :class="tab === 'general' ? 'text-blue-600 bg-blue-50 border-b-2 border-blue-600 -mb-3.5' : 'text-slate-400'">
                Empresa
            </button>
            <button type="button" @click="tab = 'whatsapp'" class="admin-tab" :class="tab === 'whatsapp' ? 'text-blue-600 bg-blue-50 border-b-2 border-blue-600 -mb-3.5' : 'text-slate-400'">
                WhatsApp & Asesoría
            </button>
            <button type="button" @click="tab = 'social'" class="admin-tab" :class="tab === 'social' ? 'text-blue-600 bg-blue-50 border-b-2 border-blue-600 -mb-3.5' : 'text-slate-400'">
                Redes Sociales
            </button>
        </div>

        <!-- Tab 1: Empresa -->
        <div x-show="tab === 'general'" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Nombre Comercial *</label>
                    <input type="text" name="name" value="{{ old('name', $settings->name) }}" required class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Eslogan / Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $settings->tagline) }}" class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Razón Social Legal *</label>
                    <input type="text" name="legal_name" value="{{ old('legal_name', $settings->legal_name) }}" required class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Número de RUC *</label>
                    <input type="text" name="ruc" value="{{ old('ruc', $settings->ruc) }}" required class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Correo Electrónico *</label>
                    <input type="email" name="email" value="{{ old('email', $settings->email) }}" required class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Teléfono Central *</label>
                    <input type="text" name="phone" value="{{ old('phone', $settings->phone) }}" required class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Dirección Sede Central *</label>
                <input type="text" name="address" value="{{ old('address', $settings->address) }}" required class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Horario (Lunes a Sábado)</label>
                    <input type="text" name="schedule_weekdays" value="{{ old('schedule_weekdays', $settings->schedule_weekdays) }}" required class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Horario (Domingos)</label>
                    <input type="text" name="schedule_weekends" value="{{ old('schedule_weekends', $settings->schedule_weekends) }}" required class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Tab 2: WhatsApp -->
        <div x-show="tab === 'whatsapp'" class="space-y-4 text-xs">
            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Número WhatsApp Oficial *</label>
                <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" required placeholder="+51987654321" class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white font-mono">
            </div>

            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Mensaje Predeterminado de Bienvenida</label>
                <textarea name="whatsapp_default_message" rows="3" class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">{{ old('whatsapp_default_message', $settings->whatsapp_default_message) }}</textarea>
            </div>
        </div>

        <!-- Tab 4: Social Links -->
        <div x-show="tab === 'social'" class="space-y-4 text-xs">
            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Facebook URL</label>
                <input type="url" name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>
            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">Instagram URL</label>
                <input type="url" name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}" class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>
            <div class="space-y-1">
                <label class="font-bold text-slate-700 dark:text-slate-300">TikTok URL</label>
                <input type="url" name="tiktok_url" value="{{ old('tiktok_url', $settings->tiktok_url) }}" class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-500/20 transition">
                Guardar Configuración
            </button>
        </div>

    </form>
    </section>
</div>
@endsection
