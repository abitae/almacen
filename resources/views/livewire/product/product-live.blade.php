<div>
    <x-mary-card title="Productos" subtitle="Listado de productos disponibles" shadow separator>
        <x-slot:menu>
            <flux:button icon="plus" wire:click="new">Nuevo
            </flux:button>
            <flux:button icon="arrow-down-tray" wire:click="export">Exportar</flux:button>
            <flux:button icon="arrow-up-tray" wire:click="import">Importar</flux:button>
            <flux:button icon="printer" wire:click="print">Imprimir</flux:button>
        </x-slot:menu>

        <div class="mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar..."
                    clearable />
                <x-mary-choices-offline label="" wire:model.live="categoryFiltro" :options="$categories"
                    placeholder="Filtrar por categoría" single searchable inline clearable />
                <x-mary-choices-offline label="" wire:model.live="brandFiltro" :options="$brands"
                    placeholder="Filtrar por marca" single searchable inline clearable />
                <flux:select wire:model.live="statusFiltro" placeholder="Filtrar por estado" class="w-full">
                    <option value="">Todos los estados</option>
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                </flux:select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table
                class="w-full bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                    <tr>
                        @foreach ($headers_products as $header)
                            <th scope="col"
                                class="px-3 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                        <th scope="col"
                            class="px-3 py-2 md:px-6 md:py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($products as $product)
                        <tr
                            class="{{ $loop->even ? 'bg-gray-50 dark:bg-gray-700' : '' }} hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <td
                                class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap text-xs md:text-sm dark:text-gray-200">
                                {{ $product->id }}</td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-xs font-medium dark:text-gray-200">{{ $product->internal_code }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">
                                    {{ $product->barcode ?? 'N/A' }}</div>
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-xs md:text-sm text-primary font-medium dark:text-primary-400">
                                    {{ Str::limit($product->commercial_name, 30) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">
                                    {{ Str::limit($product->technical_name, 30) }}
                                </div>
                            </td>
                            <td
                                class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap text-xs md:text-sm dark:text-gray-200">
                                {{ $product->category->name ?? '-' }}</td>
                            <td
                                class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap text-xs md:text-sm dark:text-gray-200">
                                {{ $product->brand->name ?? '-' }}</td>
                            <td
                                class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap text-xs md:text-sm dark:text-gray-200">
                                {{ $product->presentation ?? '-' }}</td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-xs md:text-sm text-gray-500 dark:text-gray-400">
                                    {{ $product->primary_unit ?? '-' }}</div>
                                <div class="text-xs md:text-sm text-gray-500 dark:text-gray-400 hidden sm:block">
                                    {{ $product->secondary_unit ?? '-' }}</div>
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="flex space-x-1 md:space-x-2 justify-end">
                                    <flux:button size="xs" md:size="sm" tooltip="Ver detalles" icon="eye"
                                        wire:click="show({{ $product->id }})" />

                                    <flux:button size="xs" md:size="sm" tooltip="Editar" icon="pencil"
                                        wire:click="edit({{ $product->id }})" />
                                    <flux:button size="xs" md:size="sm" tooltip="Eliminar" icon="trash"
                                        class="text-error hover:text-error-600"
                                        wire:click="delete({{ $product->id }})"
                                        wire:confirm="¿Estás seguro de eliminar este producto?" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers_products) + 1 }}"
                                class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <p class="mt-2 text-sm">No se encontraron productos</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </x-mary-card>

    @if (session()->has('message'))
        <x-mary-toast type="success">
            {{ session('message') }}
        </x-mary-toast>
    @endif
    @if($product)
    <flux:modal wire:model.self="modal_show" variant="flyout" class="w-1/3">
        <div class="space-y-2">
            <div>
                <flux:heading size="lg">Detalles del Producto</flux:heading>
                <flux:text class="mt-1">Información detallada del producto seleccionado.</flux:text>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-1">
                <div class="bg-gray-50 dark:bg-gray-800 p-1 rounded-lg">
                    <flux:text size="sm" class="font-semibold text-primary">Información Básica</flux:text>
                    <div class="mt-1 space-y-1">
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Nombre Comercial:</flux:text>
                            <flux:text size="sm">{{ $product->commercial_name }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Nombre Técnico:</flux:text>
                            <flux:text size="sm">{{ $product->technical_name }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Código Interno:</flux:text>
                            <flux:text size="sm">{{ $product->internal_code }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Código de Barras:</flux:text>
                            <flux:text size="sm">{{ $product->barcode }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Precio de Venta:</flux:text>
                            <flux:text size="sm">S/ {{ $product->sale_price }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Precio de Compra:</flux:text>
                            <flux:text size="sm">S/ {{ $product->purchase_price }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Stock Mínimo:</flux:text>
                            <flux:text size="sm">{{ $product->minimum_stock }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Stock Máximo:</flux:text>
                            <flux:text size="sm">{{ $product->maximum_stock }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Margen de Ganancia:</flux:text>
                            <flux:text size="sm">{{ $product->profit_margin }}%</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Estado:</flux:text>
                            <flux:text size="sm">{{ $product->status }}</flux:text>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 p-1 rounded-lg">
                    <flux:text size="sm" class="font-semibold text-primary">Clasificación</flux:text>
                    <div class="mt-1 space-y-1">
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Categoría:</flux:text>
                            <flux:text size="sm">{{ $product->category->name ?? '-' }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Marca:</flux:text>
                            <flux:text size="sm">{{ $product->brand->name ?? '-' }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Unidad Primaria:</flux:text>
                            <flux:text size="sm">{{ $product->primary_unit ?? '-' }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                            <flux:text size="sm" class="font-semibold">Unidad Secundaria:</flux:text>
                            <flux:text size="sm">{{ $product->secondary_unit ?? '-' }}</flux:text>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 bg-gray-50 dark:bg-gray-800 p-1 rounded-lg">
                    <flux:text size="sm" class="font-semibold text-primary">Descripción</flux:text>
                    <div class="mt-2">
                        <flux:text size="sm">{{ $product->description ?? 'Sin descripción' }}</flux:text>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <flux:button wire:click="$set('modal_show', false)">Cerrar</flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
    <flux:modal wire:model.self="modal_form" variant="flyout" class="w-2/3">
        <div class="space-y-4 max-w-full">
            <div>
                <flux:heading size="lg">{{ $is_edit ? 'Editar Producto' : 'Nuevo Producto' }}</flux:heading>
                <flux:text class="mt-1">{{ $is_edit ? 'Edite los detalles del producto seleccionado.' : 'Complete el formulario para crear un nuevo producto.' }}</flux:text>
            </div>
            <form wire:submit="{{ $is_edit ? 'update' : 'store' }}" class="grid grid-cols-1 gap-4">
                <x-mary-tabs wire:model="selectedTab" class="w-full">
                    <x-mary-tab name="basic_info" label="Información Básica" icon="o-document-text">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <flux:input label="Código Interno" wire:model="internal_code" required class="w-full" placeholder="Ingrese código interno" />
                            <flux:input label="Código de Barras" wire:model="barcode" class="w-full" placeholder="Ingrese código de barras" />
                            <flux:input label="Nombre Comercial" wire:model="commercial_name" required class="w-full" placeholder="Ingrese nombre comercial" />
                            <flux:input label="Nombre Técnico" wire:model="technical_name" class="w-full" placeholder="Ingrese nombre técnico" />
                            <flux:input label="Presentación" wire:model="presentation" required class="w-full" placeholder="Ej: Caja, Botella, etc." />
                        </div>
                    </x-mary-tab>
                    <x-mary-tab name="units_prices" label="Unidades y Precios" icon="o-currency-dollar">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <flux:input label="Unidad Primaria" wire:model="primary_unit" required class="w-full" placeholder="Ej: Unidad, Kg, Lt" />
                            <flux:input label="Unidad Secundaria" wire:model="secondary_unit" class="w-full" placeholder="Ej: Docena, Caja" />
                            <div class="space-y-2">
                                <flux:input type="number" step="0.01" min="0" label="Precio de Compra"
                                    wire:model="purchase_price" required class="w-full" placeholder="0.00" />
                            </div>
                            <div class="space-y-2">
                                <flux:input type="number" step="0.01" min="0" label="Precio de Venta"
                                    wire:model="sale_price" required class="w-full" placeholder="0.00" />
                            </div>
                            <div class="space-y-2">
                                <flux:input type="number" step="0.01" min="0" max="100" label="Margen de Ganancia (%)"
                                    wire:model="profit_margin" required class="w-full" placeholder="0.00" />
                            </div>
                        </div>
                    </x-mary-tab>

                    <x-mary-tab name="inventory" label="Inventario" icon="o-cube">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div class="space-y-2">
                                <flux:input type="number" min="0" label="Stock Mínimo" wire:model="minimum_stock"
                                    required class="w-full" placeholder="0" />
                            </div>
                            <div class="space-y-2">
                                <flux:input type="number" min="0" label="Stock Máximo"
                                    wire:model="maximum_stock" class="w-full" placeholder="0" />
                            </div>
                            <div class="sm:col-span-2">
                                <flux:select label="Estado" wire:model="status" required class="w-full">
                                    <option value="">Seleccione un estado</option>
                                    <option value="active">Activo</option>
                                    <option value="inactive">Inactivo</option>
                                    <option value="discontinued">Descontinuado</option>
                                </flux:select>
                            </div>
                            <div class="sm:col-span-2">
                                <flux:textarea label="Descripción" wire:model="description"
                                    rows="3" class="w-full" placeholder="Ingrese una descripción detallada del producto" />
                            </div>
                        </div>
                    </x-mary-tab>

                    <x-mary-tab name="classification" label="Clasificación" icon="o-tag">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div class="space-y-2">
                                <x-mary-choices-offline
                                    label="Categoría"
                                    wire:model="category_id"
                                    :options="$categories"
                                    placeholder="Seleccione una categoría"
                                    required
                                    single
                                    searchable
                                    
                                    clearable
                                />
                            </div>

                            <div class="space-y-2">
                                <x-mary-choices-offline
                                    label="Marca"
                                    wire:model="brand_id"
                                    :options="$brands"
                                    placeholder="Seleccione una marca"
                                    required
                                    single
                                    searchable
                                    
                                    clearable
                                />
                            </div>

                            <div class="space-y-2">
                                <x-mary-choices-offline
                                    label="Proveedor"
                                    wire:model="supplier_id"
                                    :options="$suppliers"
                                    placeholder="Seleccione un proveedor"
                                    required
                                    single
                                    searchable
                                    
                                    clearable
                                />
                            </div>
                        </div>
                    </x-mary-tab>
                </x-mary-tabs>

                <!-- Errores de validación -->
                @if ($errors->any())
                    <div class="w-full mt-4">
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 rounded-md p-4 shadow-sm">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h4 class="font-semibold text-red-800">Por favor corrige los siguientes errores:</h4>
                            </div>
                            <ul class="mt-3 pl-6 list-disc text-sm space-y-1 text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li class="leading-tight">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Botones de acción -->
                <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-4 mt-6">
                    <flux:button
                        type="button"
                        wire:click="$set('modal_form', false)"
                        class="w-full sm:w-auto flex items-center justify-center"
                        icon="x-mark"
                    >
                        Cancelar
                    </flux:button>
                    <flux:button
                        type="submit"
                        variant="primary"
                        class="w-full sm:w-auto flex items-center justify-center"
                        icon="check"
                        spinner
                    >
                        {{ $is_edit ? 'Actualizar Producto' : 'Crear Producto' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
