<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Product\ProductLive;
use App\Livewire\Category\CategoryLive;
use App\Livewire\Brand\BrandLive;
use App\Livewire\Supplier\SupplierLive;
use App\Livewire\Batch\BatchLive;
use App\Livewire\Location\LocationLive;
use App\Livewire\Warehouse\WarehouseLive;
use App\Livewire\Movement\MovementLive;
use App\Livewire\Pos\Venta;
use App\Livewire\Pos\VentaLive;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/products', ProductLive::class)->name('products');

    // Rutas para Categorías
    Route::get('/categories', CategoryLive::class)->name('categories.index');

    // Rutas para Marcas
    Route::get('/brands', BrandLive::class)->name('brands.index');

    // Rutas para Proveedores
    Route::get('/suppliers', SupplierLive::class)->name('suppliers.index');

    // Rutas para Lotes
    Route::get('/batches', BatchLive::class)->name('batches.index');

    // Rutas para Ubicaciones
    Route::get('/locations', LocationLive::class)->name('locations.index');

    // Rutas para Almacenes
    Route::get('/warehouses', WarehouseLive::class)->name('warehouses.index');
    // Rutas para Movimientos
    Route::get('/movements', MovementLive::class)->name('movements.index');

    // Rutas para Ventas
    Route::get('/sales', VentaLive::class)->name('sales.index');

});

require __DIR__.'/auth.php';
