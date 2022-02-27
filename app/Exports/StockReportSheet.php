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
use Carbon\Carbon;

class StockReportSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    public $date;
    public $month;
    public $supervisorId;
    public $region;
    public $channel;
    public function __construct($date, $supervisorId, $region, $channel)
    {
        $this->date = $date;
        $this->supervisorId = $supervisorId;
        $this->region = $region;
        $this->month = Carbon::parse($date)->format('M-Y');
        $this->channel = $channel;
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

        $currentMonth = Carbon::now()->format('m');
        $month =  Carbon::parse($this->date)->format('m');
        $year = Carbon::parse($this->date)->format('Y');
        $date = Carbon::parse($this->date)->format('Y-m-d');

        $count = 0;
        $dailyOrderSummaries = $company->daily_order_summaries();
        // ->where('user_id', 3314)
        // ->where('user_id', 3314)->orwhere('user_id', 3009);
        // ->orwhere('user_id', 2857)
        // ->whereDate('created_at', '=', $date)
        // ->latest()
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
        $supervisorId = $this->supervisorId;
        if ($supervisorId != '')
            $dailyOrderSummaries = $dailyOrderSummaries->whereHas('user',  function ($q) use ($supervisorId) {
                $q->where('supervisor_id', '=', $supervisorId);
            });

        // $dailyOrderSummaries = $dailyOrderSummaries->take(20000);
        $dailyOrderSummaries = $dailyOrderSummaries->get();

        $total_opening_stocks = 0;
        $total_closing_stocks = 0;
        $total_received_stock = 0;
        $total_purchase_returned_stock = 0;
        $total_sales_stock = 0;
        $total_returned_stock = 0;
        $users = [];

        foreach ($dailyOrderSummaries as $key => $dos) {
            $user = $dos->user->toArray();
            if ($user['active'] == 1) {
                unset($dos['user']);
                $user_id = $user['id'];

                $sku = $dos->sku;
                unset($dos['sku']);

                $sku_price = $sku->price;
                $opening_stock = $dos->opening_stock;
                $closing_stock = $dos->closing_stock;
                $received_stock = $dos->received_stock;
                $purchase_returned_stock = $dos->purchase_returned_stock;
                $sales_stock = $dos->sales_stock;
                $returned_stock = $dos->returned_stock;

                $total_opening_stocks = $opening_stock * $sku_price;
                $total_closing_stocks = $closing_stock * $sku_price;
                $total_received_stock = $received_stock * $sku_price;
                $total_purchase_returned_stock = $purchase_returned_stock * $sku_price;
                $total_sales_stock = $sales_stock * $sku_price;
                $total_returned_stock = $returned_stock * $sku_price;
                $user_key = array_search($user_id, array_column($users, 'id'));
                if (!is_int($user_key)) {
                    // Insert
                    $user['total_opening_stocks'] = abs($total_opening_stocks);
                    $user['total_closing_stocks'] = abs($total_closing_stocks);
                    $user['total_received_stock'] = abs($total_received_stock);
                    $user['total_purchase_returned_stock'] = abs($total_purchase_returned_stock);
                    $user['total_sales_stock'] = abs($total_sales_stock);
                    $user['total_returned_stock'] = abs($total_returned_stock);
                    $users[] = $user;
                } else {
                    // Update
                    $users[$user_key]['total_opening_stocks'] += $total_opening_stocks;
                    $users[$user_key]['total_closing_stocks'] += $total_closing_stocks;
                    $users[$user_key]['total_received_stock'] += $total_received_stock;
                    $users[$user_key]['total_purchase_returned_stock'] += $total_purchase_returned_stock;
                    $users[$user_key]['total_sales_stock'] += $total_sales_stock;
                    $users[$user_key]['total_returned_stock'] += $total_returned_stock;
                }
            }
        }

        $allUsers = [];
        foreach ($users as $user) {
            $user['total_opening_stocks'] = round(abs($user['total_opening_stocks'] / 2));
            $user['total_closing_stocks'] = round(abs($user['total_closing_stocks'] / 2));
            $user['total_received_stock'] = round(abs($user['total_received_stock']  / 2));
            $user['total_purchase_returned_stock'] = round(abs($user['total_purchase_returned_stock'] / 2));
            $user['total_sales_stock'] = round(abs($user['total_sales_stock'] / 2));
            $allUsers[] = $user;
        }

        return view('exports.stock_report_export', compact('allUsers'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Stock Reports | " . Carbon::parse($this->date)->format('M-Y');
    }
}
