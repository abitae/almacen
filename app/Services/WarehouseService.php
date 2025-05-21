<?php

namespace App\Services;

use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;

class WarehouseService
{
    /**
     * Obtiene los encabezados para la tabla de almacenes
     *
     * @return array<array{key: string, label: string}>
     */
    public function getHeaders(): array
    {
        return [
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'code', 'label' => 'Código'],
            ['key' => 'address', 'label' => 'Dirección'],
            ['key' => 'status', 'label' => 'Estado'],
        ];
    }

    /**
     * Obtiene la lista de almacenes con filtros
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getWarehouses(array $filters, int $perPage)
    {
        $query = Warehouse::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('code', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('address', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['statusFilter'])) {
            $query->where('status', $filters['statusFilter']);
        }

        if (!empty($filters['sortField'])) {
            $query->orderBy($filters['sortField'], $filters['sortDirection'] ?? 'asc');
        }

        return $query->paginate($perPage);
    }

    public function getValidationRules(bool $isUpdate = false, ?int $warehouseId = null): array
    {
        $uniqueRule = $isUpdate
            ? 'unique:warehouses,code,' . $warehouseId
            : 'unique:warehouses,code';

        return [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|' . $uniqueRule,
            'address' => 'required|string|max:200',
            'status' => 'required|in:active,inactive',
            'phone' => 'required|string|regex:/^[0-9+\-\s()]{7,20}$/',
            'email' => 'required|email|max:100',
            'manager_name' => 'required|string|max:100',
        ];
    }

    public function getValidationMessages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser texto',
            'name.max' => 'El nombre no debe exceder 255 caracteres',
            'code.required' => 'El código es requerido',
            'code.string' => 'El código debe ser texto',
            'code.max' => 'El código no debe exceder 255 caracteres',
            'code.unique' => 'El código ya existe en otro almacén',
            'address.required' => 'La dirección es requerida',
            'address.string' => 'La dirección debe ser texto',
            'address.max' => 'La dirección no debe exceder 255 caracteres',
            'status.required' => 'El estado es requerido',
            'status.string' => 'El estado debe ser texto',
            'status.max' => 'El estado no debe exceder 255 caracteres',
            'phone.required' => 'El teléfono es requerido',
            'phone.string' => 'El teléfono debe ser texto',
            'phone.max' => 'El teléfono no debe exceder 255 caracteres',
            'phone.regex' => 'El teléfono debe ser un número de teléfono válido',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe ser una dirección de email válida',
            'email.max' => 'El email no debe exceder 100 caracteres',
            'manager_name.required' => 'El nombre del gerente es requerido',
            'manager_name.string' => 'El nombre del gerente debe ser texto',
            'manager_name.max' => 'El nombre del gerente no debe exceder 100 caracteres',
        ];
    }

    public function create(array $data): Warehouse
    {
        return DB::transaction(function () use ($data) {
            return Warehouse::create($data);
        });
    }

    public function update(Warehouse $warehouse, array $data): Warehouse
    {
        return DB::transaction(function () use ($warehouse, $data) {
            $warehouse->update($data);
            return $warehouse->fresh();
        });
    }

    public function delete(Warehouse $warehouse): bool
    {
        return DB::transaction(function () use ($warehouse) {
            if ($warehouse->locations()->exists()) {
                throw new \Exception('No se puede eliminar el almacén porque tiene ubicaciones asociadas.');
            }

            if ($warehouse->movements()->exists()) {
                throw new \Exception('No se puede eliminar el almacén porque tiene movimientos asociados.');
            }

            return $warehouse->delete();
        });
    }

    /**
     * Cambia el estado activo/inactivo de un almacén
     *
     * @param Warehouse $warehouse
     * @return Warehouse
     */
    public function toggleStatus(Warehouse $warehouse): Warehouse
    {
        return DB::transaction(function () use ($warehouse) {
            $warehouse->status = $warehouse->status == 'active' ? 'inactive' : 'active';
            $warehouse->save();
            return $warehouse->fresh();
        });
    }

    /**
     * Obtiene un almacén con sus ubicaciones
     *
     * @param Warehouse $warehouse
     * @return Warehouse
     */
    public function getWarehouseWithLocations(Warehouse $warehouse): Warehouse
    {
        return $warehouse->load(['locations' => function ($query) {
            $query->orderBy('name');
        }]);
    }

    /**
     * Obtiene un almacén con sus movimientos
     *
     * @param Warehouse $warehouse
     * @return Warehouse
     */
    public function getWarehouseWithMovements(Warehouse $warehouse): Warehouse
    {
        return $warehouse->load(['movements' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);
    }
}
