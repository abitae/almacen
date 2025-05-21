<?php

namespace App\Livewire\Category;

use App\Models\Category;
use App\Services\CategoryService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;
use Illuminate\Contracts\View\Factory;

class CategoryLive extends Component
{
    use WithPagination;

    // Propiedades públicas
    public array $headers = [];
    public string $search = '';
    public int $perPage = 10;
    public ?Category $category = null;
    public bool $modal_show = false;
    public bool $modal_form = false;
    public string $name = '';
    public string $description = '';
    public string $status = '';
    public bool $is_edit = false;
    public string $statusFiltro = '';

    // Propiedades protegidas
    protected CategoryService $categoryService;

    /**
     * Inicializa el servicio de categorías
     */
    public function boot(CategoryService $categoryService): void
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Monta el componente y establece los encabezados
     */
    public function mount(): void
    {
        $this->headers = $this->categoryService->getHeaders();
    }

    /**
     * Renderiza la vista del componente
     */
    public function render(): View|Factory
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFiltro,
        ];

        $categories = $this->categoryService->getAll($filters, $this->perPage);

        return view('livewire.category.category-live', [
            'categories' => $categories,
        ]);
    }

    /**
     * Muestra los detalles de una categoría
     */
    public function show(int $id): void
    {
        $this->category = Category::withCount('products')->findOrFail($id);
        $this->modal_show = true;
    }

    /**
     * Elimina una categoría
     */
    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->categoryService->delete($category);
        session()->flash('message', 'Categoría eliminada correctamente');
    }

    /**
     * Prepara el formulario para crear una nueva categoría
     */
    public function new(): void
    {
        $this->reset(['name', 'description', 'status']);
        $this->resetErrorBag();
        $this->resetValidation();
        $this->is_edit = false;
        $this->modal_form = true;
    }

    /**
     * Prepara el formulario para editar una categoría existente
     */
    public function edit(int $id): void
    {
        $this->is_edit = true;
        $this->category = Category::findOrFail($id);

        $this->fill([
            'name' => $this->category->name,
            'description' => $this->category->description,
            'status' => $this->category->status,
        ]);

        $this->modal_form = true;
    }

    /**
     * Almacena una nueva categoría
     */
    public function store(): void
    {
        $this->validate(
            $this->categoryService->getValidationRules(),
            $this->categoryService->getValidationMessages()
        );

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
        ];

        $this->categoryService->create($data);
        $this->modal_form = false;
        session()->flash('message', 'Categoría creada correctamente');
    }

    /**
     * Actualiza una categoría existente
     */
    public function update(): void
    {
        $this->validate(
            $this->categoryService->getValidationRules(true, $this->category->id),
            $this->categoryService->getValidationMessages()
        );

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
        ];

        $this->categoryService->update($this->category, $data);
        $this->modal_form = false;
        session()->flash('message', 'Categoría actualizada correctamente');
    }
}
