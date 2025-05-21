<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\ProductImage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ProductService
{

    public function getHeaders(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'image', 'label' => 'Imagen'],
            ['key' => 'code', 'label' => 'Código Interno'],
            ['key' => 'name', 'label' => 'Nombre'],
            ['key' => 'category.name', 'label' => 'Categoria'],
            ['key' => 'brand.name', 'label' => 'Marca'],
            ['key' => 'presentation', 'label' => 'Presentación'],
            ['key' => 'unidad_medida', 'label' => 'Unidad Medida'],
        ];
    }

    public function getProducts(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = Product::query()->with(['category', 'brand']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('internal_code', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('barcode', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('commercial_name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('technical_name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (!empty($filters['brand'])) {
            $query->where('brand_id', $filters['brand']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function getFormData(): array
    {
        return [
            'categories' => Category::where('status', 'active')->get(),
            'brands' => Brand::where('status', 'active')->get(),
            'suppliers' => Supplier::where('status', 'active')->get(),
        ];
    }

    public function create(array $data): Product
    {
        $product = Product::create($data);

        if (isset($data['images']) && is_array($data['images'])) {
            $this->handleProductImages($product, $data['images']);
        }

        return $product;
    }

    public function update(Product $product, array $data): bool
    {
        $updated = $product->update($data);

        if ($updated && isset($data['images']) && is_array($data['images'])) {
            $this->handleProductImages($product, $data['images']);
        }

        return $updated;
    }

    public function delete(Product $product): bool
    {
        // Eliminar todas las imágenes asociadas
        foreach ($product->images as $image) {
            $this->deleteImage($image->image_path);
        }

        return $product->delete();
    }

    protected function handleProductImages(Product $product, array $images): void
    {
        // Eliminar imágenes existentes si se está actualizando
        if ($product->exists) {
            foreach ($product->images as $existingImage) {
                $this->deleteImage($existingImage->image_path);
                $existingImage->delete();
            }
        }

        // Procesar y guardar nuevas imágenes
        foreach ($images as $index => $image) {
            if ($image instanceof UploadedFile) {
                $path = $this->uploadImage($image);

                // Crear registro de imagen
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'image_name' => $image->getClientOriginalName(),
                    'is_primary' => $index === 0,
                    'order' => $index,
                    'file_size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                ]);
            }
        }
    }

    protected function uploadImage(UploadedFile $image): string
    {
        // Generar nombre único para la imagen
        $fileName = Str::uuid() . '.' . $image->getClientOriginalExtension();

        // Crear directorio si no existe
        $directory = 'products/' . date('Y/m');
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Guardar imagen con optimización
        $path = $image->storeAs($directory, $fileName, 'public');

        if (!$path) {
            throw new \Exception('Error al subir la imagen');
        }

        return $path;
    }

    protected function deleteImage(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getValidationRules(bool $isUpdate = false, ?int $productId = null): array
    {
        $uniqueRule = $isUpdate ? 'unique:products,internal_code,' . $productId : 'unique:products,internal_code';
        $barcodeUniqueRule = $isUpdate ? 'unique:products,barcode,' . $productId : 'unique:products,barcode';

        $rules = [
            'internal_code' => 'required|string|' . $uniqueRule,
            'barcode' => 'nullable|string|' . $barcodeUniqueRule,
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
            'images.*' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048', // 2MB
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ],
            'images' => [
                'nullable',
                'array',
                'max:5' // Máximo 5 imágenes por producto
            ],
        ];

        return $rules;
    }

    public function getValidationMessages(): array
    {
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
            'images.*.image' => 'El archivo debe ser una imagen válida',
            'images.*.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp',
            'images.*.max' => 'La imagen no debe pesar más de 2MB',
            'images.*.dimensions' => 'La imagen debe tener dimensiones entre 100x100 y 2000x2000 píxeles',
            'images.max' => 'No se pueden subir más de 5 imágenes por producto',
        ];

        return $messages;
    }
}
