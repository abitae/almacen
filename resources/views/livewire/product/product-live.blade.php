<div>
    <x-mary-card title="Productos" subtitle="Listado de productos disponibles" shadow separator>
        <x-slot:menu>
            <flux:button icon="plus" wire:click="new">Nuevo</flux:button>
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
                                @if ($product->images->first())
                                    <img src="{{ $product->images->first()->image_path }}" alt="Imagen del producto"
                                        class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </td>
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
    @if ($product)
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
                <flux:text class="mt-1">
                    {{ $is_edit ? 'Edite los detalles del producto seleccionado.' : 'Complete el formulario para crear un nuevo producto.' }}
                </flux:text>
            </div>
            <form wire:submit="{{ $is_edit ? 'update' : 'store' }}" class="grid grid-cols-1 gap-4">
                <x-mary-tabs wire:model="selectedTab" class="w-full">
                    <x-mary-tab name="basic_info" label="Información Básica" icon="o-document-text">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <flux:input label="Código Interno" wire:model="internal_code" required class="w-full"
                                placeholder="Ingrese código interno" />
                            <flux:input label="Código de Barras" wire:model="barcode" class="w-full"
                                placeholder="Ingrese código de barras" />
                            <flux:input label="Nombre Comercial" wire:model="commercial_name" required class="w-full"
                                placeholder="Ingrese nombre comercial" />
                            <flux:input label="Nombre Técnico" wire:model="technical_name" class="w-full"
                                placeholder="Ingrese nombre técnico" />
                            <flux:input label="Presentación" wire:model="presentation" required class="w-full"
                                placeholder="Ej: Caja, Botella, etc." />
                        </div>
                    </x-mary-tab>
                    <x-mary-tab name="units_prices" label="Unidades y Precios" icon="o-currency-dollar">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <flux:input label="Unidad Primaria" wire:model="primary_unit" required class="w-full"
                                placeholder="Ej: Unidad, Kg, Lt" />
                            <flux:input label="Unidad Secundaria" wire:model="secondary_unit" class="w-full"
                                placeholder="Ej: Docena, Caja" />
                            <div class="space-y-2">
                                <flux:input type="number" step="0.01" min="0" label="Precio de Compra"
                                    wire:model="purchase_price" required class="w-full" placeholder="0.00" />
                            </div>
                            <div class="space-y-2">
                                <flux:input type="number" step="0.01" min="0" label="Precio de Venta"
                                    wire:model="sale_price" required class="w-full" placeholder="0.00" />
                            </div>
                            <div class="space-y-2">
                                <flux:input type="number" step="0.01" min="0" max="100"
                                    label="Margen de Ganancia (%)" wire:model="profit_margin" required class="w-full"
                                    placeholder="0.00" />
                            </div>
                        </div>
                    </x-mary-tab>

                    <x-mary-tab name="inventory" label="Inventario" icon="o-cube">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div class="space-y-2">
                                <flux:input type="number" min="0" label="Stock Mínimo"
                                    wire:model="minimum_stock" required class="w-full" placeholder="0" />
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
                                <flux:textarea label="Descripción" wire:model="description" rows="3"
                                    class="w-full" placeholder="Ingrese una descripción detallada del producto" />
                            </div>
                        </div>
                    </x-mary-tab>

                    <x-mary-tab name="classification" label="Clasificación" icon="o-tag">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div class="space-y-2">
                                <x-mary-choices-offline label="Categoría" wire:model="category_id" :options="$categories"
                                    placeholder="Seleccione una categoría" required single searchable clearable />
                            </div>

                            <div class="space-y-2">
                                <x-mary-choices-offline label="Marca" wire:model="brand_id" :options="$brands"
                                    placeholder="Seleccione una marca" required single searchable clearable />
                            </div>

                            <div class="space-y-2">
                                <x-mary-choices-offline label="Proveedor" wire:model="supplier_id" :options="$suppliers"
                                    placeholder="Seleccione un proveedor" required single searchable clearable />
                            </div>
                        </div>
                    </x-mary-tab>
                    <x-mary-tab name="images" label="Imágenes" icon="o-photo">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="images">
                                Imágenes del Producto
                                <span class="text-xs text-gray-500">(Máximo 5 imágenes, 2MB cada una)</span>
                            </label>

                            <!-- Área de subida de imágenes -->
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-primary-500 transition-colors duration-200"
                                x-data="{
                                    isDragging: false,
                                    handleDragOver(e) {
                                        e.preventDefault();
                                        this.isDragging = true;
                                    },
                                    handleDragLeave() {
                                        this.isDragging = false;
                                    },
                                    handleDrop(e) {
                                        e.preventDefault();
                                        this.isDragging = false;
                                        @this.upload('tempImages', e.dataTransfer.files, (uploadedFilename) => {
                                            // Callback después de la subida
                                        }, () => {
                                            // Callback de error
                                        }, (event) => {
                                            // Callback de progreso
                                        });
                                    }
                                }" x-on:dragover="handleDragOver"
                                x-on:dragleave="handleDragLeave" x-on:drop="handleDrop"
                                :class="{ 'border-primary-500 bg-primary-50': isDragging }">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="images"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                            <span>Subir imágenes</span>
                                            <input wire:model="tempImages" type="file" class="sr-only"
                                                id="images" multiple
                                                accept="image/jpeg,image/png,image/gif,image/webp">
                                        </label>
                                        <p class="pl-1">o arrastrar y soltar</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, GIF, WEBP hasta 2MB
                                    </p>
                                </div>
                            </div>

                            @error('tempImages.*')
                                <div class="mt-2 text-red-500 text-xs flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror

                            <!-- Vista previa de imágenes nuevas -->
                            @if (count($tempImages) > 0)
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Imágenes nuevas</h4>
                                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                                        @foreach ($tempImages as $index => $image)
                                            <div class="relative group">
                                                <img src="{{ $image->temporaryUrl() }}"
                                                    class="h-24 w-24 object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                                <button wire:click="removeImage({{ $index }})"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                                @if ($index === 0)
                                                    <span
                                                        class="absolute top-0 left-0 bg-green-500 text-white text-xs px-2 py-1 rounded-tl-lg">Principal</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Imágenes existentes -->
                            @if ($is_edit && $images->count() > 0)
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Imágenes existentes</h4>
                                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                                        @foreach ($images as $image)
                                            <div class="relative group">
                                                <img src="{{ Storage::url($image->image_path) }}"
                                                    class="h-24 w-24 object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                                @if ($image->is_primary)
                                                    <span
                                                        class="absolute top-0 left-0 bg-green-500 text-white text-xs px-2 py-1 rounded-tl-lg">Principal</span>
                                                @endif
                                                <div
                                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                    <button wire:click="deleteImage({{ $image->id }})"
                                                        class="text-white hover:text-red-500 transition-colors duration-200">
                                                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </x-mary-tab>
                </x-mary-tabs>

                <!-- Errores de validación -->
                @if ($errors->any())
                    <div class="w-full mt-4">
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 rounded-md p-4 shadow-sm">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
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
                    <flux:button type="button" wire:click="$set('modal_form', false)"
                        class="w-full sm:w-auto flex items-center justify-center" icon="x-mark">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary"
                        class="w-full sm:w-auto flex items-center justify-center" icon="check" spinner>
                        {{ $is_edit ? 'Actualizar Producto' : 'Crear Producto' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
