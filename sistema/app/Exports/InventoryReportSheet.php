<?php

namespace App\Exports;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class InventoryReportSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    /**
     * @param  Collection<int, Medicine>  $inventory
     */
    public function __construct(private Collection $inventory) {}

    public function collection(): Collection
    {
        return $this->inventory;
    }

    public function headings(): array
    {
        return ['Código', 'Categoría', 'Existencias', 'Precio unitario (S/)', 'Subtotal (S/)'];
    }

    /**
     * @param  Medicine  $medicine
     * @return array<int, mixed>
     */
    public function map(mixed $medicine): array
    {
        return [
            $medicine->id,
            $medicine->category?->name,
            $medicine->amount,
            $medicine->price_unit,
            $medicine->subtotal,
        ];
    }

    public function title(): string
    {
        return 'Inventario';
    }
}
