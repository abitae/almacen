<!-- Inventario -->
<x-mary-nav-item title="Inventario" icon="archive-box" :active="request()->routeIs('products')">
    <x-slot:submenu>
        <x-mary-nav-item title="Productos" href="{{ route('products') }}" :active="request()->routeIs('products')" />
        <x-mary-nav-item title="Categorías" href="{{ route('categories.index') }}" :active="request()->routeIs('categories.index')" />
        <x-mary-nav-item title="Marcas" href="{{ route('brands.index') }}" :active="request()->routeIs('brands.index')" />
        <x-mary-nav-item title="Lotes" href="{{ route('batches.index') }}" :active="request()->routeIs('batches.index')" />
    </x-slot:submenu>
</x-mary-nav-item>

<!-- Proveedores -->
<x-mary-nav-item title="Proveedores" icon="truck" href="{{ route('suppliers.index') }}" :active="request()->routeIs('suppliers.index')" />
