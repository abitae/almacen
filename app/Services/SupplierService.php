<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierService
{
    protected Supplier $model;

    public function __construct(Supplier $model)
    {
        $this->model = $model;
    }

    public function getHeaders(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'contact_name', 'label' => 'Contacto'],
            ['key' => 'phone', 'label' => 'Teléfono'],
            ['key' => 'email', 'label' => 'Email'],
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
                  ->orWhere('contact_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): Supplier
    {
        return $this->model->create($data);
    }

    public function update(Supplier $supplier, array $data): bool
    {
        return $supplier->update($data);
    }

    public function delete(Supplier $supplier): bool
    {
        return $supplier->delete();
    }

    public function getValidationRules(bool $isUpdate = false, ?int $id = null): array
    {
        $uniqueRule = $isUpdate ? 'unique:suppliers,name,' . $id : 'unique:suppliers,name';
        $uniqueEmailRule = $isUpdate ? 'unique:suppliers,email,' . $id : 'unique:suppliers,email';

        return [
            'name' => 'required|string|max:255|' . $uniqueRule,
            'contact_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|' . $uniqueEmailRule,
            'address' => 'nullable|string|max:500',
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
            'contact_name.required' => 'El nombre del contacto es requerido',
            'contact_name.string' => 'El nombre del contacto debe ser texto',
            'contact_name.max' => 'El nombre del contacto no debe exceder 255 caracteres',
            'phone.required' => 'El teléfono es requerido',
            'phone.string' => 'El teléfono debe ser texto',
            'phone.max' => 'El teléfono no debe exceder 20 caracteres',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe ser válido',
            'email.max' => 'El email no debe exceder 255 caracteres',
            'email.unique' => 'Este email ya está registrado',
            'address.string' => 'La dirección debe ser texto',
            'address.max' => 'La dirección no debe exceder 500 caracteres',
            'status.required' => 'El estado es requerido',
            'status.in' => 'El estado debe ser activo o inactivo',
        ];
    }
}
