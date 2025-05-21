<?php

namespace App\Livewire\Movement;

use App\Models\Location;
use App\Models\Movement;
use App\Models\Warehouse;
use App\Services\MovementService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class MovementLive extends Component
{
    use WithPagination;

    public array $headers_movements = [];
    public string $search = '';
    public int $perPage = 10;
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public string $warehouseFilter = '';
    public string $locationFilter = '';
    public string $typeFilter = '';
    public bool $modal_show = false;
    public bool $modal_form = false;
    public bool $is_edit = false;
    public ?Movement $movement = null;

    // Campos del formulario
    public string $type = '';
    public string $reference = '';
    public string $warehouse_id = '';
    public string $location_id = '';
    public string $destination_warehouse_id = '';
    public string $destination_location_id = '';
    public float $quantity = 0;
    public float $unit_price = 0;
    public string $description = '';

    protected MovementService $movementService;

    /**
     * Inicializa el servicio de movimientos
     */
    public function boot(MovementService $movementService): void
    {
        $this->movementService = $movementService;
    }

    /**
     * Monta el componente y carga los encabezados
     */
    public function mount(): void
    {
        $this->headers_movements = $this->movementService->getHeaders();
    }

    /**
     * Actualiza la búsqueda y resetea la paginación
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Actualiza el filtro de almacén y resetea la paginación y ubicación
     */
    public function updatingWarehouseFilter(): void
    {
        $this->resetPage();
        $this->locationFilter = '';
    }

    /**
     * Actualiza el filtro de ubicación y resetea la paginación
     */
    public function updatingLocationFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Actualiza el filtro de tipo y resetea la paginación
     */
    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Ordena los resultados por el campo especificado
     */
    public function sortBy(string $field): void
    {
        $this->sortDirection = $this->sortField === $field
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : 'asc';

        $this->sortField = $field;
    }

    /**
     * Muestra los detalles de un movimiento
     */
    public function show(int $id): void
    {
        $this->movement = Movement::findOrFail($id);
        $this->modal_show = true;
    }

    /**
     * Elimina un movimiento
     */
    public function delete(int $id): void
    {
        try {
            $movement = Movement::findOrFail($id);
            $this->movementService->delete($movement);
            session()->flash('message', 'Movimiento eliminado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    /**
     * Prepara el formulario para un nuevo movimiento
     */
    public function new(): void
    {
        $this->reset([
            'type', 'reference', 'warehouse_id', 'location_id',
            'destination_warehouse_id', 'destination_location_id',
            'quantity', 'unit_price', 'description'
        ]);

        $this->resetErrorBag();
        $this->resetValidation();
        $this->is_edit = false;
        $this->modal_form = true;
    }

    /**
     * Prepara el formulario para editar un movimiento existente
     */
    public function edit(int $id): void
    {
        $this->is_edit = true;
        $this->movement = Movement::findOrFail($id);

        $this->fill([
            'type' => $this->movement->type,
            'reference' => $this->movement->reference,
            'warehouse_id' => $this->movement->warehouse_id,
            'location_id' => $this->movement->location_id,
            'destination_warehouse_id' => $this->movement->destination_warehouse_id,
            'destination_location_id' => $this->movement->destination_location_id,
            'quantity' => $this->movement->quantity,
            'unit_price' => $this->movement->unit_price,
            'description' => $this->movement->description,
        ]);

        $this->modal_form = true;
    }

    /**
     * Almacena un nuevo movimiento
     */
    public function store(): void
    {
        $this->validate(
            $this->movementService->getValidationRules(),
            $this->movementService->getValidationMessages()
        );

        $data = $this->getFormData();
        $this->movementService->create($data);
        $this->modal_form = false;
    }

    /**
     * Actualiza un movimiento existente
     */
    public function update(): void
    {
        $this->validate(
            $this->movementService->getValidationRules(true, $this->movement->id),
            $this->movementService->getValidationMessages()
        );

        $data = $this->getFormData();
        $this->movementService->update($this->movement, $data);
        $this->modal_form = false;
    }

    /**
     * Exporta los datos de movimientos
     */
    public function export(): void
    {
        // Implementar exportación
    }

    /**
     * Imprime los datos de movimientos
     */
    public function print(): void
    {
        // Implementar impresión
    }

    /**
     * Obtiene los datos del formulario
     */
    protected function getFormData(): array
    {
        return [
            'type' => $this->type,
            'reference' => $this->reference,
            'warehouse_id' => $this->warehouse_id,
            'location_id' => $this->location_id,
            'destination_warehouse_id' => $this->destination_warehouse_id,
            'destination_location_id' => $this->destination_location_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'description' => $this->description,
        ];
    }

    /**
     * Renderiza la vista del componente
     */
    public function render(): View
    {
        $filters = [
            'search' => $this->search,
            'warehouseFilter' => $this->warehouseFilter,
            'locationFilter' => $this->locationFilter,
            'typeFilter' => $this->typeFilter,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ];

        return view('livewire.movement.movement-live', [
            'movements' => $this->movementService->getMovements($filters, $this->perPage),
            'warehouses' => $this->movementService->getWarehouses(),
            'locations' => $this->warehouseFilter
                ? $this->movementService->getLocationsByWarehouse($this->warehouseFilter)
                : collect(),
            'destinationLocations' => $this->destination_warehouse_id
                ? $this->movementService->getLocationsByWarehouse($this->destination_warehouse_id)
                : collect(),
            'types' => [
                'entry' => 'Entrada',
                'exit' => 'Salida',
                'transfer' => 'Transferencia'
            ]
        ]);
    }
}
