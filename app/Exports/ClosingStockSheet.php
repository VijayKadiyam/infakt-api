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

class ClosingStockSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
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

    // public function view(): View
    // {
    //     $asd = [];
    //     $company = Company::find(1);

    //     $currentMonth = Carbon::now()->format('m');
    //     $month =  Carbon::parse($this->date)->format('m');
    //     $year = Carbon::parse($this->date)->format('Y');

    //     $count = 0;
    //     $skus = $company->skus;
    //     $count = $skus->count();

    //     $users = $company->users();

    //     $users = $users->with('roles')
    //         ->whereHas('roles',  function ($q) {
    //             $q->where('name', '!=', 'Admin');
    //             $q->where('name', '!=', 'Distributor');
    //         });

    //     $region = $this->region;
    //     if ($region) {
    //         $users = $users->where('region', 'LIKE', '%' . $region . '%');
    //     }

    //     $channel = $this->channel;
    //     if ($channel) {
    //         $users = $users->where('channel', 'LIKE', '%' . $channel . '%');
    //     }

    //     $supervisorId = $this->supervisorId;
    //     if ($supervisorId != '') {
    //         $users = $users->where('supervisor_id', '=', $supervisorId);
    //     }

    //     $users = $users
    //         // ->take(3)
    //         ->where('employee_code', '=', "W/GUJ/0094")
    //         // ->orWhere('employee_code', '=', "W/MUM/0005")
    //         // ->where('employee_code', '=', "N/DEL/0149")
    //         // ->orWhere('employee_code', '=', "N/WUP/0012")
    //         ->latest()
    //         ->get();

    //     $usersSkusData = [];

    //     foreach ($users as $user) {
    //         if ($user) {
    //             $dailyOrderSummaries = DailyOrderSummary::where('user_id', '=', $user->id)
    //                 // ->where('sku_id', '=', $sku->id)
    //                 ->get();
    //             foreach ($skus as $sku) {
    //                 $sku['user'] = $user;
    //                 $isSku = 0;
    //                 foreach ($dailyOrderSummaries as $dailyOrderSummary) {
    //                     if ($dailyOrderSummary->sku_id == $sku->id) {
    //                         $isSku = 1;
    //                         $sku['qty'] = (int) $dailyOrderSummary->closing_stock;
    //                         $sku['opening_stock'] = (int)$dailyOrderSummary->opening_stock;
    //                         $sku['received_stock'] = (int)$dailyOrderSummary->received_stock;
    //                         $sku['purchase_returned_stock'] = (int)$dailyOrderSummary->purchase_returned_stock;
    //                         $sku['sales_stock'] = (int)$dailyOrderSummary->sales_stock;
    //                         $sku['returned_stock'] = (int)$dailyOrderSummary->returned_stock;
    //                         $sku['closing_stock'] = (int)$dailyOrderSummary->closing_stock;
    //                     }
    //                 }
    //                 if ($isSku == 0) {
    //                     $sku['qty'] = 0;
    //                     $sku['opening_stock'] = 0;
    //                     $sku['received_stock'] = 0;
    //                     $sku['purchase_returned_stock'] = 0;
    //                     $sku['sales_stock'] = 0;
    //                     $sku['returned_stock'] = 0;
    //                     $sku['closing_stock'] = 0;
    //                 }
    //             }
    //             for ($i = 0; $i < sizeof($skus); $i++) {
    //                 for ($j = $i; $j < sizeof($skus); $j++) {
    //                     if ($skus[$i]['qty'] < $skus[$j]['qty']) {
    //                         $temp = $skus[$i];
    //                         $skus[$i] = $skus[$j];
    //                         $skus[$j] = $temp;
    //                     }
    //                 }
    //             }
    //             $usersSkusData[] = array_combine(array_keys($skus->toArray()), $skus->toArray());;
    //         }

    //         // if ($user) {
    //         //     $stocks = [];
    //         //     if ($user)
    //         //         $stocks = Stock::whereYear('created_at', $year)
    //         //             ->whereMonth('created_at', $month)
    //         //             ->where('distributor_id', '=', $user->distributor_id)
    //         //             ->latest()->get();
    //         //     $orders = [];
    //         //     if ($user)
    //         //         $orders = Order::whereYear('created_at', $year)
    //         //             ->where('distributor_id', '=', $user->distributor_id)
    //         //             ->latest()->get();
    //         //     foreach ($skus as $sku) {
    //         //         $sku['mrp_price'] = $sku['price'];
    //         //         $skuStocks = [];
    //         //         foreach ($stocks as $stock) {
    //         //             if ($sku['id'] == $stock['sku_id'])
    //         //                 $skuStocks[] = $stock;
    //         //         }
    //         //         $sku['offer_price'] = null;
    //         //         if (sizeof($skuStocks) > 0) {
    //         //             if ($sku['offer_id'] != null) {
    //         //                 if ($sku['offer']['offer_type']['name'] == 'FLAT') {
    //         //                     $sku['offer_price'] = $sku['price'] - $sku['offer']['offer'];
    //         //                 }
    //         //                 if ($sku['offer']['offer_type']['name'] == 'PERCENT') {
    //         //                     $sku['offer_price'] = $sku['price'] - ($sku['price'] * $sku['offer']['offer'] / 100);
    //         //                 }
    //         //             }
    //         //         }
    //         //         $totalQty = 0;
    //         //         foreach ($skuStocks as $stock) {
    //         //             $totalQty += $stock->qty;
    //         //         }
    //         //         $receivedQty = 0;
    //         //         $purchaseReturnedQty = 0;
    //         //         $consumedQty = 0;
    //         //         $returnedQty = 0;

    //         //         foreach ($orders as $order) {
    //         //             $todayDate = Carbon::parse($this->date)->format('d-m-Y');
    //         //             // $todayDate = Carbon::now()->format('d-m-Y');
    //         //             // $todayDate = "15-02-2022";
    //         //             $orderDate = Carbon::parse($order->created_at)->format('d-m-Y');
    //         //             if ($orderDate != $todayDate) {
    //         //                 foreach ($order->order_details as $detail) {
    //         //                     if ($detail->sku_id == $sku['id'] && $order->order_type == 'Opening Stock')
    //         //                         $totalQty += $detail->qty;
    //         //                     if ($detail->sku_id == $sku['id'] && $order->order_type == 'Stock Received')
    //         //                         $totalQty += $detail->qty;
    //         //                     if ($detail->sku_id == $sku['id'] && $order->order_type == 'Purchase Returned')
    //         //                         $totalQty -= $detail->qty;
    //         //                     if ($detail->sku_id == $sku['id'] && $order->order_type == 'Sales')
    //         //                         $totalQty -= $detail->qty;
    //         //                     if ($detail->sku_id == $sku['id'] && $order->order_type == 'Stock Returned')
    //         //                         $totalQty += $detail->qty;
    //         //                 }
    //         //             } else {
    //         //                 foreach ($order->order_details as $detail) {
    //         //                     if ($detail->sku_id == $sku['id'] && $order->order_type == 'Opening Stock')
    //         //                         $totalQty += $detail->qty;
    //         //                     if ($detail->sku_id == $sku['id'] && $order->order_type == 'Stock Received')
    //         //                         $receivedQty += $detail->qty;
    //         //                     if ($detail->sku_id == $sku['id'] && $order->order_type == 'Purchase Returned')
    //         //                         $purchaseReturnedQty += $detail->qty;
    //         //                     if ($detail->sku_id == $sku['id'] && $order->order_type == 'Sales')
    //         //                         $consumedQty += $detail->qty;
    //         //                     if ($detail->sku_id == $sku['id'] && $order->order_type == 'Stock Returned')
    //         //                         $returnedQty += $detail->qty;
    //         //                 }
    //         //             }
    //         //         }

    //         //         $sku['qty'] = ($totalQty + $receivedQty - $purchaseReturnedQty - $consumedQty + $returnedQty);
    //         //         $sku['opening_stock'] = $totalQty;
    //         //         $sku['received_stock'] = $receivedQty;
    //         //         $sku['purchase_returned_stock'] = $purchaseReturnedQty;
    //         //         $sku['sales_stock'] = $consumedQty;
    //         //         $sku['returned_stock'] = $returnedQty;
    //         //         $sku['closing_stock'] = ($totalQty + $receivedQty - $purchaseReturnedQty - $consumedQty + $returnedQty);
    //         //         $sku['user'] = $user;
    //         //         $asd[] = $sku;
    //         //     }
    //         // }
    //     }

    //     return view('exports.closing_stock_export', compact('usersSkusData'));
    // }
    
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
		// $dailyOrderSummaries = $company->daily_order_summaries()
		// ->where('user_id', 3314)
		// ->orWhere('user_id', 3009)
		// ->orWhere('user_id', 2857)
		// ->whereDate('created_at', '=', $date)
		// ->latest();

		// $dailyOrderSummaries = $dailyOrderSummaries->get();

		$skus = $company->skus()
			->take(10)
			->get();

		$users = User::whereHas('roles', function ($q) {
			$q->where('name', '=', 'BA');
		})
			->where('active', '=', 1)
			->take(2)
			->get();

		// $d =  $dailyOrderSummaries
		// 	->where('user_id', '=', $users[0]->id)
		// 	->get();

		foreach ($skus as $sku) {
			$skuUsers = [];
			$userDailyOrderSummaries = [];
			$dailyOrderSummaries = $company->daily_order_summaries()
				->whereDate('created_at', '=', $date)
				->where('sku_id', '=', $sku->id)
				->latest()
				->get();
			foreach ($users as $user) {
				foreach($dailyOrderSummaries as $dailyOrderSummary) {
					if($dailyOrderSummary->user_id == $user->id) {
						$userDailyOrderSummaries[] = $dailyOrderSummary;
					}
				}
			}
			$sku['userDailyOrderSummaries'] = $userDailyOrderSummaries;
		}

        return view('exports.closing_stock_export', compact('skus'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        if ($this->region) {
            return $this->region . "'s Closing Stock | " . Carbon::parse($this->date)->format('d-M-Y');
        } else {
            return "Closing Stock | " . Carbon::parse($this->date)->format('d-M-Y');
        }
    }
}
