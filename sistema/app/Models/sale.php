<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    // 1. Se usa la propiedad estándar de Laravel en lugar del atributo #[Fillable]
    protected $fillable = [
        'customer_id', 
        'user_id', 
        'date', 
        'total', 
        'igv'
    ];

    // 2. Definición de casts (compatible con Laravel 10 y 11)
    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'total' => 'decimal:2', 
            'igv' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailSale::class);
    }

    // Diagrama: +calcularTotal(). Los precios ya incluyen el IGV (18 %).
    public function calculateTotal(): float
    {
        $this->total = round($this->details()->sum('subtotal'), 2);
        $this->igv = round($this->total - ($this->total / 1.18), 2);
        return (float) $this->total;
    }
} // 3. Llave de cierre de la clase agregada aquí