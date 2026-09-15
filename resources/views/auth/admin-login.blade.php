<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Acceso administrativo | NEXORA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-sans antialiased flex items-center justify-center p-4 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_15%_20%,rgba(14,165,233,.35),transparent_32%),radial-gradient(circle_at_85%_80%,rgba(16,185,129,.22),transparent_28%),linear-gradient(135deg,#020617,#0f172a_48%,#082f49)]"></div>
    <div class="absolute -left-24 top-20 h-72 w-72 rounded-full border border-cyan-300/15 bg-cyan-400/10 blur-2xl"></div>
    <div class="absolute -right-24 bottom-12 h-80 w-80 rounded-full border border-emerald-300/15 bg-emerald-400/10 blur-2xl"></div>
    <main class="w-full max-w-md relative z-10">
        <a href="{{ route('home') }}" class="mb-8 flex items-center justify-center gap-2.5 text-white group" aria-label="Volver a la tienda">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-tr from-blue-600 via-cyan-500 to-emerald-400 font-black text-xl shadow-lg shadow-cyan-500/20">N</span>
            <span class="font-black text-2xl tracking-wider">NEXORA</span>
        </a>

        <section class="rounded-3xl border border-white/15 bg-slate-900/80 backdrop-blur-xl p-7 shadow-2xl shadow-blue-950/50 sm:p-9">
            <div class="mb-7 text-center">
                <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-500/10 text-cyan-400">
                    <x-icon name="lock" class="h-6 w-6" />
                </span>
                <h1 class="text-xl font-black">Acceso administrativo</h1>
                <p class="mt-1 text-sm text-slate-400">Ingresa con tus credenciales autorizadas.</p>
                <a href="{{ route('home') }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-cyan-400 hover:text-cyan-300">← Volver a la tienda</a>
            </div>

            <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="mb-1.5 block text-xs font-bold text-slate-300">Correo electrónico</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3.5 py-3 text-sm text-white outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20">
                    @error('email')<p class="mt-1.5 text-xs font-medium text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="mb-1.5 block text-xs font-bold text-slate-300">Contraseña</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full rounded-xl border border-slate-700 bg-slate-800 px-3.5 py-3 text-sm text-white outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20">
                </div>
                <label class="flex items-center gap-2 text-xs text-slate-400">
                    <input name="remember" type="checkbox" value="1" class="rounded border-slate-600 bg-slate-800 text-cyan-500 focus:ring-cyan-500">
                    Mantener sesión iniciada en este equipo
                </label>
                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 px-4 py-3 font-bold text-sm text-white shadow-lg shadow-blue-500/25 transition hover:from-blue-500 hover:to-cyan-400">Ingresar al panel</button>
            </form>
        </section>
    </main>
</body>
</html>
