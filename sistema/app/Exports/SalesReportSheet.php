<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesReportSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    /**
     * @param  Collection<int, Sale>  $sales
     */
    public function __construct(private Collection $sales) {}

    public function collection(): Collection
    {
        return $this->sales;
    }

    public function headings(): array
    {
        return ['N.° venta', 'Fecha', 'Cliente', 'Vendedor', 'Total (S/)', 'IGV (S/)'];
    }

    /**
     * @param  Sale  $sale
     * @return array<int, mixed>
     */
    public function map(mixed $sale): array
    {
        return [
            $sale->id,
            $sale->date->format('Y-m-d H:i:s'),
            $sale->customer?->name,
            $sale->user?->name,
            $sale->total,
            $sale->igv,
        ];
    }

    public function title(): string
    {
        return 'Ventas';
    }
}
