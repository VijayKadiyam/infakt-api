<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BAReportExport implements WithMultipleSheets
{
    use Exportable;

    public $date;

    public function __construct($date) 
    {
        $this->date = $date;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new DailyAttendanceSheet($date);

        return $sheets;
    }
}
