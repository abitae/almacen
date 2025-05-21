<div>
    <x-mary-card title="Movimientos" subtitle="Listado de movimientos de inventario" shadow separator>
        <x-slot:menu>
            <flux:button icon="plus" wire:click="new">Nuevo</flux:button>
            <flux:button icon="arrow-down-tray" wire:click="export">Exportar</flux:button>
            <flux:button icon="printer" wire:click="print">Imprimir</flux:button>
        </x-slot:menu>

        <div class="mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar..."
                    clearable />
                <x-mary-choices-offline label="" wire:model.live="warehouseFilter" :options="$warehouses"
                    placeholder="Filtrar por almacén" single searchable inline clearable />
                <x-mary-choices-offline label="" wire:model.live="locationFilter" :options="$locations"
                    placeholder="Filtrar por ubicación" single searchable inline clearable
                    :disabled="!$warehouseFilter" />
                <flux:select wire:model.live="typeFilter" placeholder="Filtrar por tipo" class="w-full">
                    <option value="">Todos los tipos</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                    <tr>
                        <th scope="col" wire:click="sortBy('created_at')"
                            class="px-3 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            Fecha
                            @if($sortField === 'created_at')
                                @if($sortDirection === 'asc')
                                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                @endif
                            @endif
                        </th>
                        <th scope="col" wire:click="sortBy('reference')"
                            class="px-3 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            Referencia
                            @if($sortField === 'reference')
                                @if($sortDirection === 'asc')
                                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                @endif
                            @endif
                        </th>
                        <th scope="col" class="px-3 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Producto
                        </th>
                        <th scope="col" class="px-3 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th scope="col" class="px-3 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Almacén
                        </th>
                        <th scope="col" class="px-3 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Ubicación
                        </th>
                        <th scope="col" class="px-3 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Cantidad
                        </th>
                        <th scope="col" class="px-3 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Precio Unitario
                        </th>
                        <th scope="col" class="px-3 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Total
                        </th>
                        <th scope="col" class="px-3 py-2 md:px-6 md:py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($movements as $movement)
                        <tr class="{{ $loop->even ? 'bg-gray-50 dark:bg-gray-700' : '' }} hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-xs md:text-sm dark:text-gray-200">{{ $movement->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-xs md:text-sm dark:text-gray-200">{{ $movement->product->commercial_name }}</div>
                                <div class="text-xs md:text-sm dark:text-gray-200">{{ $movement->product->barcode }}</div>

                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-xs md:text-sm dark:text-gray-200">{{ $movement->reference }}</div>
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $movement->type === 'entry' ? 'bg-green-100 text-green-800' :
                                       ($movement->type === 'exit' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $movement->type }}
                                </span>
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-xs md:text-sm dark:text-gray-200">{{ $movement->warehouse->name }}</div>
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-xs md:text-sm dark:text-gray-200">{{ $movement->location->name }}</div>
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-xs md:text-sm dark:text-gray-200">{{ number_format($movement->quantity, 2) }}</div>
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-xs md:text-sm dark:text-gray-200">{{ number_format($movement->unit_price, 2) }}</div>
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-xs md:text-sm dark:text-gray-200">{{ number_format($movement->quantity * $movement->unit_price, 2) }}</div>
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-1 md:space-x-2 justify-end">
                                    <flux:button size="xs" md:size="sm" tooltip="Ver detalles" icon="eye"
                                        wire:click="show({{ $movement->id }})" />
                                    <flux:button size="xs" md:size="sm" tooltip="Editar" icon="pencil"
                                        wire:click="edit({{ $movement->id }})" />
                                    <flux:button size="xs" md:size="sm" tooltip="Eliminar" icon="trash"
                                        class="text-error hover:text-error-600"
                                        wire:click="delete({{ $movement->id }})"
                                        wire:confirm="¿Estás seguro de eliminar este movimiento?" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <p class="mt-2 text-sm">No se encontraron movimientos</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $movements->links() }}
        </div>
    </x-mary-card>

    @if (session()->has('message'))
        <x-mary-toast type="success">
            {{ session('message') }}
        </x-mary-toast>
    @endif

    @if ($movement)
        <flux:modal wire:model.self="modal_show" variant="flyout" class="w-1/3">
            <div class="space-y-2">
                <div>
                    <flux:heading size="lg">Detalles del Movimiento</flux:heading>
                    <flux:text class="mt-1">Información detallada del movimiento seleccionado.</flux:text>
                </div>

                <div class="grid grid-cols-1 gap-1">
                    <div class="bg-gray-50 dark:bg-gray-800 p-1 rounded-lg">
                        <flux:text size="sm" class="font-semibold text-primary">Información Básica</flux:text>
                        <div class="mt-1 space-y-1">
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                                <flux:text size="sm" class="font-semibold">Referencia:</flux:text>
                                <flux:text size="sm">{{ $movement->reference }}</flux:text>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                                <flux:text size="sm" class="font-semibold">Tipo:</flux:text>
                                <flux:text size="sm">{{ $movement->type }}</flux:text>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                                <flux:text size="sm" class="font-semibold">Almacén:</flux:text>
                                <flux:text size="sm">{{ $movement->warehouse->name }}</flux:text>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                                <flux:text size="sm" class="font-semibold">Ubicación:</flux:text>
                                <flux:text size="sm">{{ $movement->location->name }}</flux:text>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                                <flux:text size="sm" class="font-semibold">Cantidad:</flux:text>
                                <flux:text size="sm">{{ number_format($movement->quantity, 2) }}</flux:text>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                                <flux:text size="sm" class="font-semibold">Precio Unitario:</flux:text>
                                <flux:text size="sm">{{ number_format($movement->unit_price, 2) }}</flux:text>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1">
                                <flux:text size="sm" class="font-semibold">Total:</flux:text>
                                <flux:text size="sm">{{ number_format($movement->quantity * $movement->unit_price, 2) }}</flux:text>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 p-1 rounded-lg">
                        <flux:text size="sm" class="font-semibold text-primary">Descripción</flux:text>
                        <div class="mt-2">
                            <flux:text size="sm">{{ $movement->description ?? 'Sin descripción' }}</flux:text>
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
                <flux:heading size="lg">{{ $is_edit ? 'Editar Movimiento' : 'Nuevo Movimiento' }}</flux:heading>
                <flux:text class="mt-1">
                    {{ $is_edit ? 'Edite los detalles del movimiento seleccionado.' : 'Complete el formulario para crear un nuevo movimiento.' }}
                </flux:text>
            </div>

            <form wire:submit="{{ $is_edit ? 'update' : 'store' }}" class="grid grid-cols-1 gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:select label="Tipo de Movimiento" wire:model="type" required class="w-full">
                        <option value="">Seleccione un tipo</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>

                    <flux:input label="Referencia" wire:model="reference" required class="w-full"
                        placeholder="Ingrese referencia del movimiento" />

                    <div class="sm:col-span-2">
                        <x-mary-choices-offline label="Almacén" wire:model="warehouse_id" :options="$warehouses"
                            placeholder="Seleccione un almacén" required single searchable clearable />
                    </div>

                    <div class="sm:col-span-2">
                        <x-mary-choices-offline label="Ubicación" wire:model="location_id" :options="$locations"
                            placeholder="Seleccione una ubicación" required single searchable clearable
                            :disabled="!$warehouse_id" />
                    </div>

                    @if($type === 'transfer')
                        <div class="sm:col-span-2">
                            <x-mary-choices-offline label="Almacén Destino" wire:model="destination_warehouse_id"
                                :options="$warehouses" placeholder="Seleccione un almacén destino"
                                required single searchable clearable />
                        </div>

                        <div class="sm:col-span-2">
                            <x-mary-choices-offline label="Ubicación Destino" wire:model="destination_location_id"
                                :options="$destinationLocations" placeholder="Seleccione una ubicación destino"
                                required single searchable clearable
                                :disabled="!$destination_warehouse_id" />
                        </div>
                    @endif

                    <flux:input type="number" step="0.01" min="0" label="Cantidad" wire:model="quantity"
                        required class="w-full" placeholder="0.00" />

                    <flux:input type="number" step="0.01" min="0" label="Precio Unitario" wire:model="unit_price"
                        required class="w-full" placeholder="0.00" />

                    <div class="sm:col-span-2">
                        <flux:textarea label="Descripción" wire:model="description" rows="3"
                            class="w-full" placeholder="Ingrese una descripción detallada del movimiento" />
                    </div>
                </div>

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

                <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-4 mt-6">
                    <flux:button type="button" wire:click="$set('modal_form', false)"
                        class="w-full sm:w-auto flex items-center justify-center" icon="x-mark">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary"
                        class="w-full sm:w-auto flex items-center justify-center" icon="check" spinner>
                        {{ $is_edit ? 'Actualizar Movimiento' : 'Crear Movimiento' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
