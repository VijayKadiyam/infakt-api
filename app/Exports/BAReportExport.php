<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BAReportExport implements WithMultipleSheets
{
    use Exportable;

    public $date;
    public $supervisorId;

    public function __construct($date, $supervisorId = '') 
    {
        $this->date = $date;
        $this->supervisorId = $supervisorId;
    } 

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new DailyAttendanceSheet($this->date, $this->supervisorId);
        // $sheets[] = new MonthlyAttendanceSheet($this->date, $this->supervisorId);
        // $sheets[] = new SkuOfftakesSheet($this->date, $this->supervisorId);
        // $sheets[] = new SkuValueOfftakesSheet($this->date, $this->supervisorId);
        // $sheets[] = new OfftakesSheet($this->date, $this->supervisorId);
        // $sheets[] = new OfftakesCountSheet($this->date, $this->supervisorId);

        return $sheets;
    }
}
