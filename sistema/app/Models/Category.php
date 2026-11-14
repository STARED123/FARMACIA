<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
// Columnas que create() y update() pueden llenar (asignación masiva)
#[Fillable(['name', 'description'])]
class Category extends Model
{
// Relación del diagrama: Categoría 1 — 0..* Medicamento
public function medicines(): HasMany
{
return $this->hasMany(Medicine::class);
}
}