<?php

namespace App\Livewire\Location;

use App\Models\Location;
use App\Models\Warehouse;
use App\Services\LocationService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class LocationLive extends Component
{
    use WithPagination;

    public array $headers_locations = [];
    public string $search = '';
    public int $perPage = 10;
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public string $warehouseFilter = '';
    public string $statusFilter = '';
    public bool $modal_show = false;
    public bool $modal_form = false;
    public bool $is_edit = false;
    public ?Location $location = null;
    public string $name = '';
    public string $code = '';
    public string $warehouse_id = '';
    public string $description = '';
    public string $status = 'active';

    protected LocationService $locationService;

    /**
     * Inicializa el servicio de ubicación
     */
    public function boot(LocationService $locationService): void
    {
        $this->locationService = $locationService;
    }

    /**
     * Monta el componente y carga los encabezados
     */
    public function mount(): void
    {
        $this->headers_locations = $this->locationService->getHeaders();
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
     * Muestra los detalles de una ubicación
     */
    public function show(int $id): void
    {
        $this->location = Location::findOrFail($id);
        $this->modal_show = true;
    }

    /**
     * Elimina una ubicación
     */
    public function delete(int $id): void
    {
        try {
            $location = Location::findOrFail($id);
            $this->locationService->delete($location);
        } catch (\Exception $e) {

        }
    }

    /**
     * Prepara el formulario para una nueva ubicación
     */
    public function new(): void
    {
        $this->reset([
            'name', 'code', 'warehouse_id', 'description', 'status', 'type'
        ]);

        $this->resetErrorBag();
        $this->resetValidation();
        $this->is_edit = false;
        $this->modal_form = true;
    }

    /**
     * Prepara el formulario para editar una ubicación existente
     */
    public function edit(int $id): void
    {
        $this->is_edit = true;
        $this->location = Location::findOrFail($id);

        $this->fill([
            'name' => $this->location->name,
            'code' => $this->location->code,
            'warehouse_id' => $this->location->warehouse_id,
            'description' => $this->location->description,
            'type' => $this->location->type,
            'status' => $this->location->status,
        ]);

        $this->modal_form = true;
    }

    /**
     * Almacena una nueva ubicación
     */
    public function store(): void
    {
        $this->validate(
            $this->locationService->getValidationRules(),
            $this->locationService->getValidationMessages()
        );

        $data = $this->getFormData();
        $this->locationService->create($data);
        $this->modal_form = false;
    }

    /**
     * Actualiza una ubicación existente
     */
    public function update(): void
    {
        $this->validate(
            $this->locationService->getValidationRules(true, $this->location->id),
            $this->locationService->getValidationMessages()
        );

        $data = $this->getFormData();
        $this->locationService->update($this->location, $data);
        $this->modal_form = false;
    }

    /**
     * Exporta los datos de ubicaciones
     */
    public function export(): void
    {
        // Implementar exportación
    }

    /**
     * Imprime los datos de ubicaciones
     */
    public function print(): void
    {
        // Implementar impresión
    }


    protected function getFormData(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'warehouse_id' => $this->warehouse_id,
            'description' => $this->description,
            'status' => $this->status,
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
            'statusFilter' => $this->statusFilter,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ];
        // dump($filters);
        return view('livewire.location.location-live', [
            'locations' => $this->locationService->getLocations($filters, $this->perPage),
            'warehouses' => $this->locationService->getWarehouses()
        ]);
    }
}
