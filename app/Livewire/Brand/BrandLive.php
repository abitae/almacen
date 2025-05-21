<?php

namespace App\Livewire\Brand;

use App\Models\Brand;
use App\Services\BrandService;
use Livewire\Component;
use Livewire\WithPagination;

class BrandLive extends Component
{
    use WithPagination;

    public $headers;
    public $search = '';
    public $perPage = 10;
    public Brand $brand;
    public $modal_show = false;
    public $modal_form = false;
    public $name = '';
    public $description = '';
    public $status = '';
    public $is_edit = false;
    public $statusFiltro = '';

    protected $brandService;

    public function boot(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function mount()
    {
        $this->headers = $this->brandService->getHeaders();
    }

    public function render()
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFiltro,
        ];

        $brands = $this->brandService->getAll($filters, $this->perPage);

        return view('livewire.brand.brand-live', [
            'brands' => $brands,
        ]);
    }

    public function show($id)
    {
        $this->brand = Brand::findOrFail($id);
        $this->modal_show = true;
    }

    public function delete($id)
    {
        $brand = Brand::findOrFail($id);
        $this->brandService->delete($brand);
        session()->flash('message', 'Marca eliminada correctamente');
    }

    public function new()
    {
        $this->reset(['name', 'description', 'status']);
        $this->resetErrorBag();
        $this->resetValidation();
        $this->is_edit = false;
        $this->modal_form = true;
    }

    public function edit($id)
    {
        $this->is_edit = true;
        $this->brand = Brand::findOrFail($id);

        $this->fill([
            'name' => $this->brand->name,
            'description' => $this->brand->description,
            'status' => $this->brand->status,
        ]);

        $this->modal_form = true;
    }

    public function store()
    {
        $this->validate(
            $this->brandService->getValidationRules(),
            $this->brandService->getValidationMessages()
        );

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
        ];

        $this->brandService->create($data);
        $this->modal_form = false;
        session()->flash('message', 'Marca creada correctamente');
    }

    public function update()
    {
        $this->validate(
            $this->brandService->getValidationRules(true, $this->brand->id),
            $this->brandService->getValidationMessages()
        );

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
        ];

        $this->brandService->update($this->brand, $data);
        $this->modal_form = false;
        session()->flash('message', 'Marca actualizada correctamente');
    }
}
