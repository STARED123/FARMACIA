<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    // Forma correcta de definir los campos fillable en Laravel
    protected $fillable = [
        'category_id', 
        'name', 
        'price', 
        'stock'
    ];

    /**
     * Get the attributes that should be cast.
     * (Forma moderna compatible con Laravel 11+)
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2', 
            'stock' => 'integer'
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function detailSales(): HasMany
    {
        return $this->hasMany(DetailSale::class);
    }

    // Diagrama: +actualizarStock(): void
    public function updateStock(int $amount): void
    {
        $this->decrement('stock', $amount);
    }
}