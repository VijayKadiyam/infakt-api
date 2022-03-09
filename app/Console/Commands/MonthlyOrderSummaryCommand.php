<?php

namespace App\Console\Commands;

use App\MonthlyOrderSummary;
use App\Order;
use App\Sku;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MonthlyOrderSummaryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:monthly_order_summary';

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
        ini_set('max_execution_time', 0);

		MonthlyOrderSummary::truncate();

		$skus = Sku::get();

		// $users = [
		// 	User::find(1515),
		// ];

		$users = User::whereHas('roles', function ($q) {
			$q->where('name', '=', 'BA');
		})->get();

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
						$currentMonth = Carbon::now()->format('m');
						$previousMonth = $currentMonth - 1;
						$orderMonth = Carbon::parse($order->created_at)->format('m');

						if ($orderMonth != $currentMonth) {
							if ($orderMonth != $previousMonth) {
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
					}


					$sku['opening_stock'] = $totalQty;
					$sku['received_stock'] = $receivedQty;
					$sku['purchase_returned_stock'] = $purchaseReturnedQty;
					$sku['sales_stock'] = $consumedQty;
					$sku['returned_stock'] = $returnedQty;
					$sku['closing_stock'] = ($totalQty + $receivedQty - $purchaseReturnedQty - $consumedQty + $returnedQty);
					$sku['qty'] = $sku['closing_stock'];


					MonthlyOrderSummary::create([
						'company_id'  =>  1,
						'user_id' =>  $user->id,
						'sku_id'  =>  $sku->id,
						'month'		=>	$previousMonth,
						'year'		=>	'2022',
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
