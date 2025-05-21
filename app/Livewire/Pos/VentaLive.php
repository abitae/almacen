<?php

namespace App\Livewire\Pos;

use App\Models\Batch;
use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\Customer;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

class VentaLive extends Component
{
    use Toast;
    public $categorias;
    public $categoriaFiltro = null;
    public $warehouses;
    public $warehouseFiltro = null;
    public $searchFiltro = '';
    public $filtroStock = false;
    public $filtroLotes = false;

    // Variables para el modal de cantidad
    public $modal_cantidad = false;
    public $confirmar_venta = false;
    public $productoSeleccionado = null;
    public $cantidad = 1;
    public $loteSeleccionado = null;

    // Variables para el carrito
    public $carrito = [];
    public $total = 0;
    public $iva = 0;
    public $subtotal = 0;
    // Variables para la venta
    public $cliente_id = null;
    public $metodo_pago = 1;
    public $tipo_recibo = 1;

    // Variables cliente
    public Customer $cliente;
    public $clientes;
    public function mount()
    {
        $this->categorias = Category::with('products.batches')->get();
        $this->warehouses = Warehouse::where('status', 'active')->get();
        $this->clientes = Customer::all();
        $this->actualizarProductos();
    }

    public function abrirModalCantidad($loteId)
    {
        $this->loteSeleccionado = Batch::find($loteId);
        $this->cantidad = 1;
        $this->modal_cantidad = true;
    }

    public function agregarAlCarrito()
    {
        if (!$this->loteSeleccionado) return;

        $item = [
            'id' => $this->loteSeleccionado->product->id,
            'nombre' => $this->loteSeleccionado->product->commercial_name,
            'precio' => $this->loteSeleccionado->unit_price,
            'cantidad' => $this->cantidad,
            'presentation' => $this->loteSeleccionado->product->presentation,
            'lote_id' => $this->loteSeleccionado,
            'subtotal' => $this->loteSeleccionado->unit_price * $this->cantidad
        ];
        // Verificar si hay suficiente stock
        if ($this->loteSeleccionado->quantity < $this->cantidad) {
            $this->warning('No hay suficiente stock disponible. Stock actual: ' . $this->loteSeleccionado->quantity);
            $this->modal_cantidad = false;
            $this->loteSeleccionado = null;
            $this->cantidad = 1;
            return;
        }
        // Verificar si el producto ya existe en el carrito
        $existe = false;
        foreach ($this->carrito as $key => $carritoItem) {
            if ($carritoItem['id'] === $item['id'] || $carritoItem['lote_id'] === $item['lote_id']) {

                // Verificar si hay suficiente stock antes de agregar más cantidad
                if ($this->loteSeleccionado->quantity < ($this->carrito[$key]['cantidad'] + $item['cantidad'])) {
                    $this->warning('No hay suficiente stock disponible. Stock actual: ' . $this->loteSeleccionado->quantity);
                    session()->flash('error', 'No hay suficiente stock. Stock actual: ' . $this->loteSeleccionado->quantity);
                    return;
                }
                $this->carrito[$key]['cantidad'] += $item['cantidad'];
                $this->carrito[$key]['subtotal'] = $this->carrito[$key]['cantidad'] * $this->carrito[$key]['precio'];
                $existe = true;
                break;
            }
        }

        if (!$existe) {
            $this->carrito[] = $item;
        }
        $this->calcularTotales();

        $this->success('Producto agregado al carrito');
        $this->modal_cantidad = false;
        $this->loteSeleccionado = null;
        $this->cantidad = 1;
        $this->loteSeleccionado = null;
    }
    public function calcularTotales()
    {
        $this->total = collect($this->carrito)->sum('subtotal');
        $this->iva = $this->total * 0.18; // 18% IVA incluido
        $this->subtotal = $this->total - $this->iva; // El total es igual al subtotal ya que el IVA está incluido
    }
    public function limpiarCarrito()
    {
        $this->carrito = [];
        $this->calcularTotales();
    }
    public function eliminarDelCarrito($productId)
    {
        $this->carrito = array_filter($this->carrito, function ($item) use ($productId) {
            return $item['id'] !== $productId;
        });
        $this->calcularTotales();
    }

    public function actualizarProductos()
    {
        $query = Product::with(['batches' => function ($query) {
            $query->whereHas('warehouse', function ($q) {
                $q->where('status', 'active');
            });
            if ($this->filtroStock) {
                $query->where('quantity', '>', 0);
            }
            if ($this->warehouseFiltro) {
                $query->where('warehouse_id', $this->warehouseFiltro);
            }
        }]);

        if ($this->filtroLotes) {
            $query->whereHas('batches', function ($q) {
                $q->whereHas('warehouse', function ($q) {
                    $q->where('status', 'active');
                });
                if ($this->filtroStock) {
                    $q->where('quantity', '>', 0);
                }
                if ($this->warehouseFiltro) {
                    $q->where('warehouse_id', $this->warehouseFiltro);
                }
            });
        }

        if ($this->categoriaFiltro) {
            $query->where('category_id', $this->categoriaFiltro);
        }

        if ($this->searchFiltro) {
            $query->where(function ($q) {
                $q->where('commercial_name', 'like', '%' . $this->searchFiltro . '%')
                    ->orWhere('technical_name', 'like', '%' . $this->searchFiltro . '%')
                    ->orWhere('barcode', 'like', '%' . $this->searchFiltro . '%')
                    ->orWhereHas('category', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchFiltro . '%');
                    });
            });
        }

        return $query->take(12)->get();
    }
    public function procesarVenta()
    {
        $this->confirmar_venta = true;
    }
    public function confirmarVenta()
    {
        $this->success('Venta procesada correctamente');
        $this->confirmar_venta = false;
    }
    public function filtrarPorCategoria($categoriaId)
    {
        $this->categoriaFiltro = $categoriaId;
    }
    public function render()
    {
        $products = $this->actualizarProductos();

        return view('livewire.pos.venta-live', [
            'products' => $products,
        ])->layout('components.layouts.pos');
    }
    public function updatedClienteId($value)
    {
        if ($value) {
            $this->cliente = Customer::find($value);
        }
    }
}
