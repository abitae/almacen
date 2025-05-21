<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
class ProductLive extends Component
{
    use WithPagination;
    use WithFileUploads;
    use Toast;
    public $headers_products;
    public $search = '';
    public $perPage = 10;
    public Product $product;
    public $modal_show = false;
    public $modal_form = false;
    public $selectedTab = 'basic_info';
    public $categoryFiltro = '';
    public $brandFiltro = '';
    public $statusFiltro = '';
    public $internal_code = '';
    public $barcode = '';
    public $commercial_name = '';
    public $technical_name = '';
    public $presentation = '';
    public $primary_unit = '';
    public $secondary_unit = '';
    public $category_id = '';
    public $brand_id = '';
    public $supplier_id = '';
    public $minimum_stock = '';
    public $maximum_stock = '';
    public $purchase_price = '';
    public $sale_price = '';
    public $profit_margin = '';
    public $description = '';
    public $status = '';
    public $is_edit = false;
    public $images = [];
    public $tempImages = [];

    protected $productService;

    public function boot(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function mount()
    {
        $this->headers_products = $this->productService->getHeaders();
    }

    public function render()
    {
        $filters = [
            'search' => $this->search,
            'category' => $this->categoryFiltro,
            'brand' => $this->brandFiltro,
            'status' => $this->statusFiltro,
        ];

        $products = $this->productService->getProducts($filters, $this->perPage);
        $formData = $this->productService->getFormData();

        return view('livewire.product.product-live', [
            'products' => $products,
            'categories' => $formData['categories'],
            'brands' => $formData['brands'],
            'suppliers' => $formData['suppliers'],
        ]);
    }

    public function show($id)
    {
        $this->product = Product::findOrFail($id);
        $this->modal_show = true;
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $this->productService->delete($product);
        session()->flash('message', 'Producto eliminado correctamente');
    }

    public function new()
    {
        $this->reset([
            'internal_code', 'barcode', 'commercial_name', 'technical_name',
            'presentation', 'primary_unit', 'secondary_unit', 'category_id',
            'brand_id', 'supplier_id', 'minimum_stock', 'maximum_stock',
            'purchase_price', 'sale_price', 'profit_margin', 'status',
            'description', 'selectedTab', 'images', 'tempImages'
        ]);

        $this->resetErrorBag();
        $this->resetValidation();
        $this->selectedTab = 'basic_info';
        $this->is_edit = false;
        $this->modal_form = true;
    }

    public function edit($id)
    {
        $this->is_edit = true;
        $this->product = Product::findOrFail($id);

        $this->fill([
            'internal_code' => $this->product->internal_code,
            'barcode' => $this->product->barcode,
            'commercial_name' => $this->product->commercial_name,
            'technical_name' => $this->product->technical_name,
            'presentation' => $this->product->presentation,
            'primary_unit' => $this->product->primary_unit,
            'secondary_unit' => $this->product->secondary_unit,
            'category_id' => $this->product->category_id,
            'brand_id' => $this->product->brand_id,
            'supplier_id' => $this->product->supplier_id,
            'minimum_stock' => $this->product->minimum_stock,
            'maximum_stock' => $this->product->maximum_stock,
            'purchase_price' => $this->product->purchase_price,
            'sale_price' => $this->product->sale_price,
            'profit_margin' => $this->product->profit_margin,
            'status' => $this->product->status,
            'description' => $this->product->description,
        ]);

        $this->images = $this->product->images;
        $this->modal_form = true;
    }

    public function store()
    {
        $this->validate(
            $this->productService->getValidationRules(),
            $this->productService->getValidationMessages()
        );

        $data = $this->getFormData();
        $data['images'] = $this->tempImages;
        $product = $this->productService->create($data);
        if ($product) {
            $this->success('Producto creado correctamente');
            Log::info('Producto creado correctamente', ['product' => $product]);
            $this->modal_form = false;
        } else {
            Log::error('Error al crear el producto');
            $this->error('Error al crear el producto');
        }
    }

    public function update()
    {
        $this->validate(
            $this->productService->getValidationRules(true, $this->product->id),
            $this->productService->getValidationMessages()
        );

        $data = $this->getFormData();
        $data['images'] = $this->tempImages;
        $this->productService->update($this->product, $data);
        $this->modal_form = false;
    }

    public function removeImage($index)
    {
        if (isset($this->tempImages[$index])) {
            unset($this->tempImages[$index]);
            $this->tempImages = array_values($this->tempImages);
        }
    }

    public function deleteImage($imageId)
    {
        $image = ProductImage::findOrFail($imageId);

        // Verificar que la imagen pertenece al producto actual
        if ($image->product_id === $this->product->id) {
            $this->productService->deleteImage($image->image_path);
            $image->delete();

            // Actualizar la lista de imágenes
            $this->images = $this->product->images;

            session()->flash('message', 'Imagen eliminada correctamente');
        }
    }

    protected function getFormData(): array
    {
        return [
            'internal_code' => $this->internal_code,
            'barcode' => $this->barcode,
            'commercial_name' => $this->commercial_name,
            'technical_name' => $this->technical_name,
            'presentation' => $this->presentation,
            'primary_unit' => $this->primary_unit,
            'secondary_unit' => $this->secondary_unit,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'supplier_id' => $this->supplier_id,
            'minimum_stock' => $this->minimum_stock,
            'maximum_stock' => $this->maximum_stock,
            'purchase_price' => $this->purchase_price,
            'sale_price' => $this->sale_price,
            'profit_margin' => $this->profit_margin,
            'status' => $this->status,
            'description' => $this->description,
        ];
    }
}
