<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MamaearthBAReportExport implements WithMultipleSheets
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
        $this->brand = 'Mamaearth';
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new DailyAttendanceSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        $sheets[] = new MonthlyAttendanceSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        $sheets[] = new SkuOfftakesSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        $sheets[] = new SkuValueOfftakesSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        $sheets[] = new OfftakesSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        $sheets[] = new OfftakesCountSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        $sheets[] = new LeaveDefaulterSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        $sheets[] = new CustomerSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        $sheets[] = new CustomerDataEntrySheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        $sheets[] = new CompetitorDataSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        // $sheets[] = new TargetSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        // $sheets[] = new FocusedTargetSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        $sheets[] = new ClosingStockSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        $sheets[] = new StockReportSheet($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);

        // $sheets[] = new ClosingStockSheet1($this->date, $this->supervisorId, $this->region, $this->channel, $this->brand);
        return $sheets;
    }
}
