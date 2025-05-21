<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandService
{
    protected Brand $model;

    public function __construct(Brand $model)
    {
        $this->model = $model;
    }

    public function getHeaders(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'description', 'label' => 'Descripción'],
            ['key' => 'status', 'label' => 'Estado'],
            ['key' => 'products_count', 'label' => 'Productos'],
        ];
    }

    public function getAll(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->withCount('products');

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): Brand
    {
        return $this->model->create($data);
    }

    public function update(Brand $brand, array $data): bool
    {
        return $brand->update($data);
    }

    public function delete(Brand $brand): bool
    {
        return $brand->delete();
    }

    public function getValidationRules(bool $isUpdate = false, ?int $id = null): array
    {
        $uniqueRule = $isUpdate ? 'unique:brands,name,' . $id : 'unique:brands,name';

        return [
            'name' => 'required|string|max:255|' . $uniqueRule,
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function getValidationMessages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser texto',
            'name.max' => 'El nombre no debe exceder 255 caracteres',
            'name.unique' => 'Este nombre ya existe',
            'description.string' => 'La descripción debe ser texto',
            'description.max' => 'La descripción no debe exceder 1000 caracteres',
            'status.required' => 'El estado es requerido',
            'status.in' => 'El estado debe ser activo o inactivo',
        ];
    }
}
