<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Location;
use App\Models\Movement;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Crear usuario administrador
        $admin = User::factory()->create([
            'name' => 'Abel Arana Cortez',
            'email' => 'abel.arana@hotmail.com',
            'password' => Hash::make('lobomalo123'),
        ]);

        // Crear marcas
        $brands = Brand::factory()->count(10)->create();

        // Crear categorías principales
        $mainCategories = Category::factory()->count(5)->create();

        // Crear subcategorías
        $subCategories = Category::factory()
            ->count(15)
            ->state(function (array $attributes) use ($mainCategories) {
                return [
                    'parent_id' => $mainCategories->random()->id,
                ];
            })
            ->create();

        // Crear proveedores
        $suppliers = Supplier::factory()->count(8)->create();

        // Crear almacenes
        $warehouses = Warehouse::factory()->count(3)->create();

        // Crear ubicaciones para cada almacén
        $locations = collect();
        $warehouses->each(function ($warehouse) use ($locations) {
            $warehouseLocations = Location::factory()
                ->count(5)
                ->state(['warehouse_id' => $warehouse->id])
                ->create();
            $locations->push($warehouseLocations);
        });

        // Crear productos
        $products = Product::factory()
            ->count(50)
            ->state(function (array $attributes) use ($brands, $subCategories, $suppliers) {
                return [
                    'brand_id' => $brands->random()->id,
                    'category_id' => $subCategories->random()->id,
                    'supplier_id' => $suppliers->random()->id,
                ];
            })
            ->create();

        // Crear imágenes para cada producto
        $products->each(function ($product) {
            ProductImage::factory()
                ->count(rand(1, 3))
                ->state(['product_id' => $product->id])
                ->create();
        });

        // Crear lotes para cada producto
        $products->each(function ($product) {
            Batch::factory()
                ->count(rand(1, 3))
                ->state(['product_id' => $product->id])
                ->create();
        });

        // Crear órdenes de compra
        $purchaseOrders = PurchaseOrder::factory()
            ->count(20)
            ->state(function (array $attributes) use ($suppliers, $admin) {
                return [
                    'supplier_id' => $suppliers->random()->id,
                    'user_id' => $admin->id,
                ];
            })
            ->create();

        // Crear items para cada orden de compra
        $purchaseOrders->each(function ($order) use ($products) {
            PurchaseOrderItem::factory()
                ->count(rand(2, 5))
                ->state(function (array $attributes) use ($order, $products) {
                    $product = $products->random();
                    return [
                        'purchase_order_id' => $order->id,
                        'product_id' => $product->id,
                    ];
                })
                ->create();
        });

        // Crear movimientos de inventario
        $products->each(function ($product) use ($warehouses, $locations, $admin) {
            Movement::factory()
                ->count(rand(3, 8))
                ->state(function (array $attributes) use ($product, $warehouses, $locations, $admin) {
                    $warehouse = $warehouses->random();
                    $location = $locations->flatten()->where('warehouse_id', $warehouse->id)->random();
                    return [
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                        'location_id' => $location->id,
                        'user_id' => $admin->id,
                    ];
                })
                ->create();
        });
    }
}
