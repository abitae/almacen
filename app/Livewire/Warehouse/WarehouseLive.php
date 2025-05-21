<?php

namespace App\Livewire\Warehouse;

use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;

class WarehouseLive extends Component
{
    use WithPagination;

    public array $headers_warehouses = [];
    public string $search = '';
    public int $perPage = 10;
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public bool $showModal = false;
    public ?Warehouse $warehouse = null;
    public bool $isEditing = false;
    public bool $modal_show = false;
    public bool $modal_form = false;
    public string $statusFilter = '';
    public string $name = '';
    public string $code = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public string $manager_name = '';
    public string $status = 'active';
    public bool $is_edit = false;

    protected WarehouseService $warehouseService;

    /**
     * Inicializa el servicio de almacén
     */
    public function boot(WarehouseService $warehouseService): void
    {
        $this->warehouseService = $warehouseService;
    }

    /**
     * Monta el componente y carga los encabezados
     */
    public function mount(): void
    {
        $this->headers_warehouses = $this->warehouseService->getHeaders();
    }

    /**
     * Resetea la paginación cuando se actualiza la búsqueda
     */
    public function updatingSearch(): void
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

    public function delete(int $warehouseId): void
    {
        try {
            $warehouse = Warehouse::findOrFail($warehouseId);
            $this->warehouseService->delete($warehouse);
            $this->flashMessage('deleted');
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
        }
    }

    /**
     * Renderiza la vista del componente
     */
    public function render(): View
    {
        $filters = [
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ];

        return view('livewire.warehouse.warehouse-live', [
            'warehouses' => $this->warehouseService->getWarehouses($filters, $this->perPage)
        ]);
    }


    public function show(int $id): void
    {
        $this->warehouse = Warehouse::findOrFail($id);
        $this->name = $this->warehouse->name;
        $this->code = $this->warehouse->code;
        $this->address = $this->warehouse->address;
        $this->status = $this->warehouse->status;
        $this->phone = $this->warehouse->phone;
        $this->email = $this->warehouse->email;
        $this->manager_name = $this->warehouse->manager_name;
        $this->is_edit = true;
        $this->modal_show = true;
    }

    public function new(): void
    {
        $this->reset([
            'name', 'code', 'address', 'status', 'phone', 'email', 'manager_name'
        ]);

        $this->resetErrorBag();
        $this->resetValidation();
        $this->is_edit = false;
        $this->modal_form = true;
    }

    public function edit(int $id): void
    {
        $this->is_edit = true;
        $this->warehouse = Warehouse::findOrFail($id);

        $this->fill([
            'name' => $this->warehouse->name,
            'code' => $this->warehouse->code,
            'address' => $this->warehouse->address,
            'status' => $this->warehouse->status,
            'phone' => $this->warehouse->phone,
            'email' => $this->warehouse->email,
            'manager_name' => $this->warehouse->manager_name,
        ]);

        $this->modal_form = true;
    }

    public function store(): void
    {
        $this->validate(
            $this->warehouseService->getValidationRules(),
            $this->warehouseService->getValidationMessages()
        );

        $data = $this->getFormData();
        $this->warehouseService->create($data);

        $this->modal_form = false;
    }

    public function update(): void
    {
        
        $this->validate(
            $this->warehouseService->getValidationRules(true, $this->warehouse->id),
            $this->warehouseService->getValidationMessages()
        );

        $data = $this->getFormData();
        $this->warehouseService->update($this->warehouse, $data);
        $this->modal_form = false;
    }

    public function toggleStatus(int $id): void
    {
        $warehouse = Warehouse::findOrFail($id);
        $this->warehouseService->toggleStatus($warehouse);
    }

    public function export(): void
    {
        // Implementar exportación
    }

    public function print(): void
    {
        // Implementar impresión
    }

    protected function getFormData(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'status' => $this->status,
            'phone' => $this->phone,
            'email' => $this->email,
            'manager_name' => $this->manager_name,
        ];
    }
}
