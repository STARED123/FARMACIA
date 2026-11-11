use Illuminate\Database\Eloquent\Relations\BelongsTo; // ← nuevo
use Illuminate\Database\Eloquent\Relations\HasMany; // ← nuevo
#[Fillable(['name', 'email', 'password',
'role_id', 'dni', 'phone', 'tuition'])] // ← nuevos
// Dentro de la clase, después de casts():
public function role(): BelongsTo // ← nuevo
{
return $this->belongsTo(Role::class);
}
public function sales(): HasMany // ← nuevo
{
return $this->hasMany(Sale::class);
}
