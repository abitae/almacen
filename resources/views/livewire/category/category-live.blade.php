{{-- Componente principal de gestión de categorías --}}
<div>
    {{-- Tarjeta principal con listado de categorías --}}
    <x-mary-card title="Categorías" subtitle="Listado de categorías disponibles" shadow separator>
        {{-- Botón para crear nueva categoría --}}
        <x-slot:menu>
            <flux:button icon="plus" wire:click="new" aria-label="Crear nueva categoría">Nueva</flux:button>
        </x-slot:menu>

        {{-- Filtros de búsqueda --}}
        <div class="mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    placeholder="Buscar categorías..."
                    clearable
                    aria-label="Buscar categorías" />
                <flux:select
                    wire:model.live="statusFiltro"
                    placeholder="Filtrar por estado"
                    class="w-full"
                    aria-label="Filtrar por estado">
                    <option value="">Todos los estados</option>
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                </flux:select>
            </div>
        </div>

        {{-- Tabla de categorías --}}
        <div class="overflow-x-auto">
            <table class="w-full bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 border-collapse" role="grid">
                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                    <tr>
                        @foreach ($headers as $header)
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
                    @forelse($categories as $category_item)
                        <tr wire:key="category-{{ $category_item->id }}"
                            class="{{ $loop->even ? 'bg-gray-50 dark:bg-gray-700' : '' }} hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap text-xs md:text-sm dark:text-gray-200">
                                {{ $category_item->id }}
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-xs md:text-sm text-primary font-medium dark:text-primary-400">
                                    {{ $category_item->name }}
                                </div>
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap text-xs md:text-sm dark:text-gray-200">
                                {{ Str::limit($category_item->description, 50) }}
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category_item->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}"
                                    role="status"
                                    aria-label="Estado: {{ $category_item->status === 'active' ? 'Activo' : 'Inactivo' }}">
                                    {{ $category_item->status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap text-xs md:text-sm dark:text-gray-200">
                                {{ $category_item->products_count }}
                            </td>
                            <td class="px-3 py-2 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="flex space-x-1 md:space-x-2 justify-end">
                                    <flux:button
                                        size="xs"
                                        md:size="sm"
                                        tooltip="Ver detalles"
                                        icon="eye"
                                        wire:click="show({{ $category_item->id }})"/>
                                    <flux:button
                                        size="xs"
                                        md:size="sm"
                                        tooltip="Editar"
                                        icon="pencil"
                                        wire:click="edit({{ $category_item->id }})"/>
                                    <flux:button
                                        size="xs"
                                        md:size="sm"
                                        tooltip="Eliminar"
                                        icon="trash"
                                        class="text-error hover:text-error-600"
                                        wire:click="delete({{ $category_item->id }})"
                                        wire:confirm="¿Estás seguro de eliminar esta categoría?"/>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) + 1 }}"
                                class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <p class="mt-2 text-sm">No se encontraron categorías</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </x-mary-card>

    {{-- Notificación de éxito --}}
    @if (session()->has('message'))
        <x-mary-toast type="success">
            {{ session('message') }}
        </x-mary-toast>
    @endif

    {{-- Modal de detalles --}}
    @if ($category)
        <flux:modal wire:model.self="modal_show" variant="flyout" class="w-1/3">
            <div class="space-y-2">
                <div>
                    <flux:heading size="lg">Detalles de la Categoría</flux:heading>
                    <flux:text class="mt-1">Información detallada de la categoría seleccionada.</flux:text>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <div class="space-y-2">
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                            <flux:text size="sm" class="font-semibold">Nombre:</flux:text>
                            <flux:text size="sm">{{ $category->name }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                            <flux:text size="sm" class="font-semibold">Descripción:</flux:text>
                            <flux:text size="sm">{{ $category->description }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                            <flux:text size="sm" class="font-semibold">Estado:</flux:text>
                            <flux:text size="sm">{{ $category->status === 'active' ? 'Activo' : 'Inactivo' }}</flux:text>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                            <flux:text size="sm" class="font-semibold">Productos:</flux:text>
                            <flux:text size="sm">{{ $category->products_count }}</flux:text>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <flux:button wire:click="$set('modal_show', false)" aria-label="Cerrar detalles">Cerrar</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    {{-- Modal de formulario --}}
    <flux:modal wire:model.self="modal_form" variant="flyout" class="w-1/2">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">{{ $is_edit ? 'Editar Categoría' : 'Nueva Categoría' }}</flux:heading>
                <flux:text class="mt-1">
                    {{ $is_edit ? 'Edite los detalles de la categoría seleccionada.' : 'Complete el formulario para crear una nueva categoría.' }}
                </flux:text>
            </div>

            <form wire:submit="{{ $is_edit ? 'update' : 'store' }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4">
                    <flux:input
                        label="Nombre"
                        wire:model="name"
                        required
                        class="w-full"
                        placeholder="Ingrese el nombre de la categoría"
                        aria-label="Nombre de la categoría" />
                    <flux:textarea
                        label="Descripción"
                        wire:model="description"
                        rows="3"
                        class="w-full"
                        placeholder="Ingrese una descripción detallada"
                        aria-label="Descripción de la categoría" />
                    <flux:select
                        label="Estado"
                        wire:model="status"
                        required
                        class="w-full"
                        aria-label="Estado de la categoría">
                        <option value="">Seleccione un estado</option>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </flux:select>
                </div>

                {{-- Mensajes de error --}}
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 rounded-md p-4 shadow-sm" role="alert">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
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

                {{-- Botones de acción --}}
                <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-4">
                    <flux:button
                        type="button"
                        wire:click="$set('modal_form', false)"
                        class="w-full sm:w-auto flex items-center justify-center"
                        icon="x-mark"
                        aria-label="Cancelar operación">
                        Cancelar
                    </flux:button>
                    <flux:button
                        type="submit"
                        variant="primary"
                        class="w-full sm:w-auto flex items-center justify-center"
                        icon="check"
                        spinner
                        aria-label="{{ $is_edit ? 'Actualizar categoría' : 'Crear categoría' }}">
                        {{ $is_edit ? 'Actualizar Categoría' : 'Crear Categoría' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
