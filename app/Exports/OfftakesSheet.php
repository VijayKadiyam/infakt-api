<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

use App\Company;
use Carbon\Carbon;
use App\User;

class OfftakesSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
	public $date;
	public $supervisorId;
	public $region;
	public $channel;
	public $brand;

	public function __construct($date, $supervisorId, $region, $channel, $brand)
	{
		$this->date = $date;
		$this->supervisorId = $supervisorId;
		$this->region = $region;
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
		$date = $this->date;
		$month =  Carbon::parse($date)->format('m');
		$year = Carbon::parse($date)->format('Y');

		$count = 0;
		$orders = [];
		$supervisors = User::with('roles')
			->where('active', '=', 1)
			->whereHas('roles',  function ($q) {
				$q->where('name', '=', 'SUPERVISOR');
			})->orderBy('name');

		// $supervisors = $supervisors->take(1);

		$supervisorId = $this->supervisorId;
		if ($supervisorId != '')
			$supervisors = $supervisors->where('id', '=', $supervisorId);

		$supervisors = $supervisors->get();

		foreach ($supervisors as $supervisor) {

			$users = User::where('supervisor_id', '=', $supervisor->id)
				->where('active', '=', 1);
			// ->get();

			$region = $this->region;
			if ($region) {
				$users = $users->where('region', 'LIKE', '%' . $region . '%');
			}

			$channel = $this->channel;
			if ($channel) {
				$users = $users->where('channel', 'LIKE', '%' . $channel . '%');
			}

			$users = $users->get();

			foreach ($users as $user) {

				$company = Company::find(1);
				$ors = $company->orders_list()
					->where('user_id', '=', $user->id)
					->where('is_active', '=', 1)
					->with('order_details')
					->whereHas('order_details',  function ($q) {
						$q->groupBy('sku_id');
					});
				// if ($date) {
				// 	$ors = $ors->whereDate('created_at', $date);
				// }
			if ($month) {
					$ors = $ors->whereMonth('created_at', '=', $month);
				}
				if ($year) {
					$ors = $ors->whereYear('created_at', '=', $year);
				}
				$ors = $ors->get();
				if (count($ors) != 0) {
					foreach ($ors as $order)
						$orders[] = $order;
				}
			}
		}

		// Once we have list of all the orders
		// retailer_id
		$finalOrders = [];

		$daysInMonth = Carbon::parse($year . $month . '01')->daysInMonth;
		$currentMonth = Carbon::now()->format('m');
		if ($month == $currentMonth) {
			$daysInMonth = Carbon::now()->format('d');
		}
		for ($i = 1; $i <= $daysInMonth; $i++) {
			// To check single day orders
			$ordersOfADay = [];
			foreach ($orders as $or) {
				// var_dump(Carbon::parse($or->created_at)->format('d'));
				if (Carbon::parse($or->created_at)->format('d') == sprintf("%02d", $i)) {
					$ordersOfADay[] = $or;
				}
			}
			// End To check single day orders

			if (sizeof($ordersOfADay) > 0) {
				$singleDaySalesOrders = [];
				$singleDayStockReceived = [];
				$singleDayStockReturned = [];

				$salesOrder = [
					'order_details' => [],
				];
				$stockReceived = [
					'order_details' => [],
				];
				$stockReturned = [
					'order_details' => [],
				];

				$salesOrdersOfAllRetailersOfADay = [];
				$stockReceivedOfAllRetailersOfADay = [];
				$stockReturnedOfAllRetailersOfADay = [];

				foreach ($ordersOfADay as $order) {

					// Sales
					// Check if this date and this store is already there in the singleDaySalesOrders
					if ($order->order_type == 'Sales') {

						for ($k = 0; $k < sizeof($salesOrdersOfAllRetailersOfADay); $k++) {
							if ($salesOrdersOfAllRetailersOfADay[$k]['user_id'] == $order->user_id)
								$salesOrder = $salesOrdersOfAllRetailersOfADay[$k];
						}

						foreach ($order->order_details as $orderDetail) {
							$salesOrder['id'] = $order->id;
							$salesOrder['distributor_id'] = $order->distributor_id;
							$salesOrder['retailer_id'] = $order->retailer_id;
							$salesOrder['user_id'] = $order->user_id;
							$salesOrder['status'] = $order->status;
							$salesOrder['order_type'] = $order->order_type;
							$salesOrder['created_at'] = Carbon::parse($order->created_at)->format('d-m-Y');
							$salesOrder['user'] = $order->user;
							$orderDetailOfSkuAlreadyThere = false;
							for ($k = 0; $k < sizeof($salesOrder['order_details']); $k++) {
								if ($salesOrder['order_details'][$k]['sku_id'] == $orderDetail['sku_id']) {
									// var_dump(1234);
									$orderDetailOfSkuAlreadyThere = true;
									$salesOrder['order_details'][$k]['qty'] += $orderDetail['qty'];
									$salesOrder['order_details'][$k]['value'] += $orderDetail['value'];
								}
							}
							if (!$orderDetailOfSkuAlreadyThere)
								$salesOrder['order_details'][]  = $orderDetail;
						}
						// End Foreach order_details

						$isSalesOrderOfSingleRetailersOfADay = 0;
						for ($j = 0; $j < sizeof($salesOrdersOfAllRetailersOfADay); $j++) {
							if ($salesOrdersOfAllRetailersOfADay[$j]['user_id'] == $salesOrder['user_id']) {
								$salesOrdersOfAllRetailersOfADay[$j] = $salesOrder;
								$isSalesOrderOfSingleRetailersOfADay = 1;
							}
						}
						if ($isSalesOrderOfSingleRetailersOfADay == 0)
							$salesOrdersOfAllRetailersOfADay[] = $salesOrder;
						$salesOrder = [
							'order_details' => [],
						];
					}
					// End Sales

				}
				// End $orders of a day Foreach

				foreach ($salesOrdersOfAllRetailersOfADay as $salesOrderOfSingleRetailerOfADay) {
					if (sizeof(($salesOrderOfSingleRetailerOfADay['order_details'])) > 0)
						$finalOrders[] = $salesOrderOfSingleRetailerOfADay;
				}
			}
		}

		return view('exports.offtakes_export', compact('finalOrders'));
	}

	/**
	 * @return string
	 */
	public function title(): string
	{
		return 'Offtakes | ' . Carbon::parse($this->date)->format('M-Y');
	}
}
