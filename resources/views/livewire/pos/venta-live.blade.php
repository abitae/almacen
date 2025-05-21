<div>
    <x-mary-card title="Punto de Venta (POS)" subtitle="Sistema de ventas rápido y eficiente" shadow separator>
        <x-slot:menu>
            <flux:button icon="printer" wire:click="print">Imprimir</flux:button>
            <flux:button icon="arrow-down-tray" wire:click="export">Exportar</flux:button>
        </x-slot:menu>

        <!-- Filtros de búsqueda -->
        <div class="mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <flux:input wire:model.live.debounce.300ms="searchFiltro" icon="magnifying-glass"
                    placeholder="Buscar por nombre o código..." clearable />
                <x-mary-choices-offline label="" wire:model.live="categoriaFiltro" :options="$categorias"
                    placeholder="Filtrar por categoría" single searchable inline clearable />
                <x-mary-choices-offline label="" wire:model.live="warehouseFiltro" :options="$warehouses"
                    placeholder="Filtrar por bodega" single searchable inline clearable />
                <div class="flex items-center space-x-4">
                    <flux:checkbox wire:model.live="filtroStock" label="stock disponible" />
                    <flux:checkbox wire:model.live="filtroLotes" label="lotes disponibles" />
                </div>

            </div>
        </div>

        <div class="flex gap-4">
            <!-- Carrito de compras (1/6) -->
            <div class="w-1/6">
                
            </div>
            <!-- Grid de productos (4/6) -->
            <div class="w-3/6">
                <div
                    class="grid grid-cols-1 xs:grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-2">
                    @forelse($products as $product)
                        <div wire:key="product-{{ $product->id }}"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-all duration-200 overflow-hidden h-[600px]">
                            <!-- Imagen del producto -->
                            <div class="relative h-48 bg-gray-100 dark:bg-gray-700 overflow-hidden group">
                                @if ($product->images->first())
                                    <img src="{{ $product->images->first()->image_path }}"
                                        alt="{{ $product->commercial_name }}"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <!-- Información del producto -->
                            <div class="p-2 h-full">
                                <div class="space-y-1">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $product->commercial_name }}
                                    </h3>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $product->category->name ?? 'Sin categoría' }}
                                        </span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $product->barcode }}
                                        </span>
                                    </div>

                                    <!-- Información de lotes -->
                                    <div class="mt-2 h-full">
                                        <div class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Lotes disponibles:
                                        </div>
                                        <div class="space-y-2 h-full overflow-y-auto pr-1">
                                            @forelse($product->batches->sortBy('expiration_date') as $lote)
                                                <button class="w-full group" {{  $confirmar_venta ? 'disabled' : '' }}
                                                    wire:click="abrirModalCantidad({{ $lote->id }})">
                                                    <div
                                                        class="relative bg-white dark:bg-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 p-3 border border-gray-100 dark:border-gray-600 group-hover:border-primary-500">
                                                        <div class="flex flex-col gap-2">
                                                            <div class="flex items-center justify-between">
                                                                <span
                                                                    class="font-semibold text-xs text-gray-800 dark:text-gray-200">
                                                                    #{{ $lote->batch_number }}
                                                                </span>

                                                                <span
                                                                    class="bg-green-500 text-white text-xs px-3 py-1 rounded-full font-medium">
                                                                    S/{{ number_format($lote->unit_price, 2) }}
                                                                </span>
                                                            </div>

                                                            <div class="flex flex-col gap-2 text-xs">
                                                                <span
                                                                    class="text-gray-600 dark:text-gray-400 flex items-center bg-gray-50 dark:bg-gray-600 px-2 py-1 rounded">
                                                                    <svg class="w-4 h-4 mr-1 text-primary-500"
                                                                        fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                                    </svg>
                                                                    {{ $lote->quantity }}
                                                                    {{ $lote->product->presentation }}
                                                                </span>
                                                                <span
                                                                    class="text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ $lote->warehouse->name ?? 'Sin almacén' }}
                                                                </span>
                                                                @if ($lote->expiration_date)
                                                                    <span
                                                                        class="text-red-500 flex items-center bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded">
                                                                        <svg class="w-4 h-4 mr-1" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                        </svg>
                                                                        {{ $lote->expiration_date->format('d/m/Y') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </button>
                                            @empty
                                                <div class="text-center py-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                    <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                    </svg>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Sin lotes
                                                        disponibles</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay productos</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    No se encontraron productos que coincidan con tu búsqueda.
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Carrito de compras (1/6) -->
            <div class="w-2/6">
                @if (!$confirmar_venta)
                    <x-mary-card title="Carrito de Compras" class="bg-white dark:bg-gray-800">
                        <div class="space-y-4">
                            <!-- Lista de productos en el carrito -->
                            <div class="space-y-2">
                                @forelse($carrito as $item)
                                    <div
                                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="flex-shrink-0 bg-primary-100 dark:bg-primary-900/20 px-3 py-1 rounded-full">
                                                <span
                                                    class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ $item['cantidad'] }}x</span>
                                            </div>
                                            <div class="space-y-1">
                                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $item['nombre'] }}
                                                </h4>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $item['presentation'] }}
                                                    </span>
                                                    <span class="text-xs text-gray-400">•</span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        Lote: {{ $item['lote_id']->batch_number }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="text-right">
                                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                                    S/{{ number_format($item['subtotal'], 2) }}
                                                </span>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    S/{{ number_format($item['subtotal'] / $item['cantidad'], 2) }} c/u
                                                </p>
                                            </div>
                                            <x-flux::button wire:click="eliminarDelCarrito({{ $item['id'] }})"
                                                class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-full transition-colors">
                                                <x-flux::icon name="trash" class="w-5 h-5" />
                                            </x-flux::button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            El carrito está vacío
                                        </p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Resumen del carrito -->
                            @if (count($carrito) > 0)
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span
                                            class="text-sm font-medium text-gray-700 dark:text-gray-300">Subtotal:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            S/{{ number_format($subtotal, 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">IGV
                                            (18%):</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            S/{{ number_format($iva, 2) }}
                                        </span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center border-t border-gray-200 dark:border-gray-700 pt-2">
                                        <span class="text-base font-bold text-gray-900 dark:text-white">Total:</span>
                                        <span class="text-base font-bold text-gray-900 dark:text-white">
                                            S/{{ number_format($total, 2) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="flex space-x-3 mt-4">
                                    <x-flux::button wire:click="limpiarCarrito" class="flex-1">
                                        Limpiar Carrito
                                    </x-flux::button>
                                    <x-flux::button wire:click="procesarVenta" variant="primary" class="flex-1">
                                        Procesar Venta
                                    </x-flux::button>
                                </div>
                            @endif
                        </div>
                    </x-mary-card>
                @else
                    <x-mary-card title="Confirmar Venta" class="bg-white dark:bg-gray-800">
                        <div class="space-y-6 p-6">

                            <!-- Selección de Cliente -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 mb-6 shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <x-heroicon-o-user class="w-5 h-5" />
                                    Información del Cliente
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <flux:label class="text-sm font-medium">Cliente</flux:label>
                                        <x-mary-choices-offline wire:model.live="cliente_id" :options="$clientes"
                                            option-label="name" option-value="id"
                                            placeholder="Seleccione un cliente"
                                            single searchable inline clearable />
                                    </div>

                                    @if ($cliente_id)
                                        <div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                            <div>
                                                <flux:label class="text-sm text-gray-500">Nombre</flux:label>
                                                <flux:text class="font-medium">{{ $cliente->name }}</flux:text>
                                            </div>
                                            <div>
                                                <flux:label class="text-sm text-gray-500">Documento</flux:label>
                                                <flux:text class="font-medium">{{ $cliente->document_type }} {{ $cliente->document_number }}</flux:text>
                                            </div>
                                            @if ($cliente->address)
                                            <div>
                                                <flux:label class="text-sm text-gray-500">Dirección</flux:label>
                                                <flux:text class="font-medium">{{ $cliente->address }}</flux:text>
                                            </div>
                                            @endif
                                            @if ($cliente->phone)
                                            <div>
                                                <flux:label class="text-sm text-gray-500">Teléfono</flux:label>
                                                <flux:text class="font-medium">{{ $cliente->phone }}</flux:text>
                                            </div>
                                            @endif
                                            @if ($cliente->email)
                                            <div>
                                                <flux:label class="text-sm text-gray-500">Email</flux:label>
                                                <flux:text class="font-medium">{{ $cliente->email }}</flux:text>
                                            </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center py-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <x-heroicon-o-user-circle class="w-12 h-12 mx-auto text-gray-400" />
                                            <flux:text class="text-gray-500 mt-2">No hay cliente seleccionado</flux:text>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Método de Pago y Tipo de Recibo -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                                <div class="grid grid-cols-1 md:grid-cols-1 gap-2">
                                    <!-- Método de Pago -->
                                    <div>
                                        <flux:radio.group label="Método de Pago" wire:model="metodo_pago" variant="segmented" class="space-y-2">
                                            <flux:radio value="1" label="Efectivo" icon="banknotes" class="w-full" />
                                            <flux:radio value="2" label="Tarjeta" icon="credit-card" class="w-full" />
                                            <flux:radio value="3" label="Transferencia" icon="banknotes" class="w-full" />
                                        </flux:radio.group>
                                    </div>

                                    <!-- Tipo de Recibo -->
                                    <div>
                                        <flux:radio.group label="Tipo de Recibo" wire:model="tipo_recibo" variant="segmented" class="space-y-2">
                                            <flux:radio value="1" label="Boleta" icon="document-text" class="w-full" />
                                            <flux:radio value="6" label="Factura" icon="document-text" class="w-full" />
                                            <flux:radio value="8" label="Nota de venta" icon="document-duplicate" class="w-full" />
                                        </flux:radio.group>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón Confirmar -->
                            <div class="flex space-x-3 mt-6">
                                <x-flux::button icon="x-mark" wire:click="$set('confirmar_venta', false)" class="w-full">
                                    Cancelar
                                </x-flux::button>
                                <x-flux::button icon="check" wire:click="confirmarVenta" variant="primary" class="w-full">
                                    Procesar Venta
                                </x-flux::button>
                            </div>
                        </div>
                    </x-mary-card>
                @endif
            </div>
        </div>
    </x-mary-card>

    <!-- Modal de Cantidad -->
    <flux:modal wire:model.self="modal_cantidad" class="max-w-lg">
        <div class="p-6 space-y-6">
            <!-- Encabezado -->
            <div class="text-center">
                <flux:heading size="xl" class="text-primary-600">Agregar al Carrito</flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-gray-400">Seleccione la cantidad a vender</flux:text>
            </div>

            @if ($loteSeleccionado)
                <div class="space-y-6">
                    <!-- Información del Producto -->
                    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                        <div class="flex items-center gap-6">
                            @if ($loteSeleccionado->product->images->first())
                                <div class="relative w-24 h-24">
                                    <img src="{{ $loteSeleccionado->product->images->first()->image_path }}"
                                        alt="{{ $loteSeleccionado->product->commercial_name }}"
                                        class="w-full h-full object-cover rounded-lg shadow-md">
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    {{ $loteSeleccionado->product->commercial_name }}
                                </h3>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Precio:</span>
                                    <span class="text-xl font-bold text-primary-600">
                                        S/{{ number_format($loteSeleccionado->unit_price, 2) }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $loteSeleccionado->product->presentation }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Controles de Cantidad -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <flux:label class="text-lg font-semibold text-gray-700 dark:text-gray-300">Cantidad
                            </flux:label>
                            <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    Stock: {{ $loteSeleccionado->quantity }}
                                    {{ $loteSeleccionado->product->presentation }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-center gap-4 mt-3">
                            <flux:button wire:click="decrementarCantidad" size="sm"
                                class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 12H4" />
                                </svg>
                            </flux:button>

                            <flux:input type="number" wire:model="cantidad" min="1"
                                class="w-32 text-center text-2xl font-bold border-2 border-primary-500 focus:ring-2 focus:ring-primary-500"
                                x-ref="cantidadInput" x-init="$nextTick(() => $refs.cantidadInput.focus())" />

                            <flux:button wire:click="incrementarCantidad" size="sm"
                                class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </flux:button>
                        </div>

                        @if (session()->has('error'))
                            <div
                                class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm font-medium">{{ session('error') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Botones de Acción -->
            <div class="flex justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <flux:button wire:click="$set('modal_cantidad', false)">
                    Cancelar
                </flux:button>
                <flux:button wire:click="agregarAlCarrito" variant="primary">
                    Agregar al Carrito
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
