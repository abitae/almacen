<?php

namespace App\Livewire\Batch;

use App\Models\Batch;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\BatchService;
use Livewire\Component;
use Livewire\WithPagination;

class BatchLive extends Component
{
    use WithPagination;

    public $headers;
    public $search = '';
    public $perPage = 10;
    public Batch $batch;
    public $modal_show = false;
    public $modal_form = false;
    public $product_id = '';
    public $warehouse_id = '';
    public $batch_number = '';
    public $manufacturing_date = '';
    public $expiration_date = '';
    public $quantity = '';
    public $unit_price = '';
    public $status = '';
    public $notes = '';
    public $is_edit = false;
    public $statusFiltro = '';
    public $productFiltro = '';
    public $warehouseFiltro = '';
    public $expirationDateFiltro = '';

    protected $batchService;

    public function boot(BatchService $batchService)
    {
        $this->batchService = $batchService;
    }

    public function mount()
    {
        $this->headers = $this->batchService->getHeaders();
    }

    public function render()
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFiltro,
            'product_id' => $this->productFiltro,
            'warehouse_id' => $this->warehouseFiltro,
            'expiration_date' => $this->expirationDateFiltro,
        ];

        $batches = $this->batchService->getAll($filters, $this->perPage);
        $products = Product::where('status', 'active')->get();
        $warehouses = Warehouse::where('status', 'active')->get();
        return view('livewire.batch.batch-live', [
            'batches' => $batches,
            'products' => $products,
            'warehouses' => $warehouses,
        ]);
    }

    public function show($id)
    {
        $this->batch = Batch::with('product')->findOrFail($id);
        $this->modal_show = true;
    }

    public function delete($id)
    {
        $batch = Batch::findOrFail($id);
        $this->batchService->delete($batch);
        session()->flash('message', 'Lote eliminado correctamente');
    }

    public function new()
    {
        $this->reset(['product_id', 'batch_number', 'expiration_date', 'quantity', 'status', 'notes', 'warehouse_id', 'unit_price']);
        $this->resetErrorBag();
        $this->resetValidation();
        $this->is_edit = false;
        $this->modal_form = true;
    }

    public function edit($id)
    {

        $this->is_edit = true;
        $this->batch = Batch::findOrFail($id);

        $this->fill([
            'product_id' => $this->batch->product_id,
            'batch_number' => $this->batch->batch_number,
            'manufacturing_date' => $this->batch->manufacturing_date,
            'expiration_date' => $this->batch->expiration_date,
            'quantity' => $this->batch->quantity,
            'status' => $this->batch->status,
            'notes' => $this->batch->notes,
            'warehouse_id' => $this->batch->warehouse_id,
            'unit_price' => $this->batch->unit_price,
        ]);

        $this->modal_form = true;
    }

    public function store()
    {
        $this->validate(
            $this->batchService->getValidationRules(),
            $this->batchService->getValidationMessages()
        );

        $data = [
            'product_id' => $this->product_id,
            'batch_number' => $this->batch_number,
            'manufacturing_date' => $this->manufacturing_date,
            'expiration_date' => $this->expiration_date,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'notes' => $this->notes,
            'warehouse_id' => $this->warehouse_id,
            'unit_price' => $this->unit_price,
        ];

        $this->batchService->create($data);
        $this->modal_form = false;
        session()->flash('message', 'Lote creado correctamente');
    }

    public function update()
    {
        $this->validate(
            $this->batchService->getValidationRules(true, $this->batch->id),
            $this->batchService->getValidationMessages()
        );

        $data = [
            'product_id' => $this->product_id,
            'batch_number' => $this->batch_number,
            'manufacturing_date' => $this->manufacturing_date,
            'expiration_date' => $this->expiration_date,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'notes' => $this->notes,
            'warehouse_id' => $this->warehouse_id,
            'unit_price' => $this->unit_price,
        ];

        $this->batchService->update($this->batch, $data);
        $this->modal_form = false;
    }
}
