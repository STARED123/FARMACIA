<?php

namespace App\Exports;

use App\Models\Medicine;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportsExport implements Export, WithMultipleSheets
{
    /**
     * @param  Collection<int, Sale>  $sales
     * @param  Collection<int, Medicine>  $inventory
     */
    public function __construct(
        private Collection $sales,
        private Collection $inventory,
    ) {}

    public function sheets(): array
    {
        return [
            new SalesReportSheet($this->sales),
            new InventoryReportSheet($this->inventory),
        ];
    }
}
