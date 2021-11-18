<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DailyAttendanceExport implements WithMultipleSheets
{
    use Exportable;

    public $userAttendances;

    public function __construct($userAttendances) 
    {
        $this->userAttendances = $userAttendances;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        for ($month = 1; $month <= 12; $month++) {
            $sheets[] = new DailyAttendanceSheet($this->userAttendances);
        }

        return $sheets;
    }
}