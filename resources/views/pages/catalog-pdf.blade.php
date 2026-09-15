<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Oficial de Productos - {{ $settings->name }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-size: 11px !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-6 sm:p-10 font-sans">

    <!-- Floating Action Toolbar for printing -->
    <div class="no-print max-w-5xl mx-auto mb-6 flex items-center justify-between bg-slate-900 text-white p-4 rounded-2xl shadow-xl">
        <div class="flex items-center gap-2">
            <x-icon name="download" class="w-5 h-5 text-cyan-400" />
            <span class="font-bold text-sm">Catálogo General Oficial en PDF</span>
        </div>
        <div class="flex items-center gap-3">
            <button 
                onclick="window.print()" 
                class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md transition flex items-center gap-2"
            >
                <x-icon name="printer" class="w-4 h-4" />
                <span>Imprimir o Guardar en PDF</span>
            </button>
            <a href="{{ route('catalog') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs">
                Volver a la Tienda
            </a>
        </div>
    </div>

    <!-- Printable Document Container -->
    <div class="max-w-5xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-lg border border-slate-200 space-y-8">
        
        <!-- Header / Letterhead -->
        <div class="flex flex-col sm:flex-row items-start justify-between border-b-2 border-blue-600 pb-6 gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-black text-xl flex items-center justify-center">
                        N
                    </div>
                    <div>
                        <h1 class="text-2xl font-black tracking-wider text-slate-900">{{ $settings->name }}</h1>
                        <span class="text-xs text-blue-600 font-bold uppercase tracking-widest">{{ $settings->tagline }}</span>
                    </div>
                </div>
                <div class="text-xs text-slate-500 pt-2 space-y-0.5">
                    <p><strong>Razón Social:</strong> {{ $settings->legal_name }}</p>
                    <p><strong>RUC:</strong> {{ $settings->ruc }}</p>
                    <p><strong>Dirección:</strong> {{ $settings->address }}</p>
                </div>
            </div>

            <div class="text-right text-xs text-slate-500 space-y-1">
                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-extrabold text-[11px] uppercase tracking-wider inline-block">
                    Catálogo de Precios Oficial
                </span>
                <p><strong>Fecha de Emisión:</strong> {{ date('d/m/Y') }}</p>
                <p><strong>WhatsApp Ventas:</strong> {{ $settings->whatsapp_number }}</p>
                <p><strong>Correo:</strong> {{ $settings->email }}</p>
            </div>
        </div>

        <!-- Products Table -->
        <div class="space-y-4">
            <h2 class="text-base font-bold text-slate-900 uppercase tracking-wide">
                Lista de Equipos y Precios Vigentes
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100 border-b border-slate-300 text-slate-700 uppercase font-black tracking-wider">
                            <th class="p-3 w-16">Foto</th>
                            <th class="p-3">Producto / Modelo</th>
                            <th class="p-3 w-28">Categoría</th>
                            <th class="p-3 w-24">Marca</th>
                            <th class="p-3 w-24">Stock</th>
                            <th class="p-3 w-28 text-right">Precio (S/)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($products as $product)
                            <tr class="hover:bg-slate-50">
                                <td class="p-3">
                                    <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="w-12 h-12 object-contain rounded-lg border bg-white">
                                </td>
                                <td class="p-3 space-y-0.5">
                                    <span class="font-bold text-slate-900 block text-sm">{{ $product->name }}</span>
                                    <span class="text-[10px] font-mono text-slate-400">SKU: {{ $product->sku }}</span>
                                    <p class="text-[10px] text-slate-500 line-clamp-1">{{ $product->short_description }}</p>
                                </td>
                                <td class="p-3 text-slate-600 font-semibold">{{ $product->category?->name }}</td>
                                <td class="p-3 text-slate-600 font-semibold">{{ $product->brand?->name }}</td>
                                <td class="p-3">
                                    @if($product->stock > 0)
                                        <span class="text-emerald-600 font-bold">{{ $product->stock }} uds.</span>
                                    @else
                                        <span class="text-slate-400 font-bold">A pedido</span>
                                    @endif
                                </td>
                                <td class="p-3 text-right font-mono font-black text-sm text-slate-900">
                                    {{ $product->formattedPrice() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Contact and ordering banner -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <strong class="text-emerald-700 block font-black text-sm">Compra por WhatsApp:</strong>
                <p class="text-slate-600">Escríbenos: <strong>{{ $settings->whatsapp_number }}</strong></p>
                <p class="text-slate-500">Confirmamos stock y coordinamos tu pedido.</p>
            </div>
            <div>
                <strong class="text-blue-700 block font-black text-sm">Cotizaciones:</strong>
                <p class="text-slate-600">Atención personalizada para empresas.</p>
                <p class="text-slate-500">Solicita una cotización por WhatsApp.</p>
            </div>
            <div>
                <strong class="text-slate-800 block font-black text-sm">Envíos:</strong>
                <p class="text-slate-600">Coordinamos la entrega contigo.</p>
                <p class="text-slate-500">Envíos diarios a nivel nacional</p>
            </div>
        </div>

        <!-- Footer note -->
        <div class="text-center text-[10px] text-slate-400 border-t pt-4">
            Precios referenciales sujetos a variación sin previo aviso. Incluyen I.G.V. oficial de ley.
        </div>

    </div>

</body>
</html>
