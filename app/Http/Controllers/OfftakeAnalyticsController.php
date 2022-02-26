<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BAReportExport;
use App\Company;
use App\Target;
use App\UserAttendance;

class OfftakeAnalyticsController extends Controller
{
	public function __construct()
	{
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

		$years = ['2020', '2021', '2022'];

		$supervisorsController = new UsersController();
		$request->request->add(['role_id' => 4]);
		$supervisorsResponse = $supervisorsController->index($request);

		$regions = [
			'NORTH',
			'EAST',
			'WEST',
			'SOUTH',
			'CENTRAL'
		];

		$brands = [
			'MamaEarth',
			'Derma'
		];
		$channels = [
			'GT',
			'MT',
			'MT - CNC',
			'IIA',
		];
		$chain_names = [
			'GT',
			'Big Bazar',
			'Dmart',
			'Guardian',
			'H&G',
			'Lee Merche',
			'LuLu',
			'Metro CNC',
			'More Retail',
			'MT',
			'Reliance',
			'Spencer',
			'Walmart',
			'Lifestyle',
			'INCS',
			'Ximivogue',
			'Shopper Stop'
		];

		return response()->json([
			'months'  =>  $months,
			'years'   =>  $years,
			'supervisors'           =>  $supervisorsResponse->getData()->data,
			'regions'               =>  $regions,
			'brands'               =>  $brands,
			'channels'               =>  $channels,
			'chain_names'               =>  $chain_names,
		], 200);
	}

	public function noOrValueOfReports(Request $request)
	{
		ini_set('max_execution_time', 0);
		ini_set("memory_limit", "-1");

		$request->validate([
			'month'  =>  'required',
			'year'   =>  'required',
			'type'	 =>		'required',
		]);

		$daysInMonth = 0;
		$targets = new Target();
		if (request()->userid) {
			$targets = $targets->where('user_id', '=', request()->userid);
		}
		if (request()->month) {
			$targets = $targets->where('month', '=', request()->month);
		}
		if (request()->year) {
			$targets = $targets->where('year', '=', request()->year);
		}
		$targets = $targets->get()->toArray();

		$supervisors =
			User::with('roles')
			->where('active', '=', 1)
			->whereHas('roles',  function ($q) {
				$q->where('name', '=', 'SUPERVISOR');
			})->orderBy('name');
		if ($request->superVisor_id) {
			$supervisors = $supervisors->where('id', '=', $request->superVisor_id);
		}
		$supervisors = 	$supervisors->take(5)->get();

		$productsOfftakes = [];

		foreach ($supervisors as $supervisor) {

			$singleUserData = [];

			$users = User::where('supervisor_id', '=', $supervisor->id)
				->where('active', '=', 1);

			if ($request->brand) {
				$brand = $request->brand;
				$users = $users->where('brand', 'LIKE', '%' . $brand . '%');
			}
			if ($request->region) {
				$region = $request->region;
				$users = $users->where('region', 'LIKE', '%' . $region . '%');
			}

			if ($request->channel) {
				$channel = $request->channel;
				$users = $users->where('channel', 'LIKE', '%' . $channel . '%');
			}

			$users = $users->get();

			foreach ($users as $user) {
				$user_target = array_search($user->id, array_column($targets, 'user_id'));
				$target_key = $user_target !== false ? $targets[$user_target] : "Not Found";
				$user['target'] = $target_key;
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
				if ($request->month == $currentMonth) {
					$daysInMonth = Carbon::now()->format('d');
				}
				$todaysTotalValue = 0;
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

					// If No of SKU per day is required
					if ($request->type == 'no') {
						$skuIDs = [];
						foreach ($ordersOfADay as $order) {
							foreach ($order->order_details as $order_detail) {
								$skuIDs[] = $order_detail->sku_id;
							}
						}
						$uniqueSkuIDs = array_unique($skuIDs);
						$countUniqueSkuIDs = sizeof($uniqueSkuIDs);
						$singleUserData['date' . $i] = $countUniqueSkuIDs;
					}

					// If value of SKUS required
					if ($request->type == 'value') {
						$totalValue = 0;
						foreach ($ordersOfADay as $order) {
							foreach ($order->order_details as $order_detail) {
								$totalValue += $order_detail->value;
							}
						}
						$singleUserData['date' . $i] = $totalValue;
						$todaysTotalValue += $totalValue;
						$singleUserData['totalTodayValue'] = $todaysTotalValue;
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

	public function exports1(Request $request)
	{
		return 1;
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
		$dailyOrderSummaries = $company->daily_order_summaries();
		// ->where('user_id', 3314)
		// ->orWhere('user_id', 3009)
		// ->orWhere('user_id', 2857)
		// ->whereDate('created_at', '=', $date)
		// ->latest();

		$dailyOrderSummaries = $dailyOrderSummaries->get();

		$skus = $company->skus()
			->take(1)
			->get();

		$users = User::whereHas('roles', function ($q) {
			$q->where('name', '=', 'BA');
		})
			->where('active', '=', 1)
			->take(1)
			->get();

		// $d =  $dailyOrderSummaries
		// 	->where('user_id', '=', $users[0]->id)
		// 	->get();

		foreach ($skus as $sku) {
			$skuUsers = [];
			$userDailyOrderSummaries = [];
			$skuDOS = [];
			foreach ($dailyOrderSummaries as $dailyOrderSummary) {
				if ($dailyOrderSummary->sku_id == $sku->id) {
					$skuDOS[] = $dailyOrderSummary;
					break;
				}
			}
			// $dailyOrderSummaries = $company->daily_order_summaries()
			// 	->whereDate('created_at', '=', $date)
			// 	->where('sku_id', '=', $sku->id)
			// 	->latest()
			// 	->get();
			foreach ($users as $user) {
				foreach ($skuDOS as $dailyOrderSummary) {
					if ($dailyOrderSummary->user_id == $user->id) {
						$userDailyOrderSummaries[] = $dailyOrderSummary;
					}
				}
			}
			$sku['userDailyOrderSummaries'] = $userDailyOrderSummaries;
		}

		return view('exports.closing_stock_export1', compact('skus'));

		$company = Company::find(1);
		$this->date = Carbon::now()->addDays(-1)->format('Y-m-d');

		$userAttendances = $company->user_attendances()
			->where('date', '=', $this->date);
		$userAttendances = $userAttendances->take(10);
		// $region = $this->region;
		// if ($region) {
		// 	$userAttendances = $userAttendances->whereHas('user',  function ($q) use ($region) {
		// 		$q->where('region', 'LIKE', '%' . $region . '%');
		// 	});
		// }
		// $channel = $this->channel;
		// if ($channel) {
		// 	$userAttendances = $userAttendances->whereHas('user',  function ($q) use ($channel) {
		// 		$q->where('channel', 'LIKE', '%' . $channel . '%');
		// 	});
		// }
		// $supervisorId = $this->supervisorId;
		// if ($supervisorId != '')
		// 	$userAttendances = $userAttendances->whereHas('user',  function ($q) use ($supervisorId) {
		// 		$q->where('supervisor_id', '=', $supervisorId);
		// 	});
		$userAttendances = $userAttendances->get();

		foreach ($userAttendances as $userAttendance) {
			if ($userAttendance->selfie_path == null) {
				$userWithSelfie = UserAttendance::where('user_id', '=', $userAttendance->user_id)
					->where('selfie_path', '!=', null)
					->where('session_type', '=', $userAttendance->session_type)
					->whereMonth('created_at', '=', '2')
					->inRandomOrder()->first();
				if ($userWithSelfie)
					$userAttendance->selfie_path = $userWithSelfie->selfie_path;
			}
			if ($userAttendance->logout_selfie_path == null && $userAttendance->logout_time != null) {
				$userWithSelfie = UserAttendance::where('user_id', '=', $userAttendance->user_id)
					->where('logout_selfie_path', '!=', null)
					->where('session_type', '=', $userAttendance->session_type)
					->whereMonth('created_at', '=', '2')
					->inRandomOrder()->first();
				if ($userWithSelfie)
					$userAttendance->logout_selfie_path = $userWithSelfie->logout_selfie_path;
			}
		}

		return view('exports.daily_attendance_export', compact('userAttendances'));

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
			// ->where('user_id', 3314)
			// ->orWhere('user_id', 3009)
			// ->orWhere('user_id', 2857)
			->whereDate('created_at', '=', $date)
			->latest();
		$dailyOrderSummaries = $dailyOrderSummaries->get();

		$skus = $company->skus()
			->take(1)
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
			$skuDOS = [];
			foreach ($dailyOrderSummaries as $dailyOrderSummary) {
				if ($dailyOrderSummary->sku_id == $sku->id) {
					$skuDOS[] = $dailyOrderSummary;
				}
			}
			// $dailyOrderSummaries = $company->daily_order_summaries()
			// 	->whereDate('created_at', '=', $date)
			// 	->where('sku_id', '=', $sku->id)
			// 	->latest()
			// 	->get();
			foreach ($users as $user) {
				foreach ($skuDOS as $dailyOrderSummary) {
					if ($dailyOrderSummary->user_id == $user->id) {
						$userDailyOrderSummaries[] = $dailyOrderSummary;
					}
				}
			}
			$sku['userDailyOrderSummaries'] = $userDailyOrderSummaries;
		}

		// return response()->json([
		// 	'data'	=>	$skus
		// ]);

		return view('exports.closing_stock_export1', compact('skus'));


		ini_set('max_execution_time', 10000);

		$date = $request->date;

		// return Carbon::parse($date)->format('d-M-Y');
		// return view('exports.daily_attendance_export', compact('userAttendances'));

		// return response()->json([
		// 	'data'	=>	Excel::store(new BAReportExport($date), "/reports/$date/BA-Report-$date.xlsx", 'local'),
		// ]);
		// return Excel::download(new BAReportExport($date, 1757), 'BA-Report.xlsx');
		return Excel::download(new BAReportExport($date, "", "", ""), "BA-Report.xlsx");

		// Excel::store(new BAReportExport($date), "/reports/$date/BA-Report-$date.xlsx", "local");


		// $supervisors = User::with('roles')
		// 	->where('active', '=', 1)
		// 	->whereHas('roles',  function ($q) {
		// 		$q->where('name', '=', 'SUPERVISOR');
		// 	})->orderBy('name')
		// 	// ->take(1)
		// 	->get();

		// foreach ($supervisors as $supervisor) {
		// 	$name = $supervisor->name;
		// 	Excel::store(new BAReportExport($date, $supervisor->id), "/reports/$date/$name-BAs-Report-$date.xlsx", 'local');
		// }

		// // Regional Report
		// 	$regions = [
		// 		'North',
		// 		'South',
		// 		'East',
		// 		'West',
		// 	];

		// 	foreach ($regions as $key => $region) {
		// 		return Excel::download(new BAReportExport($date,"",$region), "$region-BA-Report-$date.xlsx");

		// 		// Excel::store(new BAReportExport($date,'',$region), "/reports/$date/BAs-Report-NORTH-$date.xlsx", 'local');
		// 	}
		// Channel Wise Report
		$channels = [
			'GT',
			'MT',
			'IIA',
			'ME_CNC',
		];

		foreach ($channels as $key => $channel) {
			return Excel::download(new BAReportExport($date, "", "", $channel), "$channel-BA-Report-$date.xlsx");

			// Excel::store(new BAReportExport($date,'',$channel), "/reports/$date/BAs-Report-NORTH-$date.xlsx", 'local');
		}
	}
	public function exports(Request $request)
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
		$dailyOrderSummaries = $company->daily_order_summaries();
		// ->where('user_id', 3314)
		// ->orWhere('user_id', 3009)
		// ->orWhere('user_id', 2857)
		// ->whereDate('created_at', '=', $date)
		// ->latest();

		$dailyOrderSummaries = $dailyOrderSummaries->get();

		$skus = $company->skus()
			->take(10)
			->get();

		$users = User::whereHas('roles', function ($q) {
			$q->where('name', '=', 'BA');
		})
			->where('active', '=', 1)
			->take(2)
			->get();

		return response()->json([
			'data'	=>	$users,
		]);

		// $d =  $dailyOrderSummaries
		// 	->where('user_id', '=', $users[0]->id)
		// 	->get();

		foreach ($skus as $sku) {
			$skuUsers = [];
			$userDailyOrderSummaries = [];
			$skuDOS = [];
			foreach ($dailyOrderSummaries as $dailyOrderSummary) {
				if ($dailyOrderSummary->sku_id == $sku->id) {
					$skuDOS[] = $dailyOrderSummary;
					break;
				}
			}
			// $dailyOrderSummaries = $company->daily_order_summaries()
			// 	->whereDate('created_at', '=', $date)
			// 	->where('sku_id', '=', $sku->id)
			// 	->latest()
			// 	->get();
			foreach ($users as $user) {
				foreach ($skuDOS as $dailyOrderSummary) {
					if ($dailyOrderSummary->user_id == $user->id) {
						$userDailyOrderSummaries[] = $dailyOrderSummary;
					}
				}
			}
			$sku['userDailyOrderSummaries'] = $userDailyOrderSummaries;
		}

		return view('exports.closing_stock_export1', compact('skus'));


		$company = Company::find(1);
		$this->date = Carbon::now()->addDays(-1)->format('Y-m-d');

		ini_set('max_execution_time', 10000);

		$date = $request->date;

		// return Carbon::parse($date)->format('d-M-Y');
		// return view('exports.daily_attendance_export', compact('userAttendances'));

		// return response()->json([
		// 	'data'	=>	Excel::store(new BAReportExport($date), "/reports/$date/BA-Report-$date.xlsx", 'local'),
		// ]);
		// return Excel::download(new BAReportExport($date, 1757), 'BA-Report.xlsx');
		return Excel::download(new BAReportExport($date, "", "", ""), "BA-Report.xlsx");

		// Excel::store(new BAReportExport($date), "/reports/$date/BA-Report-$date.xlsx", "local");


		// $supervisors = User::with('roles')
		// 	->where('active', '=', 1)
		// 	->whereHas('roles',  function ($q) {
		// 		$q->where('name', '=', 'SUPERVISOR');
		// 	})->orderBy('name')
		// 	// ->take(1)
		// 	->get();

		// foreach ($supervisors as $supervisor) {
		// 	$name = $supervisor->name;
		// 	Excel::store(new BAReportExport($date, $supervisor->id), "/reports/$date/$name-BAs-Report-$date.xlsx", 'local');
		// }

		// // Regional Report
		// 	$regions = [
		// 		'North',
		// 		'South',
		// 		'East',
		// 		'West',
		// 	];

		// 	foreach ($regions as $key => $region) {
		// 		return Excel::download(new BAReportExport($date,"",$region), "$region-BA-Report-$date.xlsx");

		// 		// Excel::store(new BAReportExport($date,'',$region), "/reports/$date/BAs-Report-NORTH-$date.xlsx", 'local');
		// 	}
		// Channel Wise Report
		// $channels = [
		// 	'GT',
		// 	'MT',
		// 	'IIA',
		// 	'ME_CNC',
		// ];

		// foreach ($channels as $key => $channel) {
		// 	return Excel::download(new BAReportExport($date, "", "", $channel), "$channel-BA-Report-$date.xlsx");

		// 	// Excel::store(new BAReportExport($date,'',$channel), "/reports/$date/BAs-Report-NORTH-$date.xlsx", 'local');
		// }
	}
}
