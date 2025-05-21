<?php

namespace App\Livewire\Supplier;

use App\Models\Supplier;
use App\Services\SupplierService;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierLive extends Component
{
    use WithPagination;

    public $headers;
    public $search = '';
    public $perPage = 10;
    public Supplier $supplier;
    public $modal_show = false;
    public $modal_form = false;
    public $name = '';
    public $contact_name = '';
    public $phone = '';
    public $email = '';
    public $address = '';
    public $status = '';
    public $is_edit = false;
    public $statusFiltro = '';

    protected $supplierService;

    public function boot(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function mount()
    {
        $this->headers = $this->supplierService->getHeaders();
    }

    public function render()
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFiltro,
        ];

        $suppliers = $this->supplierService->getAll($filters, $this->perPage);

        return view('livewire.supplier.supplier-live', [
            'suppliers' => $suppliers,
        ]);
    }

    public function show($id)
    {
        $this->supplier = Supplier::findOrFail($id);
        $this->modal_show = true;
    }

    public function delete($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplierService->delete($supplier);
        session()->flash('message', 'Proveedor eliminado correctamente');
    }

    public function new()
    {
        $this->reset(['name', 'contact_name', 'phone', 'email', 'address', 'status']);
        $this->resetErrorBag();
        $this->resetValidation();
        $this->is_edit = false;
        $this->modal_form = true;
    }

    public function edit($id)
    {
        $this->is_edit = true;
        $this->supplier = Supplier::findOrFail($id);

        $this->fill([
            'name' => $this->supplier->name,
            'contact_name' => $this->supplier->contact_name,
            'phone' => $this->supplier->phone,
            'email' => $this->supplier->email,
            'address' => $this->supplier->address,
            'status' => $this->supplier->status,
        ]);

        $this->modal_form = true;
    }

    public function store()
    {
        $this->validate(
            $this->supplierService->getValidationRules(),
            $this->supplierService->getValidationMessages()
        );

        $data = [
            'name' => $this->name,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'status' => $this->status,
        ];

        $this->supplierService->create($data);
        $this->modal_form = false;
        session()->flash('message', 'Proveedor creado correctamente');
    }

    public function update()
    {
        $this->validate(
            $this->supplierService->getValidationRules(true, $this->supplier->id),
            $this->supplierService->getValidationMessages()
        );

        $data = [
            'name' => $this->name,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'status' => $this->status,
        ];

        $this->supplierService->update($this->supplier, $data);
        $this->modal_form = false;
        session()->flash('message', 'Proveedor actualizado correctamente');
    }
}
