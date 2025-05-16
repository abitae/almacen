<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_number',
        'manufacturing_date',
        'expiration_date',
        'quantity',
        'unit_price',
        'status',
    ];

    protected $casts = [
        'manufacturing_date' => 'date',
        'expiration_date' => 'date',
        'unit_price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }
}
