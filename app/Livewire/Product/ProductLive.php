<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class ProductLive extends Component
{
    use WithPagination;

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

    public function mount()
    {
        $this->headers_products = [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'code', 'label' => 'Código Interno'],
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'category.name', 'label' => 'Categoria'],
            ['key' => 'brand.name', 'label' => 'Marca'],
            ['key' => 'presentation', 'label' => 'Presentación'],
            ['key' => 'unidad_medida', 'label' => 'Unidad Medida'],
        ];
    }

    public function render()
    {
        $query = Product::query();
        if ($this->search) {
            $query->with(['category', 'brand'])
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('internal_code', 'like', '%' . $this->search . '%')
                            ->orWhere('barcode', 'like', '%' . $this->search . '%')
                            ->orWhere('commercial_name', 'like', '%' . $this->search . '%')
                            ->orWhere('technical_name', 'like', '%' . $this->search . '%');
                    });
                });
        }
        if ($this->categoryFiltro) {
            $query->where('category_id', $this->categoryFiltro);
        }
        if ($this->brandFiltro) {
            $query->where('brand_id', $this->brandFiltro);
        }
        if ($this->statusFiltro) {
            $query->where('status', $this->statusFiltro);
        }
        $products = $query->paginate($this->perPage);

        return view('livewire.product.product-live', [
            'products' => $products,
            'categories' => Category::all(),
            'brands' => Brand::all(),
            'suppliers' => Supplier::all(),
        ]);
    }
    public function show($id)
    {
        $this->product = Product::findOrFail($id);
        $this->modal_show = true;
    }
    public function export()
    {
        //return Excel::download(new ProductsExport, 'productos.xlsx');
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        session()->flash('message', 'Producto eliminado correctamente');
    }
    public function new()
    {
        // Limpiar todas las variables del formulario
        $this->reset([
            'internal_code',
            'barcode',
            'commercial_name',
            'technical_name',
            'presentation',
            'primary_unit',
            'secondary_unit',
            'category_id',
            'brand_id',
            'supplier_id',
            'minimum_stock',
            'maximum_stock',
            'purchase_price',
            'sale_price',
            'profit_margin',
            'status',
            'description',
            'selectedTab'
        ]);
        // Reiniciar los errores de validación
        $this->resetErrorBag();
        $this->resetValidation();
        // Establecer la pestaña inicial
        $this->selectedTab = 'basic_info';
        $this->is_edit = false;
        $this->modal_form = true;
    }
    public function edit($id)
    {
        $this->is_edit = true;
        $this->product = Product::findOrFail($id);
        $this->internal_code = $this->product->internal_code;
        $this->barcode = $this->product->barcode;
        $this->commercial_name = $this->product->commercial_name;
        $this->technical_name = $this->product->technical_name;
        $this->presentation = $this->product->presentation;
        $this->primary_unit = $this->product->primary_unit;
        $this->secondary_unit = $this->product->secondary_unit;
        $this->category_id = $this->product->category_id;
        $this->brand_id = $this->product->brand_id;
        $this->supplier_id = $this->product->supplier_id;
        $this->minimum_stock = $this->product->minimum_stock;
        $this->maximum_stock = $this->product->maximum_stock;
        $this->purchase_price = $this->product->purchase_price;
        $this->sale_price = $this->product->sale_price;
        $this->profit_margin = $this->product->profit_margin;
        $this->status = $this->product->status;
        $this->description = $this->product->description;
        $this->modal_form = true;
    }
    public function store()
    {
        $rules = [
            'internal_code' => 'required|string|unique:products,internal_code',
            'barcode' => 'nullable|string|unique:products,barcode',
            'commercial_name' => 'required|string|max:255',
            'technical_name' => 'nullable|string|max:255',
            'presentation' => 'required|string|max:100',
            'primary_unit' => 'required|string|max:50',
            'secondary_unit' => 'nullable|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'minimum_stock' => 'required|numeric|min:0',
            'maximum_stock' => 'nullable|numeric|min:0|gte:minimum_stock',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0|gte:purchase_price',
            'profit_margin' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,discontinued',
            'description' => 'nullable|string|max:1000',
        ];

        $messages = [
            'internal_code.required' => 'El código interno es requerido',
            'internal_code.unique' => 'El código interno ya existe en otro producto',
            'internal_code.string' => 'El código interno debe ser texto',

            'barcode.unique' => 'El código de barras ya existe en otro producto',
            'barcode.string' => 'El código de barras debe ser texto',

            'commercial_name.required' => 'El nombre comercial es requerido',
            'commercial_name.string' => 'El nombre comercial debe ser texto',
            'commercial_name.max' => 'El nombre comercial no debe exceder 255 caracteres',

            'technical_name.string' => 'El nombre técnico debe ser texto',
            'technical_name.max' => 'El nombre técnico no debe exceder 255 caracteres',

            'presentation.required' => 'La presentación es requerida',
            'presentation.string' => 'La presentación debe ser texto',
            'presentation.max' => 'La presentación no debe exceder 100 caracteres',

            'primary_unit.required' => 'La unidad primaria es requerida',
            'primary_unit.string' => 'La unidad primaria debe ser texto',
            'primary_unit.max' => 'La unidad primaria no debe exceder 50 caracteres',

            'secondary_unit.string' => 'La unidad secundaria debe ser texto',
            'secondary_unit.max' => 'La unidad secundaria no debe exceder 50 caracteres',

            'category_id.required' => 'La categoría es requerida',
            'category_id.exists' => 'La categoría seleccionada no existe',

            'brand_id.required' => 'La marca es requerida',
            'brand_id.exists' => 'La marca seleccionada no existe',

            'supplier_id.required' => 'El proveedor es requerido',
            'supplier_id.exists' => 'El proveedor seleccionado no existe',

            'minimum_stock.required' => 'El stock mínimo es requerido',
            'minimum_stock.numeric' => 'El stock mínimo debe ser un número',
            'minimum_stock.min' => 'El stock mínimo no puede ser negativo',

            'maximum_stock.numeric' => 'El stock máximo debe ser un número',
            'maximum_stock.min' => 'El stock máximo no puede ser negativo',
            'maximum_stock.gte' => 'El stock máximo debe ser mayor o igual al stock mínimo',

            'purchase_price.required' => 'El precio de compra es requerido',
            'purchase_price.numeric' => 'El precio de compra debe ser un número',
            'purchase_price.min' => 'El precio de compra no puede ser negativo',

            'sale_price.required' => 'El precio de venta es requerido',
            'sale_price.numeric' => 'El precio de venta debe ser un número',
            'sale_price.min' => 'El precio de venta no puede ser negativo',
            'sale_price.gte' => 'El precio de venta debe ser mayor o igual al precio de compra',

            'profit_margin.required' => 'El margen de ganancia es requerido',
            'profit_margin.numeric' => 'El margen de ganancia debe ser un número',
            'profit_margin.min' => 'El margen de ganancia no puede ser negativo',
            'profit_margin.max' => 'El margen de ganancia no puede ser mayor a 100%',

            'status.required' => 'El estado es requerido',
            'status.in' => 'El estado debe ser activo, inactivo o descontinuado',

            'description.string' => 'La descripción debe ser texto',
            'description.max' => 'La descripción no debe exceder 1000 caracteres',
        ];
        $this->validate($rules, $messages);
        $this->product = new Product();
        $this->product->internal_code = $this->internal_code;
        $this->product->barcode = $this->barcode;
        $this->product->commercial_name = $this->commercial_name;
        $this->product->technical_name = $this->technical_name;
        $this->product->presentation = $this->presentation;
        $this->product->primary_unit = $this->primary_unit;
        $this->product->secondary_unit = $this->secondary_unit;
        $this->product->category_id = $this->category_id;
        $this->product->brand_id = $this->brand_id;
        $this->product->supplier_id = $this->supplier_id;
        $this->product->minimum_stock = $this->minimum_stock;
        $this->product->maximum_stock = $this->maximum_stock;
        $this->product->purchase_price = $this->purchase_price;
        $this->product->sale_price = $this->sale_price;
        $this->product->profit_margin = $this->profit_margin;
        $this->product->status = $this->status;
        $this->product->description = $this->description;
        $this->product->save();
        $this->modal_form = false;
    }
    public function update()
    {
        $rules = [
            'internal_code' => 'required|string|unique:products,internal_code,' . $this->product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $this->product->id,
            'commercial_name' => 'required|string|max:255',
            'technical_name' => 'nullable|string|max:255',
            'presentation' => 'required|string|max:100',
            'primary_unit' => 'required|string|max:50',
            'secondary_unit' => 'nullable|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'minimum_stock' => 'required|numeric|min:0',
            'maximum_stock' => 'nullable|numeric|min:0|gte:minimum_stock',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0|gte:purchase_price',
            'profit_margin' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,discontinued',
            'description' => 'nullable|string|max:1000',
        ];
        $messages = [
            'internal_code.required' => 'El código interno es requerido',
            'internal_code.unique' => 'El código interno ya existe en otro producto',
            'internal_code.string' => 'El código interno debe ser texto',
            'barcode.unique' => 'El código de barras ya existe en otro producto',
            'barcode.string' => 'El código de barras debe ser texto',
            'commercial_name.required' => 'El nombre comercial es requerido',
            'commercial_name.string' => 'El nombre comercial debe ser texto',
            'commercial_name.max' => 'El nombre comercial no debe exceder 255 caracteres',
            'technical_name.string' => 'El nombre técnico debe ser texto',
            'technical_name.max' => 'El nombre técnico no debe exceder 255 caracteres',
            'presentation.required' => 'La presentación es requerida',
            'presentation.string' => 'La presentación debe ser texto',
            'presentation.max' => 'La presentación no debe exceder 100 caracteres',
            'primary_unit.required' => 'La unidad primaria es requerida',
            'primary_unit.string' => 'La unidad primaria debe ser texto',
            'primary_unit.max' => 'La unidad primaria no debe exceder 50 caracteres',
            'secondary_unit.string' => 'La unidad secundaria debe ser texto',
            'secondary_unit.max' => 'La unidad secundaria no debe exceder 50 caracteres',
            'category_id.required' => 'La categoría es requerida',
            'category_id.exists' => 'La categoría seleccionada no existe',
            'brand_id.required' => 'La marca es requerida',
            'brand_id.exists' => 'La marca seleccionada no existe',
            'supplier_id.required' => 'El proveedor es requerido',
            'supplier_id.exists' => 'El proveedor seleccionado no existe',
            'minimum_stock.required' => 'El stock mínimo es requerido',
            'minimum_stock.numeric' => 'El stock mínimo debe ser un número',
            'minimum_stock.min' => 'El stock mínimo no puede ser negativo',
            'maximum_stock.numeric' => 'El stock máximo debe ser un número',
            'maximum_stock.min' => 'El stock máximo no puede ser negativo',
            'maximum_stock.gte' => 'El stock máximo debe ser mayor o igual al stock mínimo',
            'purchase_price.required' => 'El precio de compra es requerido',
            'purchase_price.numeric' => 'El precio de compra debe ser un número',
            'purchase_price.min' => 'El precio de compra no puede ser negativo',
            'sale_price.required' => 'El precio de venta es requerido',
            'sale_price.numeric' => 'El precio de venta debe ser un número',
            'sale_price.min' => 'El precio de venta no puede ser negativo',
            'sale_price.gte' => 'El precio de venta debe ser mayor o igual al precio de compra',
            'profit_margin.required' => 'El margen de ganancia es requerido',
            'profit_margin.numeric' => 'El margen de ganancia debe ser un número',
            'profit_margin.min' => 'El margen de ganancia no puede ser negativo',
            'profit_margin.max' => 'El margen de ganancia no puede ser mayor a 100%',
            'status.required' => 'El estado es requerido',
            'status.in' => 'El estado debe ser activo, inactivo o descontinuado',
            'description.string' => 'La descripción debe ser texto',
            'description.max' => 'La descripción no debe exceder 1000 caracteres',
        ];
        $this->validate($rules, $messages);
        $this->product = Product::findOrFail($this->product->id);
        $this->product->internal_code = $this->internal_code;
        $this->product->barcode = $this->barcode;
        $this->product->commercial_name = $this->commercial_name;
        $this->product->technical_name = $this->technical_name;
        $this->product->presentation = $this->presentation;
        $this->product->primary_unit = $this->primary_unit;
        $this->product->secondary_unit = $this->secondary_unit;
        $this->product->category_id = $this->category_id;
        $this->product->brand_id = $this->brand_id;
        $this->product->supplier_id = $this->supplier_id;
        $this->product->minimum_stock = $this->minimum_stock;
        $this->product->maximum_stock = $this->maximum_stock;
        $this->product->purchase_price = $this->purchase_price;
        $this->product->sale_price = $this->sale_price;
        $this->product->profit_margin = $this->profit_margin;
        $this->product->status = $this->status;
        $this->product->description = $this->description;
        $this->product->save();
        $this->modal_form = false;
    }
}
