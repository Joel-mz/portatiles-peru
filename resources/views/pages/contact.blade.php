@extends('layouts.app')

@section('title', 'Contacto y Empresa | NEXORA')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">

    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto space-y-2">
        <span class="text-xs font-bold uppercase tracking-widest text-cyan-500">Sobre Nexora Technology</span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
            Tecnología sin límites a tu alcance
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
            Somos especialistas en computadoras, laptops corporativas, periféricos gamers y seguridad electrónica en todo el Perú.
        </p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-bold text-xs flex items-center gap-2">
            <x-icon name="check-circle" class="w-5 h-5 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Company Official Info & Payment accounts (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                    Información de la Empresa
                </h3>

                <div class="space-y-4 text-xs">
                    <div class="flex items-start gap-3">
                        <div class="p-2 rounded-xl bg-blue-500/10 text-blue-500 shrink-0">
                            <x-icon name="box" class="w-4 h-4" />
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white block">{{ $settings->legal_name }}</span>
                            <span class="text-slate-500">RUC: {{ $settings->ruc }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="p-2 rounded-xl bg-cyan-500/10 text-cyan-500 shrink-0">
                            <x-icon name="map-pin" class="w-4 h-4" />
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white block">Sede Central</span>
                            <span class="text-slate-500">{{ $settings->address }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="p-2 rounded-xl bg-emerald-500/10 text-emerald-500 shrink-0">
                            <x-icon name="whatsapp" class="w-4 h-4" />
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white block">WhatsApp y Atención</span>
                            <span class="text-slate-500">{{ $settings->whatsapp_number }} / {{ $settings->phone }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="p-2 rounded-xl bg-purple-500/10 text-purple-500 shrink-0">
                            <x-icon name="clock" class="w-4 h-4" />
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white block">Horarios de Atención</span>
                            <span class="text-slate-500 block">{{ $settings->schedule_weekdays }}</span>
                            <span class="text-slate-500 block">{{ $settings->schedule_weekends }}</span>
                        </div>
                    </div>
                </div>


                <!-- Social Links -->
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-xs font-bold text-slate-400 block uppercase">Síguenos en Redes</span>
                    <div class="flex items-center gap-3">
                        @if($settings->facebook_url)
                            <a href="{{ $settings->facebook_url }}" target="_blank" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-cyan-400 transition font-bold text-xs">Facebook</a>
                        @endif
                        @if($settings->instagram_url)
                            <a href="{{ $settings->instagram_url }}" target="_blank" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-cyan-400 transition font-bold text-xs">Instagram</a>
                        @endif
                        @if($settings->tiktok_url)
                            <a href="{{ $settings->tiktok_url }}" target="_blank" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-cyan-400 transition font-bold text-xs">TikTok</a>
                        @endif
                    </div>
                </div>

            </div>

        </div>

        <!-- Right: Contact Form (7 cols) -->
        <div class="lg:col-span-7">
            <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                    Envíanos un Mensaje
                </h3>

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700 dark:text-slate-300">Tu Nombre *</label>
                            <input type="text" name="name" required placeholder="Tu nombre" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700 dark:text-slate-300">Correo Electrónico *</label>
                            <input type="email" name="email" required placeholder="tu@correo.com" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700 dark:text-slate-300">Teléfono / WhatsApp *</label>
                            <input type="tel" name="phone" required placeholder="+51 987 654 321" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700 dark:text-slate-300">Asunto *</label>
                            <input type="text" name="subject" required placeholder="Consulta sobre producto, cotización..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-700 dark:text-slate-300">Mensaje Detallado *</label>
                        <textarea name="message" rows="5" required placeholder="Escribe aquí tu consulta..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-500/20 transition">
                        Enviar Mensaje
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
