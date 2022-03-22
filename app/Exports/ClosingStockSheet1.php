<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

use App\Company;
use App\DailyOrderSummary;
use App\FocusedTarget;
use App\Order;
use App\Stock;
use App\Target;
use App\User;
use Carbon\Carbon;

class ClosingStockSheet1 implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    public $date;
    public $month;
    public $supervisorId;
    public $region;
    public $channel;
    public function __construct($date, $supervisorId, $region, $channel, $brand)
    {
        $this->date = $date;
        $this->supervisorId = $supervisorId;
        $this->region = $region;
        $this->month = Carbon::parse($date)->format('M-Y');
        $this->channel = $channel;
        $this->brand = $brand;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => '80FFFF00']
                ]
            ],
        ];
    }


    public function view(): View
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $asd = [];
        $company = Company::find(1);

        $date = Carbon::now()->format('Y-m-d');

        $currentMonth = Carbon::now()->format('m');
        $month =  Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');
        $date = Carbon::parse($date)->format('Y-m-d');

        $count = 0;
        $dailyOrderSummaries = $company->daily_order_summaries()
            // ->where('user_id', 3314)->orwhere('user_id', 3009)->orwhere('user_id', 2857)
            // ->whereDate('created_at', '=', $date)
            ->latest();
        // ->orderBy('closing_stock', 'DESC');

        $region = $this->region;
        if ($region) {
            $dailyOrderSummaries = $dailyOrderSummaries->whereHas('user',  function ($q) use ($region) {
                $q->where('region', 'LIKE', '%' . $region . '%');
            });
        }
        $channel = $this->channel;
        if ($channel) {
            $dailyOrderSummaries = $dailyOrderSummaries->whereHas('user',  function ($q) use ($channel) {
                $q->where('channel', 'LIKE', '%' . $channel . '%');
            });
        }
        $brand = $this->brand;
        if ($brand) {
            $dailyOrderSummaries = $dailyOrderSummaries->whereHas('user',  function ($q) use ($brand) {
                $q->where('brand', 'LIKE', '%' . $brand . '%');
            });
        }
        $supervisorId = $this->supervisorId;
        if ($supervisorId != '')
            $dailyOrderSummaries = $dailyOrderSummaries->whereHas('user',  function ($q) use ($supervisorId) {
                $q->where('supervisor_id', '=', $supervisorId);
            });

        $dailyOrderSummaries = $dailyOrderSummaries->get();

        return view('exports.closing_stock_export', compact('dailyOrderSummaries'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        if ($this->region) {
            return $this->region . "'s Closing Stock | " . Carbon::parse($this->date)->format('d-M-Y');
        } else if ($this->channel) {
            return $this->channel . "'s Closing Stock | " . Carbon::parse($this->date)->format('d-M-Y');
        } else {
            return "Closing Stock | " . Carbon::parse($this->date)->format('d-M-Y');
        }
    }
}
