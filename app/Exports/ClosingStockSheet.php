<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

use App\Company;
use App\FocusedTarget;
use App\Order;
use App\Stock;
use App\Target;
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

    public function view(): View
    {
        $asd = [];
        $company = Company::find(1);

        $currentMonth = Carbon::now()->format('m');
        $month =  Carbon::parse($this->date)->format('m');
        $year = Carbon::parse($this->date)->format('Y');

        $count = 0;
        $skus = $company->skus;
        $count = $skus->count();

        $users = $company->users();

        $users = $users->with('roles')
            ->whereHas('roles',  function ($q) {
                $q->where('name', '!=', 'Admin');
                $q->where('name', '!=', 'Distributor');
            });

        $region = $this->region;
        if ($region) {
            $users = $users->where('region', 'LIKE', '%' . $region . '%');
        }

        $channel = $this->channel;
        if ($channel) {
            $users = $users->where('channel', 'LIKE', '%' . $channel . '%');
        }

        $supervisorId = $this->supervisorId;
        if ($supervisorId != '') {
            $users = $users->where('supervisor_id', '=', $supervisorId);
        }
        $users = $users->get();

        foreach ($users as $key => $user) {
            if ($user) {
                $stocks = [];
                if ($user)
                    $stocks = Stock::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->where('distributor_id', '=', $user->distributor_id)
                        ->latest()->get();
                $orders = [];
                if ($user)
                    $orders = Order::whereYear('created_at', $year)
                        ->where('distributor_id', '=', $user->distributor_id)
                        ->latest()->get();
                foreach ($skus as $sku) {
                    $sku['mrp_price'] = $sku['price'];
                    $skuStocks = [];
                    foreach ($stocks as $stock) {
                        if ($sku['id'] == $stock['sku_id'])
                            $skuStocks[] = $stock;
                    }
                    $sku['offer_price'] = null;
                    if (sizeof($skuStocks) > 0) {
                        if ($sku['offer_id'] != null) {
                            if ($sku['offer']['offer_type']['name'] == 'FLAT') {
                                $sku['offer_price'] = $sku['price'] - $sku['offer']['offer'];
                            }
                            if ($sku['offer']['offer_type']['name'] == 'PERCENT') {
                                $sku['offer_price'] = $sku['price'] - ($sku['price'] * $sku['offer']['offer'] / 100);
                            }
                        }
                    }
                    $totalQty = 0;
                    foreach ($skuStocks as $stock) {
                        $totalQty += $stock->qty;
                    }
                    $receivedQty = 0;
                    $purchaseReturnedQty = 0;
                    $consumedQty = 0;
                    $returnedQty = 0;

                    foreach ($orders as $order) {
                        $todayDate = Carbon::parse($this->date)->format('d-m-Y');
                        // $todayDate = Carbon::now()->format('d-m-Y');
                        // $todayDate = "15-02-2022";
                        $orderDate = Carbon::parse($order->created_at)->format('d-m-Y');
                        if ($orderDate != $todayDate) {
                            foreach ($order->order_details as $detail) {
                                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Opening Stock')
                                    $totalQty += $detail->qty;
                                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Stock Received')
                                    $totalQty += $detail->qty;
                                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Purchase Returned')
                                    $totalQty -= $detail->qty;
                                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Sales')
                                    $totalQty -= $detail->qty;
                                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Stock Returned')
                                    $totalQty += $detail->qty;
                            }
                        } else {
                            foreach ($order->order_details as $detail) {
                                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Opening Stock')
                                    $totalQty += $detail->qty;
                                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Stock Received')
                                    $receivedQty += $detail->qty;
                                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Purchase Returned')
                                    $purchaseReturnedQty += $detail->qty;
                                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Sales')
                                    $consumedQty += $detail->qty;
                                if ($detail->sku_id == $sku['id'] && $order->order_type == 'Stock Returned')
                                    $returnedQty += $detail->qty;
                            }
                        }
                    }

                    $sku['qty'] = ($totalQty + $receivedQty - $purchaseReturnedQty - $consumedQty + $returnedQty);
                    $sku['opening_stock'] = $totalQty;
                    $sku['received_stock'] = $receivedQty;
                    $sku['purchase_returned_stock'] = $purchaseReturnedQty;
                    $sku['sales_stock'] = $consumedQty;
                    $sku['returned_stock'] = $returnedQty;
                    $sku['closing_stock'] = ($totalQty + $receivedQty - $purchaseReturnedQty - $consumedQty + $returnedQty);
                    $sku['user'] = $user;
                    $asd[] = $sku;
                }
            }
        }
        $skus = $asd;
        for ($i = 0; $i < sizeof($skus); $i++) {
            for ($j = $i; $j < sizeof($skus); $j++) {
                if ($skus[$i]['qty'] < $skus[$j]['qty']) {
                    $temp = $skus[$i];
                    $skus[$i] = $skus[$j];
                    $skus[$j] = $temp;
                }
            }
        }

        return view('exports.closing_stock_export', compact('skus'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Closing Stock | ' . Carbon::parse($this->date)->format('d-M-Y');
    }
}
