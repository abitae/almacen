<?php

namespace App\Services;

use App\Models\Batch;
use Illuminate\Pagination\LengthAwarePaginator;

class BatchService
{
    protected Batch $model;

    public function __construct(Batch $model)
    {
        $this->model = $model;
    }

    public function getHeaders(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'product.name', 'label' => 'Producto'],
            ['key' => 'batch_number', 'label' => 'Número de Lote'],
            ['key' => 'warehouse.name', 'label' => 'Bodega'],
            ['key' => 'manufacturing_date', 'label' => 'Fecha de Fabricación'],
            ['key' => 'expiration_date', 'label' => 'Fecha de Vencimiento'],
            ['key' => 'quantity', 'label' => 'Cantidad'],
            ['key' => 'unit_price', 'label' => 'Precio Unitario'],
            ['key' => 'status', 'label' => 'Estado'],
            ['key' => 'notes', 'label' => 'Notas'],
        ];
    }

    public function getAll(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with('product', 'warehouse');

        // separa la logica de busqueda
        $query->when(!empty($filters['search']), function($q) use ($filters) {
            $searchTerm = '%' . $filters['search'] . '%';
            $q->where(function($subQuery) use ($searchTerm) {
                $subQuery->where('batch_number', 'like', $searchTerm)
                        ->orWhereHas('product', function($productQuery) use ($searchTerm) {
                            $productQuery->where('commercial_name', 'like', $searchTerm);
                        });
            });
        })
        ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
        ->when(!empty($filters['product_id']), fn($q) => $q->where('product_id', $filters['product_id']))
        ->when(!empty($filters['warehouse_id']), fn($q) => $q->where('warehouse_id', $filters['warehouse_id']))
        ->when(!empty($filters['expiration_date']), fn($q) => $q->whereDate('expiration_date', $filters['expiration_date']));

        return $query->paginate($perPage);
    }

    public function create(array $data): Batch
    {
        return $this->model->create($data);
    }

    public function update(Batch $batch, array $data): bool
    {
        return $batch->update($data);
    }

    public function delete(Batch $batch): bool
    {
        return $batch->delete();
    }

    public function getValidationRules(bool $isUpdate = false, ?int $id = null): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'batch_number' => 'required|string|max:50',
            'manufacturing_date' => 'required|date',
            'expiration_date' => 'required|date|after:today',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,expired,depleted',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function getValidationMessages(): array
    {
        return [
            'product_id.required' => 'El producto es requerido',
            'product_id.exists' => 'El producto seleccionado no existe',
            'warehouse_id.required' => 'La bodega es requerida',
            'warehouse_id.exists' => 'La bodega seleccionada no existe',
            'batch_number.required' => 'El número de lote es requerido',
            'batch_number.string' => 'El número de lote debe ser texto',
            'batch_number.max' => 'El número de lote no debe exceder 50 caracteres',
            'manufacturing_date.required' => 'La fecha de fabricación es requerida',
            'manufacturing_date.date' => 'La fecha de fabricación debe ser una fecha válida',
            'expiration_date.required' => 'La fecha de vencimiento es requerida',
            'expiration_date.date' => 'La fecha de vencimiento debe ser una fecha válida',
            'expiration_date.after' => 'La fecha de vencimiento debe ser posterior a hoy',
            'quantity.required' => 'La cantidad es requerida',
            'quantity.numeric' => 'La cantidad debe ser un número',
            'quantity.min' => 'La cantidad no puede ser negativa',
            'unit_price.required' => 'El precio unitario es requerido',
            'unit_price.numeric' => 'El precio unitario debe ser un número',
            'unit_price.min' => 'El precio unitario no puede ser negativo',
            'status.required' => 'El estado es requerido',
            'status.in' => 'El estado debe ser activo, vencido o agotado',
            'notes.string' => 'Las notas deben ser texto',
            'notes.max' => 'Las notas no deben exceder 1000 caracteres',
        ];
    }
}
