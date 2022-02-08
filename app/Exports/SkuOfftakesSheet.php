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

class SkuOfftakesSheet implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
	public $date;
	public $supervisorId;
	public $region;

	public function __construct($date, $supervisorId, $region)
	{
		$this->date = $date;
		$this->supervisorId = $supervisorId;
		$this->region = $region;
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

		$daysInMonth = 0;

		$supervisors =
			User::with('roles')
			->where('active', '=', 1)
			->whereHas('roles',  function ($q) {
				$q->where('name', '=', 'SUPERVISOR');
			})->orderBy('name');
		$supervisors = $supervisors->take(5);

		$supervisorId = $this->supervisorId;
		if ($supervisorId != '')
			$supervisors = $supervisors->where('id', '=', $supervisorId);

		$supervisors = $supervisors->get();

		$productsOfftakes = [];

		foreach ($supervisors as $supervisor) {

			$singleUserData = [];

			$users = User::where('supervisor_id', '=', $supervisor->id)
				->where('active', '=', 1);
			// ->get();
			$region = $this->region;
			if ($region) {
				$users = $users->where('region', 'LIKE', '%' . $region . '%');
			}
			$users = $users->get();
			foreach ($users as $user) {

				$singleUserData['user'] = $user;

				$company = Company::find(1);
				$ors = $company->orders_list()
					->where('order_type', '=', 'Sales')
					->where('user_id', '=', $user->id)
					->where('is_active', '=', 1)
					->with('order_details')
					->whereHas('order_details',  function ($q) {
						$q->groupBy('sku_id');
					});

				if ($month) {
					$ors = $ors->whereMonth('created_at', '=', $month);
				}
				if ($year) {
					$ors = $ors->whereYear('created_at', '=', $year);
				}
				$ors = $ors->get();

				$daysInMonth = Carbon::parse($year . $month . '01')->daysInMonth;
				$currentMonth = Carbon::now()->format('m');
				if ($month == $currentMonth) {
					$daysInMonth = Carbon::now()->format('d');
				}
				for ($i = 1; $i <= $daysInMonth; $i++) {
					// To check single day orders
					$ordersOfADay = [];
					foreach ($ors as $or) {
						// var_dump(Carbon::parse($or->created_at)->format('d'));
						if (Carbon::parse($or->created_at)->format('d') == sprintf("%02d", $i)) {
							$ordersOfADay[] = $or;
						}
					}
					// End To check single day orders


					$skuIDs = [];
					foreach ($ordersOfADay as $order) {
						foreach ($order->order_details as $order_detail) {
							$skuIDs[] = $order_detail->sku_id;
						}
					}
					$uniqueSkuIDs = array_unique($skuIDs);
					$countUniqueSkuIDs = sizeof($uniqueSkuIDs);
					$singleUserData['date' . $i] = $countUniqueSkuIDs;

					// // If value of SKUS required
					// if($request->type == 'value') {
					// 	$totalValue = 0;
					// 	foreach($ordersOfADay as $order) {
					// 		foreach($order->order_details as $order_detail) {
					// 			$totalValue += $order_detail->value; 
					// 		}
					// 	}
					// 	$singleUserData['date' . $i] = $totalValue;
					// }
				}
				$productsOfftakes[] = $singleUserData;
			}
		}

		return view('exports.sku_offtakes_export', compact('productsOfftakes', 'daysInMonth'));
	}

	public function title(): string
	{
		return 'SKU Offtakes | ' . Carbon::parse($this->date)->format('M-Y');
	}
}
