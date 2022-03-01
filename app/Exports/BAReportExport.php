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
    public $channel;

    public function __construct($date, $supervisorId = '', $region = "", $channel = "")
    {
        $this->date = $date;
        $this->supervisorId = $supervisorId;
        $this->region = $region;
        $this->channel = $channel;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new DailyAttendanceSheet($this->date, $this->supervisorId, $this->region, $this->channel);
        $sheets[] = new MonthlyAttendanceSheet($this->date, $this->supervisorId, $this->region, $this->channel);
        $sheets[] = new SkuOfftakesSheet($this->date, $this->supervisorId, $this->region, $this->channel);
        $sheets[] = new SkuValueOfftakesSheet($this->date, $this->supervisorId, $this->region, $this->channel);
        $sheets[] = new OfftakesSheet($this->date, $this->supervisorId, $this->region, $this->channel);
        $sheets[] = new OfftakesCountSheet($this->date, $this->supervisorId, $this->region, $this->channel);
        $sheets[] = new LeaveDefaulterSheet($this->date, $this->supervisorId, $this->region, $this->channel);
        $sheets[] = new CustomerSheet($this->date, $this->supervisorId, $this->region, $this->channel);
        $sheets[] = new CustomerDataEntrySheet($this->date, $this->supervisorId, $this->region, $this->channel);
        $sheets[] = new CompetitorDataSheet($this->date, $this->supervisorId, $this->region, $this->channel);
        // $sheets[] = new TargetSheet($this->date, $this->supervisorId, $this->region, $this->channel);
        // $sheets[] = new FocusedTargetSheet($this->date, $this->supervisorId, $this->region, $this->channel);
        $sheets[] = new ClosingStockSheet($this->date, $this->supervisorId, $this->region, $this->channel);
        $sheets[] = new StockReportSheet($this->date, $this->supervisorId, $this->region, $this->channel);

        // $sheets[] = new ClosingStockSheet1($this->date, $this->supervisorId, $this->region, $this->channel);
        return $sheets;
    }
}
