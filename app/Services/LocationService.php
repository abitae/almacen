<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class LocationService
{
    public function getHeaders(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'code', 'label' => 'Código'],
            ['key' => 'warehouse.name', 'label' => 'Almacén'],
            ['key' => 'type', 'label' => 'Tipo'],
            ['key' => 'status', 'label' => 'Estado'],
        ];
    }

    public function getWarehouses()
    {
        return Warehouse::where('status', 'active')->get();
    }

    public function getLocations(array $filters, int $perPage)
    {
        $query = Location::query()
            ->with('warehouse');
        if (!empty($filters['search'])) {
            $query->when(!empty($filters['search']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('code', 'like', '%' . $filters['search'] . '%');
                });
            });
        }
        if (!empty($filters['warehouseFilter']) && is_numeric($filters['warehouseFilter'])) {
            $query->where('warehouse_id', $filters['warehouseFilter']);
        }
        if (!empty($filters['statusFilter'])) {
            $query->where('status', $filters['statusFilter']);
        }
        $query->orderBy($filters['sortField'], $filters['sortDirection']);
        return $query->paginate($perPage);
    }

    public function getValidationRules(bool $isUpdate = false, ?int $locationId = null): array
    {
        $uniqueRule = $isUpdate
            ? 'unique:locations,code,' . $locationId
            : 'unique:locations,code';

        return [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|' . $uniqueRule,
            'warehouse_id' => 'required|exists:warehouses,id',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:warehouse,storage',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function getValidationMessages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser texto',
            'name.max' => 'El nombre no debe exceder 100 caracteres',
            'code.required' => 'El código es requerido',
            'code.string' => 'El código debe ser texto',
            'code.max' => 'El código no debe exceder 50 caracteres',
            'code.unique' => 'El código ya existe en otra ubicación',
            'warehouse_id.required' => 'El almacén es requerido',
            'warehouse_id.exists' => 'El almacén seleccionado no es válido',
            'description.string' => 'La descripción debe ser texto',
            'description.max' => 'La descripción no debe exceder 255 caracteres',
            'status.required' => 'El estado es requerido',
            'status.in' => 'El estado debe ser active o inactive',
            'type.required' => 'El tipo es requerido',
            'type.in' => 'El tipo debe ser warehouse o storage',
        ];
    }
    public function create(array $data): Location
    {
        return DB::transaction(function () use ($data) {
            return Location::create($data);
        });
    }

    public function update(Location $location, array $data): Location
    {
        return DB::transaction(function () use ($location, $data) {
            $location->update($data);
            return $location;
        });
    }

    public function delete(Location $location): bool
    {
        return DB::transaction(function () use ($location) {
            // Verificar si la ubicación tiene movimientos asociados
            if ($location->movements()->exists()) {
                throw new \Exception('No se puede eliminar la ubicación porque tiene movimientos asociados.');
            }

            return $location->delete();
        });
    }

    public function toggleStatus(Location $location): Location
    {
        return DB::transaction(function () use ($location) {
            $location->is_active = !$location->is_active;
            $location->save();
            return $location;
        });
    }

    public function getLocationWithMovements(Location $location)
    {
        return $location->load('movements');
    }

    public function getLocationsByWarehouse($warehouseId)
    {
        return Location::where('warehouse_id', $warehouseId)
            ->where('is_active', true)
            ->get();
    }
}
