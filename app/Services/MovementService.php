<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Movement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class MovementService
{
    public function getHeaders(): array
    {
        return [
            'created_at' => 'Fecha',
            'type' => 'Tipo',
            'reference' => 'Referencia',
            'description' => 'Descripción',
            'warehouse' => 'Almacén',
            'location' => 'Ubicación',
            'destination_warehouse' => 'Almacén Destino',
            'destination_location' => 'Ubicación Destino',
            'quantity' => 'Cantidad',
            'unit_price' => 'Precio Unitario',
            'total' => 'Total',
        ];
    }
    public function getMovements()
    {
        return Movement::paginate(10);
    }

    public function getWarehouses() {
        return Warehouse::all();
    }

    public function getLocationsByWarehouse($warehouseId)
    {
        return Location::where('warehouse_id', $warehouseId)->get();
    }

    public function create(array $data): Movement
    {
        return DB::transaction(function () use ($data) {
            $movement = Movement::create($data);

            // Actualizar el stock según el tipo de movimiento
            $this->updateStock($movement);

            return $movement;
        });
    }

    public function update(Movement $movement, array $data): Movement
    {
        return DB::transaction(function () use ($movement, $data) {
            // Revertir el stock anterior
            $this->revertStock($movement);

            // Actualizar el movimiento
            $movement->update($data);

            // Actualizar el nuevo stock
            $this->updateStock($movement);

            return $movement;
        });
    }

    public function delete(Movement $movement): bool
    {
        return DB::transaction(function () use ($movement) {
            // Revertir el stock
            $this->revertStock($movement);

            return $movement->delete();
        });
    }

    protected function updateStock(Movement $movement)
    {
        switch ($movement->type) {
            case 'entry':
                $this->addStock($movement);
                break;
            case 'exit':
                $this->subtractStock($movement);
                break;
            case 'transfer':
                $this->transferStock($movement);
                break;
        }
    }

    protected function revertStock(Movement $movement)
    {
        switch ($movement->type) {
            case 'entry':
                $this->subtractStock($movement);
                break;
            case 'exit':
                $this->addStock($movement);
                break;
            case 'transfer':
                $this->revertTransfer($movement);
                break;
        }
    }

    protected function addStock(Movement $movement)
    {
        $location = $movement->location;
        $location->stock += $movement->quantity;
        $location->save();
    }

    protected function subtractStock(Movement $movement)
    {
        $location = $movement->location;
        if ($location->stock < $movement->quantity) {
            throw new \Exception('No hay suficiente stock disponible.');
        }
        $location->stock -= $movement->quantity;
        $location->save();
    }

    protected function transferStock(Movement $movement)
    {
        // Restar del origen
        $this->subtractStock($movement);

        // Sumar al destino
        $destinationLocation = $movement->destinationLocation;
        $destinationLocation->stock += $movement->quantity;
        $destinationLocation->save();
    }

    protected function revertTransfer(Movement $movement)
    {
        // Restar del destino
        $destinationLocation = $movement->destinationLocation;
        if ($destinationLocation->stock < $movement->quantity) {
            throw new \Exception('No hay suficiente stock disponible en la ubicación destino.');
        }
        $destinationLocation->stock -= $movement->quantity;
        $destinationLocation->save();

        // Sumar al origen
        $this->addStock($movement);
    }

    public function getMovementsByWarehouse($warehouseId)
    {
        return Movement::where('warehouse_id', $warehouseId)
            ->orWhere('destination_warehouse_id', $warehouseId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getMovementsByLocation($locationId)
    {
        return Movement::where('location_id', $locationId)
            ->orWhere('destination_location_id', $locationId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
