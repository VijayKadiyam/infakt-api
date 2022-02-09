<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BAReportExport implements WithMultipleSheets
{
    use Exportable;

    public $date;
    public $supervisorId;
    public $region;

    public function __construct($date, $supervisorId = '', $region = "")
    {
        $this->date = $date;
        $this->supervisorId = $supervisorId;
        $this->region = $region;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new DailyAttendanceSheet($this->date, $this->supervisorId, $this->region);
        // $sheets[] = new MonthlyAttendanceSheet($this->date, $this->supervisorId, $this->region);
        // $sheets[] = new SkuOfftakesSheet($this->date, $this->supervisorId, $this->region);
        // $sheets[] = new SkuValueOfftakesSheet($this->date, $this->supervisorId, $this->region);
        // $sheets[] = new OfftakesSheet($this->date, $this->supervisorId, $this->region);
        // $sheets[] = new OfftakesCountSheet($this->date, $this->supervisorId, $this->region);
        // $sheets[] = new LeaveDefaulterSheet($this->date, $this->supervisorId, $this->region);
        // $sheets[] = new CustomerSheet($this->date, $this->supervisorId, $this->region);
        // $sheets[] = new CustomerDataEntrySheet($this->date, $this->supervisorId, $this->region);
        // $sheets[] = new CompetitorDataSheet($this->date, $this->supervisorId, $this->region);

        return $sheets;
    }
}
