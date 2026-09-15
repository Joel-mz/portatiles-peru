@extends('layouts.app')

@section('title', 'Solicitud de Cotización Formal | NEXORA')

@section('content')
<div 
    class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8"
    x-data="{
        items: [
            @if($selectedProductId)
                @php $selProd = $products->firstWhere('id', $selectedProductId); @endphp
                @if($selProd)
                    { product_id: {{ $selProd->id }}, product_name: '{{ addslashes($selProd->name) }}', product_sku: '{{ $selProd->sku }}', quantity: 1, unit_price: {{ $selProd->effective_price }} }
                @endif
            @else
                { product_id: {{ $products->first()->id ?? 'null' }}, product_name: '{{ addslashes($products->first()->name ?? '') }}', product_sku: '{{ $products->first()->sku ?? '' }}', quantity: 1, unit_price: {{ $products->first()->effective_price ?? 0 }} }
            @endif
        ],
        availableProducts: {{ Js::from($products) }},
        addItem() {
            const first = this.availableProducts[0];
            this.items.push({
                product_id: first.id,
                product_name: first.name,
                product_sku: first.sku,
                quantity: 1,
                unit_price: parseFloat(first.price)
            });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        onProductChange(index, event) {
            const pId = parseInt(event.target.value);
            const found = this.availableProducts.find(p => p.id === pId);
            if (found) {
                this.items[index].product_id = found.id;
                this.items[index].product_name = found.name;
                this.items[index].product_sku = found.sku;
                this.items[index].unit_price = parseFloat(found.price);
            }
        },
        get totalEstimate() {
            return this.items.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
        }
    }"
>
    <!-- Header -->
    <div class="border-b border-slate-200 dark:border-slate-800 pb-6">
        <span class="text-xs font-bold uppercase tracking-widest text-cyan-500">Ventas Corporativas & Clientes</span>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
            Solicitar Cotización Formal
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Obtén una propuesta comercial con correlativo oficial (COT-XXXXXX) para personas naturales o empresas con RUC.
        </p>
    </div>

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-500 text-xs font-bold">
            {{ session('error') }}
        </div>
    @endif

    <form 
        action="{{ route('quotes.store') }}" 
        method="POST"
        @submit="$refs.itemsJsonInput.value = JSON.stringify(items)"
        class="space-y-8"
    >
        @csrf
        <input type="hidden" name="items_json" x-ref="itemsJsonInput">

        <!-- 1. Customer & Company Data -->
        <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm space-y-5">
            <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                1. Datos del Solicitante / Empresa
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Nombre Completo *</label>
                    <input type="text" name="customer_name" required value="{{ old('customer_name') }}" placeholder="Ej. Roberto Silva" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Correo Electrónico *</label>
                    <input type="email" name="customer_email" required value="{{ old('customer_email') }}" placeholder="roberto@empresa.com" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Teléfono / WhatsApp *</label>
                    <input type="tel" name="customer_phone" required value="{{ old('customer_phone') }}" placeholder="+51 987 654 321" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-700 dark:text-slate-300">Razón Social / Empresa (Opcional)</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="Ej. Silva Asociados S.A.C." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="font-bold text-slate-700 dark:text-slate-300">RUC de la Empresa (Opcional)</label>
                    <input type="text" name="company_ruc" value="{{ old('company_ruc') }}" placeholder="20123456789" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                </div>
            </div>
        </div>

        <!-- 2. Products to Quote -->
        <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0F172A] border border-slate-200 dark:border-slate-800/80 shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    2. Equipos y Cantidades a Cotizar
                </h3>
                <button 
                    type="button" 
                    @click="addItem()"
                    class="px-3 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs flex items-center gap-1 transition shadow-sm"
                >
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    <span>Agregar otro equipo</span>
                </button>
            </div>

            <!-- Items Table Loop -->
            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center gap-3 text-xs">
                        
                        <!-- Select Product -->
                        <div class="flex-1 w-full space-y-1">
                            <label class="font-bold text-slate-500 block">Producto:</label>
                            <select 
                                :value="item.product_id"
                                @change="onProductChange(index, $event)"
                                class="w-full p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white"
                            >
                                <template x-for="prod in availableProducts" :key="prod.id">
                                    <option :value="prod.id" x-text="prod.name + ' (SKU: ' + prod.sku + ')'"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="w-28 space-y-1">
                            <label class="font-bold text-slate-500 block">Cantidad:</label>
                            <input 
                                type="number" 
                                min="1" 
                                x-model.number="item.quantity"
                                class="w-full p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 font-mono font-bold text-center"
                            >
                        </div>

                        <!-- Estimated Line Total -->
                        <div class="w-32 space-y-1 text-right">
                            <label class="font-bold text-slate-500 block">Estimado:</label>
                            <span class="font-mono font-black text-cyan-500 text-sm block pt-2" x-text="'S/ ' + (item.unit_price * item.quantity).toFixed(2)"></span>
                        </div>

                        <!-- Delete button -->
                        <div class="pt-4 sm:pt-6">
                            <button 
                                type="button" 
                                @click="removeItem(index)" 
                                :disabled="items.length <= 1"
                                class="p-2 text-slate-400 hover:text-rose-500 transition disabled:opacity-30"
                            >
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Message / Notes -->
            <div class="space-y-1 pt-3">
                <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Mensaje o Requerimientos Especiales (Opcional):</label>
                <textarea 
                    name="message" 
                    rows="3" 
                    placeholder="Especificaciones adicionales, requerimiento de despacho, modalidad de pago, etc."
                    class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white"
                ></textarea>
            </div>

            <!-- Estimated Total -->
            <div class="flex items-center justify-between p-4 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 text-xs">
                <span class="font-bold text-cyan-600 dark:text-cyan-400 uppercase tracking-wider">Total Estimado Referencial:</span>
                <span class="text-xl font-black font-mono text-cyan-500" x-text="'S/ ' + totalEstimate.toFixed(2)"></span>
            </div>

        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button 
                type="submit"
                class="w-full py-4 rounded-2xl bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-600 hover:from-blue-500 hover:to-cyan-400 text-white font-extrabold text-sm shadow-xl shadow-cyan-500/25 transition flex items-center justify-center gap-2"
            >
                <x-icon name="box" class="w-5 h-5" />
                <span>Generar Solicitud de Cotización Oficial</span>
            </button>
        </div>

    </form>
</div>
@endsection
