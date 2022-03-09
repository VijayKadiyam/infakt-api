<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Sku;
use App\Product;
use App\Stock;
use App\User;
use App\Order;
use Carbon\Carbon;
use App\UserReferencePlan;
use App\DailyOrderSummary;
use App\Company;

class StoreSkuCountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:sku_count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Start Calculating...');

        ini_set('max_execution_time', 0);

        // DailyOrderSummary::truncate();

        $skus = Sku::all();

        // $users = [
        //     User::find(1516),
        // ];

        $users = User::whereHas('roles', function ($q) {
            $q->where('name', '=', 'BA');
        })->get();

        $dailyOrderSummaries = DailyOrderSummary::all();

        return sizeof($dailyOrderSummaries);

        foreach ($users as $user) {
            if ($user) {

                $orders = [];
                if ($user)
                    $orders = Order::whereYear('created_at', Carbon::now())
                        // ->whereMonth('created_at', Carbon::now())
                        ->where('distributor_id', '=', $user->distributor_id)
                        ->latest()->get();

                foreach ($skus as $sku) {
                    $sku['mrp_price'] = $sku->price;
                    $sku['offer_price'] = null;

                    $totalQty = 0;
                    $receivedQty = 0;
                    $purchaseReturnedQty = 0;
                    $consumedQty = 0;
                    $returnedQty = 0;

                    foreach ($orders as $order) {
                        $todayDate = Carbon::now()->format('m');
                        $orderDate = Carbon::parse($order->created_at)->format('m');

                        if ($orderDate != $todayDate) {
                            foreach ($order->order_details as $detail) {
                                if ($detail->sku_id == $sku->id && $order->order_type == 'Opening Stock')
                                    $totalQty += $detail->qty;
                                if ($detail->sku_id == $sku->id && $order->order_type == 'Stock Received')
                                    $totalQty += $detail->qty;
                                if ($detail->sku_id == $sku->id && $order->order_type == 'Purchase Returned')
                                    $totalQty -= $detail->qty;
                                if ($detail->sku_id == $sku->id && $order->order_type == 'Sales')
                                    $totalQty -= $detail->qty;
                                if ($detail->sku_id == $sku->id && $order->order_type == 'Stock Returned')
                                    $totalQty += $detail->qty;
                            }
                        } else {
                            foreach ($order->order_details as $detail) {
                                if ($detail->sku_id == $sku->id && $order->order_type == 'Opening Stock')
                                    $totalQty += $detail->qty;
                                if ($detail->sku_id == $sku->id && $order->order_type == 'Stock Received')
                                    $receivedQty += $detail->qty;
                                if ($detail->sku_id == $sku->id && $order->order_type == 'Purchase Returned')
                                    $purchaseReturnedQty += $detail->qty;
                                if ($detail->sku_id == $sku->id && $order->order_type == 'Sales')
                                    $consumedQty += $detail->qty;
                                if ($detail->sku_id == $sku->id && $order->order_type == 'Stock Returned')
                                    $returnedQty += $detail->qty;
                            }
                        }
                    }


                    $sku['opening_stock'] = $totalQty;
                    $sku['received_stock'] = $receivedQty;
                    $sku['purchase_returned_stock'] = $purchaseReturnedQty;
                    $sku['sales_stock'] = $consumedQty;
                    $sku['returned_stock'] = $returnedQty;
                    $sku['closing_stock'] = ($totalQty + $receivedQty - $purchaseReturnedQty - $consumedQty + $returnedQty);
                    $sku['qty'] = $sku['closing_stock'];

                    DailyOrderSummary::where('sku_id', '=', $sku->id)
						->where('user_id', '=', $user->id)
						->delete();

                    DailyOrderSummary::create([
                        'company_id'  =>  1,
                        'user_id' =>  $user->id,
                        'sku_id'  =>  $sku->id,
                        'opening_stock' =>  $sku['opening_stock'],
                        'received_stock' =>  $sku['received_stock'],
                        'purchase_returned_stock' =>  $sku['purchase_returned_stock'],
                        'sales_stock' =>  $sku['sales_stock'],
                        'returned_stock' =>  $sku['returned_stock'],
                        'closing_stock' =>  $sku['closing_stock'],
                    ]);
                }
            }
        }
    }
}
