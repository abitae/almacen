<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'internal_code',
        'barcode',
        'commercial_name',
        'technical_name',
        'brand_id',
        'category_id',
        'supplier_id',
        'presentation',
        'primary_unit',
        'secondary_unit',
        'purchase_price',
        'sale_price',
        'profit_margin',
        'status',
        'minimum_stock',
        'maximum_stock',
        'description',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'profit_margin' => 'decimal:2',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function equivalentProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_equivalents', 'product_id', 'equivalent_id')
            ->withTimestamps();
    }
}
