<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BAReportExport;
use App\Company;

class OfftakeAnalyticsController extends Controller
{
	public function __construct() {
		$this->middleware(['auth:api', 'company'])
			->except(['exports']);
	}

	public function masters(Request $request)
  {
    $months = [
      ['text'  =>  'JANUARY', 'value' =>  1],
      ['text'  =>  'FEBRUARY', 'value' =>  2],
      ['text'  =>  'MARCH', 'value' =>  3],
      ['text'  =>  'APRIL', 'value' =>  4],
      ['text'  =>  'MAY', 'value' =>  5],
      ['text'  =>  'JUNE', 'value' =>  6],
      ['text'  =>  'JULY', 'value' =>  7],
      ['text'  =>  'AUGUST', 'value' =>  8],
      ['text'  =>  'SEPTEMBER', 'value' =>  9],
      ['text'  =>  'OCTOBER', 'value' =>  10],
      ['text'  =>  'NOVEMBER', 'value' =>  11],
      ['text'  =>  'DECEMBER', 'value' =>  12],
    ];

    $years = ['2020', '2021'];

    return response()->json([
      'months'  =>  $months,
      'years'   =>  $years,
    ], 200);
  }

	public function noOrValueOfReports(Request $request) 
	{
		$request->validate([
			'month'  =>  'required',            
			'year'   =>  'required',
			'type'	 =>		'required',
 		]);

		$daysInMonth = 0;

		$supervisors = 
			User::with('roles')
				->where('active', '=', 1)
				->whereHas('roles',  function ($q) {
					$q->where('name', '=', 'SUPERVISOR');
				})->orderBy('name')
			// ->take(1)
			->get();

		$productsOfftakes = [];

		foreach ($supervisors as $supervisor) {

			$singleUserData = [];

			$users = User::where('supervisor_id', '=', $supervisor->id)
				->where('active', '=', 1)
				->get();

			foreach ($users as $user) {

				$singleUserData['user'] = $user;

				$ors = request()->company->orders_list()
					->where('order_type', '=', 'Sales')
					->where('user_id', '=', $user->id)
					->where('is_active', '=', 1)
					->with('order_details')
					->whereHas('order_details',  function ($q) {
						$q->groupBy('sku_id');
					});	

				if ($request->month) {
					$ors = $ors->whereMonth('created_at', '=', $request->month);
				}
				if ($request->year) {
					$ors = $ors->whereYear('created_at', '=', $request->year);
				}
				$ors = $ors->get();

				$daysInMonth = Carbon::parse($request->year . $request->month . '01')->daysInMonth;
				$currentMonth = Carbon::now()->format('m');
				if($request->month == $currentMonth) {
					$daysInMonth = Carbon::now()->format('d');	
				}
				for($i = 1; $i <= $daysInMonth; $i++) {
					// To check single day orders
					$ordersOfADay = [];
					foreach($ors as $or) {
						// var_dump(Carbon::parse($or->created_at)->format('d'));
						if(Carbon::parse($or->created_at)->format('d') == sprintf("%02d", $i)) {
							$ordersOfADay[] = $or;
						}
					}
					// End To check single day orders

					// If No of SKU per day is required
					if($request->type == 'no') {
						$skuIDs = [];
						foreach($ordersOfADay as $order) {
							foreach($order->order_details as $order_detail) {
								$skuIDs[] = $order_detail->sku_id; 
							}
						}
						$uniqueSkuIDs = array_unique($skuIDs);
						$countUniqueSkuIDs = sizeof($uniqueSkuIDs);
						$singleUserData['date' . $i] = $countUniqueSkuIDs;
					}

					// If value of SKUS required
					if($request->type == 'value') {
						$totalValue = 0;
						foreach($ordersOfADay as $order) {
							foreach($order->order_details as $order_detail) {
								$totalValue += $order_detail->value; 
							}
						}
						$singleUserData['date' . $i] = $totalValue;
					}
				}
				$productsOfftakes[] = $singleUserData;
			}
		}

		return response()->json([   
			'count' =>  sizeof($productsOfftakes),
			'data'  =>  $productsOfftakes,
			'daysInMonth'	=>	$daysInMonth,
		]);
	}

	public function exports(Request $request) {
		ini_set('max_execution_time', 10000);

		$date = $request->date;

		// return Carbon::parse($date)->format('d-M-Y');
		// return view('exports.daily_attendance_export', compact('userAttendances'));

		// return response()->json([
		// 	'data'	=>	Excel::store(new BAReportExport($date), "/reports/$date/BA-Report-$date.xlsx", 'local'),
		// ]);
		// return Excel::download(new BAReportExport($date, 1757), 'BA-Report.xlsx');
		return Excel::download(new BAReportExport($date,753), "BA-Report.xlsx");
		Excel::store(new BAReportExport($date), "/reports/$date/BA-Report-$date.xlsx", "local");


		$supervisors = User::with('roles')
			->where('active', '=', 1)
			->whereHas('roles',  function ($q) {
			$q->where('name', '=', 'SUPERVISOR');
			})->orderBy('name')
			// ->take(1)
			->get();
			
		foreach ($supervisors as $supervisor) {
			$name = $supervisor->name;
			Excel::store(new BAReportExport($date, $supervisor->id), "/reports/$date/$name-BAs-Report-$date.xlsx", 'local');
		}
	}
}
