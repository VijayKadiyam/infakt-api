<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BAReportExport;
use App\Company;
use App\DailyOrderSummary;
use App\MonthlyOrderSummary;
use App\Order;
use App\Sku;
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
	public function exports2(Request $request)
	{
		ini_set('max_execution_time', 0);

		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');

		// DailyOrderSummary::truncate();

		return 1;

		$skus = Sku::all();

		// $users = [
		//     User::find(1516),
		// ];

		$users = User::whereHas('roles', function ($q) {
			$q->where('name', '=', 'BA');
		})
			->take(1)
			->get();

		foreach ($users as $user) {
			if ($user) {

				// Get previous month closing

				$orders = [];
				if ($user)
					$orders = Order::whereYear('created_at', Carbon::now())
						->whereMonth('created_at', Carbon::now())
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

					// DailyOrderSummary::create([
					// 	'company_id'  =>  1,
					// 	'user_id' =>  $user->id,
					// 	'sku_id'  =>  $sku->id,
					// 	'opening_stock' =>  $sku['opening_stock'],
					// 	'received_stock' =>  $sku['received_stock'],
					// 	'purchase_returned_stock' =>  $sku['purchase_returned_stock'],
					// 	'sales_stock' =>  $sku['sales_stock'],
					// 	'returned_stock' =>  $sku['returned_stock'],
					// 	'closing_stock' =>  $sku['closing_stock'],
					// ]);
				}
			}
		}

		return 'Done';





		// $currentMonth = Carbon::now()->format('m');
		// $previousMonthDate = Carbon::parse('01-' . ($currentMonth - 1) . '-2022');
		// $previousMonthDaysInMonth = $previousMonthDate->daysInMonth;
		// return $previousMonthDaysInMonth;

		ini_set('max_execution_time', 0);

		// MonthlyOrderSummary::truncate();

		$skus = Sku::get();

		$users = [
			User::find(1515),
		];

		// $users = User::whereHas('roles', function ($q) {
		// 	$q->where('name', '=', 'BA');
		// })->get();

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


					// MonthlyOrderSummary::create([
					// 	'company_id'  =>  1,
					// 	'user_id' =>  $user->id,
					// 	'sku_id'  =>  $sku->id,
					// 	'month'		=>	$previousMonth,
					// 	'year'		=>	'2022',
					// 	'opening_stock' =>  $sku['opening_stock'],
					// 	'received_stock' =>  $sku['received_stock'],
					// 	'purchase_returned_stock' =>  $sku['purchase_returned_stock'],
					// 	'sales_stock' =>  $sku['sales_stock'],
					// 	'returned_stock' =>  $sku['returned_stock'],
					// 	'closing_stock' =>  $sku['closing_stock'],
					// ]);
				}
			}
		}

		return 1;

		// Stock Report
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');

		$asd = [];
		$company = Company::find(1);
		$this->date = Carbon::now()->format('Y-m-d');
		$this->region = '';
		$this->channel = '';
		$this->supervisorId = '';
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

		$dailyOrderSummaries = $dailyOrderSummaries->take(20000);
		$dailyOrderSummaries = $dailyOrderSummaries->get();

		$total_opening_stocks = 0;
		$total_closing_stocks = 0;
		$total_received_stock = 0;
		$total_purchase_returned_stock = 0;
		$total_sales_stock = 0;
		$total_returned_stock = 0;
		$users = [];

		foreach ($dailyOrderSummaries as $dos) {
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
					$user['total_opening_stocks'] = $total_opening_stocks;
					$user['total_closing_stocks'] = $total_closing_stocks;
					$user['total_received_stock'] = $total_received_stock;
					$user['total_purchase_returned_stock'] = $total_purchase_returned_stock;
					$user['total_sales_stock'] = $total_sales_stock;
					$user['total_returned_stock'] = $total_returned_stock;
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
		// ->where('user_id', 3314);
		// ->orWhere('user_id', 3009)
		// ->orWhere('user_id', 2857)
		// ->whereDate('created_at', '=', $date)
		// ->latest();

		$dailyOrderSummaries = $dailyOrderSummaries->get();

		$skus = $company->skus()
			->take(2)
			->get();

		$users = User::whereHas('roles', function ($q) {
			$q->where('name', '=', 'BA');
		})
			->where('active', '=', 1)
			// ->where('id', 3314)
			->take(1)
			->get();

		// return response()->json([
		// 	'data'	=>	$users,
		// ]);

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
					// break;
				}
			}
			foreach ($users as $user) {
				$check = 0;
				foreach ($skuDOS as $dailyOrderSummary) {
					if ($dailyOrderSummary->user_id == $user->id && $check != 1) {
						$check = 1;
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
		return Excel::download(new BAReportExport($date, "", "", "", ""), "BA-Report.xlsx");

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
	public function exports(Request $request)
	{
		ini_set('max_execution_time', 0);

		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');

		$date = $request->date;

		return Excel::download(new BAReportExport($date, "", "", "", ""), "BA-Report.xlsx");
	}

	public function category_wise_report()
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);
		$current_month = Carbon::now()->format('m');
		$orders = request()->company->orders_list()
			->where(['is_active' => 1, 'order_type' => 'sales'])
			->with('order_details')
			->whereHas('order_details',  function ($q) use ($current_month) {
				$q->with('sku');
				$q->whereMonth('created_at', '=', $current_month);
			});
		$orders = $orders->get();
		$category_list = [];
		$total_value = 0;
		foreach ($orders as $key => $order) {
			foreach ($order->order_details as $key => $ord) {

				$sku = $ord->sku;
				$category = $sku->main_category;
				$value_name = 'total_' . $category . '_value';
				if (!in_array($category, $category_list)) {
					// Initial
					$category_list[] = $category;
					$$value_name = $ord->value;
				} else {
					// Existing
					$$value_name += $ord->value;
				}

				$total_value += $ord->value;

				$Final_Report[$category] = [
					'category' => $category,
					'value' => $$value_name
				];
			}
		}

		foreach ($Final_Report as $key => $category) {
			$value = $category['value'];
			$cate = $category['category'];
			$contro = ($value / $total_value) * 100;

			$Final_Report[$cate]['contro'] = round($contro, 2);
		}
		return response()->json([
			'data'     =>  $Final_Report,
			'count' => sizeof($Final_Report),
			'success' =>  true
		], 200);
	}
	public function TVA_report(Request $request)
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);
		$now = Carbon::now()->format('Y-m-d');
		$month =  Carbon::parse($now)->format('m');
		$year =  Carbon::parse($now)->format('Y');

		$type = $request->type;
		$users = request()->company->users()->with('roles')
			->whereHas('roles',  function ($q) {
				$q->where('name', '!=', 'Admin');
				$q->where('name', '!=', 'DISTRIBUTOR');
			});
		$users = $users->with(
			[
				'targets' => fn ($q) => $q->where(['month' => $month, 'year' => $year])
			]
		);
		$users = $users->get();
		if ($type == 1) {
			// Filter Type Region
			$total_North_App_id = 0;
			$total_South_App_id = 0;
			$total_East_App_id = 0;
			$total_West_App_id = 0;

			$total_North_Target = 0;
			$total_South_Target = 0;
			$total_East_Target = 0;
			$total_West_Target = 0;

			$total_North_Achieved = 0;
			$total_South_Achieved = 0;
			$total_East_Achieved = 0;
			$total_West_Achieved = 0;

			$Active_North_Ba_Count = 0;
			$Active_South_Ba_Count = 0;
			$Active_East_Ba_Count = 0;
			$Active_West_Ba_Count = 0;

			foreach ($users as $key => $user) {
				$region = str_replace(" ", "", $user['region']);
				$target = sizeOf($user->targets) ? $user->targets[0]->target : 0;
				$achieved = sizeOf($user->targets) ? $user->targets[0]->achieved : 0;
				switch ($region) {
					case 'North':
						$total_North_App_id++;
						if ($user['active'] == true) {
							$Active_North_Ba_Count++;
						}
						if ($target) {
							$total_North_Target += $target;
							$total_North_Achieved += $achieved;
						}
						break;
					case 'South':
						$total_South_App_id++;
						if ($user['active'] == true) {
							$Active_South_Ba_Count++;
						}
						if ($target) {
							$total_South_Target += $target;
							$total_South_Achieved += $achieved;
						}
						break;
					case 'East':
						$total_East_App_id++;
						if ($user['active'] == true) {
							$Active_East_Ba_Count++;
						}
						if ($target) {
							$total_East_Target += $target;
							$total_East_Achieved += $achieved;
						}
						break;
					case 'West':
						$total_West_App_id++;
						if ($user['active'] == true) {
							$Active_West_Ba_Count++;
						}
						if ($target) {
							$total_West_Target += $target;
							$total_West_Achieved += $achieved;
						}
						break;

					default:
						# code...
						break;
				}
			}

			$TVA_report['North'] = [
				'Store_Count' => $total_North_App_id,
				'Target' => $total_North_Target,
				'Achieved' => $total_North_Achieved,
				'Achieved_percentage' =>  $total_North_Achieved / $total_North_Target * 100,
			];
			$TVA_report['South'] = [
				'Store_Count' => $total_South_App_id,
				'Target' => $total_South_Target,
				'Achieved' => $total_South_Achieved,
				'Achieved_percentage' =>  $total_South_Achieved / $total_South_Target * 100,

			];
			$TVA_report['East'] = [
				'Store_Count' => $total_East_App_id,
				'Target' => $total_East_Target,
				'Achieved' => $total_East_Achieved,
				'Achieved_percentage' =>  $total_East_Achieved / $total_East_Target * 100,

			];
			$TVA_report['West'] = [
				'Store_Count' => $total_West_App_id,
				'Target' => $total_West_Target,
				'Achieved' => $total_West_Achieved,
				'Achieved_percentage' =>  $total_West_Achieved / $total_West_Target * 100,

			];
		} elseif ($type == 2) {
			// Filter Type Channel
			$total_GT_App_id = 0;
			$total_MT_App_id = 0;
			$total_MT_CNC_App_id = 0;
			$total_IIA_App_id = 0;

			$total_GT_Target = 0;
			$total_MT_Target = 0;
			$total_MT_CNC_Target = 0;
			$total_IIA_Target = 0;

			$total_GT_Achieved = 0;
			$total_MT_Achieved = 0;
			$total_MT_CNC_Achieved = 0;
			$total_IIA_Achieved = 0;

			$Active_GT_Ba_Count = 0;
			$Active_MT_Ba_Count = 0;
			$Active_MT_CNC_Ba_Count = 0;
			$Active_IIA_Ba_Count = 0;
			foreach ($users as $key => $user) {
				$channel = str_replace(" ", "", $user['channel']);
				$target = sizeOf($user->targets) ? $user->targets[0]->target : 0;
				$achieved = sizeOf($user->targets) ? $user->targets[0]->achieved : 0;
				switch ($channel) {
					case 'GT':
						$total_GT_App_id++;
						if ($user['active'] == true) {
							$Active_GT_Ba_Count++;
						}
						if ($target) {
							$total_GT_Target += $target;
							$total_GT_Achieved += $achieved;
						}
						break;
					case 'MT':
						$total_MT_App_id++;
						if ($user['active'] == true) {
							$Active_MT_Ba_Count++;
						}
						if ($target) {
							$total_MT_Target += $target;
							$total_MT_Achieved += $achieved;
						}
						break;
					case 'MT-CNC':
						$total_MT_CNC_App_id++;
						if ($user['active'] == true) {
							$Active_MT_CNC_Ba_Count++;
						}
						if ($target) {
							$total_MT_CNC_Target += $target;
							$total_MT_CNC_Achieved += $achieved;
						}
						break;
					case 'IIA':
						$total_IIA_App_id++;
						if ($user['active'] == true) {
							$Active_IIA_Ba_Count++;
						}
						if ($target) {
							$total_IIA_Target += $target;
							$total_IIA_Achieved += $achieved;
						}
						break;

					default:
						# code...
						break;
				}
			}
			$TVA_report['GT'] = [
				'Store_Count' => $total_GT_App_id,
				'Target' => $total_GT_Target,
				'Achieved' => $total_GT_Achieved,
				'Achieved_percentage' =>  $total_GT_Achieved / $total_GT_Target * 100,
			];
			$TVA_report['MT'] = [
				'Store_Count' => $total_MT_App_id,
				'Target' => $total_MT_Target,
				'Achieved' => $total_MT_Achieved,
				'Achieved_percentage' =>  $total_MT_Achieved / $total_MT_Target * 100,

			];
			$TVA_report['MT - CNC'] = [
				'Store_Count' => $total_MT_CNC_App_id,
				'Target' => $total_MT_CNC_Target,
				'Achieved' => $total_MT_CNC_Achieved,
				'Achieved_percentage' =>  $total_MT_CNC_Achieved / $total_MT_CNC_Target * 100,

			];
			$TVA_report['IIA'] = [
				'Store_Count' => $total_IIA_App_id,
				'Target' => $total_IIA_Target,
				'Achieved' => $total_IIA_Achieved,
				'Achieved_percentage' =>  $total_IIA_Achieved / $total_IIA_Target * 100,

			];
		} else {
			// Filter Type ASM
			$ASM_list = [];
			foreach ($users as $key => $user) {
				$asm = strtoupper(str_replace(" ", "", $user['asm']));
				if ($asm != "" && $asm != "DEMO") {
					$total_name = 'total_' . $asm . '_App_id';
					$target_name = 'total_' . $asm . '_Target';
					$achieved_name = 'total_' . $asm . '_Achieved';
					$Active_name = 'Active_' . $asm . '_Ba_Count';
					$target = sizeOf($user->targets) ? $user->targets[0]->target : 0;
					$achieved = sizeOf($user->targets) ? $user->targets[0]->achieved : 0;
					if (!in_array($asm, $ASM_list)) {
						// Initial
						$ASM_list[] = $asm;
						$$total_name = 1;
						$$Active_name = 1;
						if ($target) {
							$$target_name = $target;
							$$achieved_name = $achieved;
						} else {
							$$target_name = 0;
							$$achieved_name = 0;
						}
					} else {
						// Existing
						$$total_name = $$total_name + 1;
						$$Active_name = $$Active_name + 1;
						if ($target) {
							$$target_name += $target;
							$$achieved_name += $achieved;
						}
					}
				}
			}
			foreach ($ASM_list as $key => $list) {
				$total_name = 'total_' . $list . '_App_id';
				$target_name = 'total_' . $list . '_Target';
				$achieved_name = 'total_' . $list . '_Achieved';
				$Active_name = 'Active_' . $list . '_Ba_Count';
				$TVA_report[$list] = [
					'Store_Count' => $$total_name,
					'Target' => $$target_name,
					'Achieved' => $$achieved_name,
					// 'Achieved_percentage' =>  $$achieved_name / $$target_name * 100,
				];
				if ($$target_name == 0) {
					$TVA_report[$list]['Achieved_percentage'] = 0;
				} else {
					$TVA_report[$list]['Achieved_percentage'] = $$achieved_name / $$target_name * 100;
				}
			}
		}


		return response()->json([
			'data'     =>  $TVA_report,
			'count' => sizeof($TVA_report),
			'success' =>  true
		], 200);
	}

	public function Top_Supervisor_report(Request $request)
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);
		$now = Carbon::now()->format('Y-m-d');
		$month =  Carbon::parse($now)->format('m');
		$year =  Carbon::parse($now)->format('Y');

		$type = $request->type;
		$users = request()->company->users()->with('roles')
			->whereHas('roles',  function ($q) {
				$q->where('name', '!=', 'Admin');
				$q->where('name', '!=', 'DISTRIBUTOR');
			});
		$users = $users->with('supervisor');
		$users = $users->get();
		$productsOfftakes = [];
		$Supervisor_list = [];
		foreach ($users as $user) {
			$singleUserData['user'] = $user;
			if ($user->supervisor) {
				$supervisor_name =  $user->supervisor->name;
				$ors = $request->company->orders_list()
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
					$totalValue = 0;
					foreach ($ordersOfADay as $order) {
						foreach ($order->order_details as $order_detail) {
							$totalValue += $order_detail->value;
						}
					}
					// $singleUserData['date' . $i] = $totalValue;
					$todaysTotalValue += $totalValue;
					$singleUserData['totalTodayValue'] = $todaysTotalValue;
				}
				$abc = str_replace(" ", "", $supervisor_name);
				$total_name = 'total_' . $abc . '_value';
				$ba_count = 'total_' . $abc . '_BA_count';
				if (!in_array($supervisor_name, $Supervisor_list)) {
					// Initial
					$Supervisor_list[] = $supervisor_name;
					$$ba_count = 1;
					$$total_name = $todaysTotalValue;
				} else {
					// Existing
					$$ba_count++;
					$$total_name += $todaysTotalValue;
				}
				$Total_list[$supervisor_name] = [
					'total_value' => $$total_name,
					'Ba_count' => $$ba_count,
					'region' => $user->supervisor->region,
					'channel' => str_replace(" ", "", $user->channel),
					'name' => $supervisor_name,
				];
				$productsOfftakes[] = $singleUserData;
			}
		}
		if ($type == 1) {
			// Filter Type Region
			foreach ($Total_list as $key => $supervisor) {
				if ($supervisor['region']) {
					$average = $supervisor['total_value'] / $supervisor['Ba_count'];
					$supervisor['average'] = $average;
					switch ($supervisor['region']) {
						case 'NORTH':
							$North_Supervisors[] = $supervisor;
							break;
						case 'SOUTH':
							$South_Supervisors[] = $supervisor;
							break;
						case 'EAST':
							$East_Supervisors[] = $supervisor;
							break;
						case 'WEST':
							$West_Supervisors[] = $supervisor;
							break;
						default:
							break;
					}
				}
			}
			// Top Five
			$Bottom_North = $North_Supervisors;
			usort($North_Supervisors, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});
			$Bottom_South = $South_Supervisors;
			usort($South_Supervisors, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});
			$Bottom_East = $East_Supervisors;
			usort($East_Supervisors, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});
			$Bottom_West = $West_Supervisors;
			usort($West_Supervisors, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});
			// Bottom 5
			usort($Bottom_North, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			usort($Bottom_South, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			usort($Bottom_East, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			usort($Bottom_West, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			$Final_Report = [];
			$Final_Report['North']['Top_Supervisor'] = array_slice($North_Supervisors, 0, 5);;
			$Final_Report['North']['Bottom_Supervisor'] = array_slice($Bottom_North, 0, 5);
			$Final_Report['South']['Top_Supervisor'] = array_slice($South_Supervisors, 0, 5);;
			$Final_Report['South']['Bottom_Supervisor'] = array_slice($Bottom_South, 0, 5);
			$Final_Report['East']['Top_Supervisor'] = array_slice($East_Supervisors, 0, 5);;
			$Final_Report['East']['Bottom_Supervisor'] = array_slice($Bottom_East, 0, 5);
			$Final_Report['West']['Top_Supervisor'] = array_slice($West_Supervisors, 0, 5);;
			$Final_Report['West']['Bottom_Supervisor'] = array_slice($Bottom_West, 0, 5);
		} else {
			// Filter Type Channel
			foreach ($Total_list as $key => $supervisor) {
				if ($supervisor['channel']) {
					$average = $supervisor['total_value'] / $supervisor['Ba_count'];
					$supervisor['average'] = $average;
					switch ($supervisor['channel']) {
						case 'GT':
							$GT_Supervisors[] = $supervisor;
							break;
						case 'MT':
							$MT_Supervisors[] = $supervisor;
							break;
						case 'MT-CNC':
							$MT_CNC_Supervisors[] = $supervisor;
							break;
						case 'IIA':
							$IIA_Supervisors[] = $supervisor;
							break;
						default:
							break;
					}
				}
			}
			// Top Five
			$Bottom_GT = $GT_Supervisors;
			usort($GT_Supervisors, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});

			$Bottom_MT = $MT_Supervisors;
			usort($MT_Supervisors, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});

			$Bottom_MT_CNC = $MT_CNC_Supervisors;
			usort($MT_CNC_Supervisors, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});

			$Bottom_IIA = $IIA_Supervisors;
			usort($IIA_Supervisors, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});
			// Bottom 5
			usort($Bottom_GT, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			usort($Bottom_MT, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			usort($Bottom_MT_CNC, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			usort($Bottom_IIA, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			$Final_Report = [];
			$Final_Report['GT']['Top_Supervisor'] = array_slice($GT_Supervisors, 0, 5);;
			$Final_Report['GT']['Bottom_Supervisor'] = array_slice($Bottom_GT, 0, 5);
			$Final_Report['MT']['Top_Supervisor'] = array_slice($MT_Supervisors, 0, 5);;
			$Final_Report['MT']['Bottom_Supervisor'] = array_slice($Bottom_MT, 0, 5);
			$Final_Report['MT_CNC']['Top_Supervisor'] = array_slice($MT_CNC_Supervisors, 0, 5);;
			$Final_Report['MT_CNC']['Bottom_Supervisor'] = array_slice($Bottom_MT_CNC, 0, 5);
			$Final_Report['IIA']['Top_Supervisor'] = array_slice($IIA_Supervisors, 0, 5);;
			$Final_Report['IIA']['Bottom_Supervisor'] = array_slice($Bottom_IIA, 0, 5);
		}

		return response()->json([
			'data'     =>  $Final_Report,
			'count' => sizeof($Final_Report),
			'success' =>  true
		], 200);
	}
	public function Top_ASM_report(Request $request)
	{
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);
		$now = Carbon::now()->format('Y-m-d');
		$month =  Carbon::parse($now)->format('m');
		$year =  Carbon::parse($now)->format('Y');

		$type = $request->type;
		$users = request()->company->users()->with('roles')
			->whereHas('roles',  function ($q) {
				$q->where('name', '!=', 'Admin');
				$q->where('name', '!=', 'DISTRIBUTOR');
			});
		$users = $users->get();
		$productsOfftakes = [];
		$ASM_list = [];
		foreach ($users as $user) {
			$singleUserData['user'] = $user;
			$asm = strtoupper(str_replace(" ", "", $user->asm));
			if ($asm != "" && $asm != "DEMO") {
				$ors = $request->company->orders_list()
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
					$totalValue = 0;
					foreach ($ordersOfADay as $order) {
						foreach ($order->order_details as $order_detail) {
							$totalValue += $order_detail->value;
						}
					}
					// $singleUserData['date' . $i] = $totalValue;
					$todaysTotalValue += $totalValue;
					$singleUserData['totalTodayValue'] = $todaysTotalValue;
				}
				$abc = str_replace(" ", "", $asm);
				$total_name = 'total_' . $abc . '_value';
				$ba_count = 'total_' . $abc . '_BA_count';
				if (!in_array($asm, $ASM_list)) {
					// Initial
					$ASM_list[] = $asm;
					$$ba_count = 1;
					$$total_name = $todaysTotalValue;
				} else {
					// Existing
					$$ba_count++;
					$$total_name += $todaysTotalValue;
				}
				$Total_list[$asm] = [
					'total_value' => $$total_name,
					'Ba_count' => $$ba_count,
					'region' => strtoupper(str_replace(" ", "", $user->region)),
					'channel' => str_replace(" ", "", $user->channel),
					'name' => $asm,
				];
				$productsOfftakes[] = $singleUserData;
			}
		}
		// return $Total_list;
		if ($type == 1) {
			// Filter Type Region
			$North_ASMs = [];
			$South_ASMs = [];
			$East_ASMs = [];
			$West_ASMs = [];
			foreach ($Total_list as $key => $asm) {
				if ($asm['region']) {
					$average = $asm['total_value'] / $asm['Ba_count'];
					$asm['average'] = $average;

					switch ($asm['region']) {
						case 'NORTH':
							$North_ASMs[] = $asm;
							break;
						case 'SOUTH':
							$South_ASMs[] = $asm;
							break;
						case 'EAST':
							$East_ASMs[] = $asm;
							break;
						case 'WEST':
							$West_ASMs[] = $asm;
							break;
						default:
							break;
					}
				}
			}
			// Top Five
			$Bottom_North = $North_ASMs;
			usort($North_ASMs, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});
			$Bottom_South = $South_ASMs;
			usort($South_ASMs, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});
			$Bottom_East = $East_ASMs;
			usort($East_ASMs, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});
			$Bottom_West = $West_ASMs;
			usort($West_ASMs, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});
			// Bottom 5
			usort($Bottom_North, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			usort($Bottom_South, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			usort($Bottom_East, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			usort($Bottom_West, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			$Final_Report = [];
			$Final_Report['North']['Top_ASM'] = array_slice($North_ASMs, 0, 5);;
			$Final_Report['North']['Bottom_ASM'] = array_slice($Bottom_North, 0, 5);
			$Final_Report['South']['Top_ASM'] = array_slice($South_ASMs, 0, 5);;
			$Final_Report['South']['Bottom_ASM'] = array_slice($Bottom_South, 0, 5);
			$Final_Report['East']['Top_ASM'] = array_slice($East_ASMs, 0, 5);;
			$Final_Report['East']['Bottom_ASM'] = array_slice($Bottom_East, 0, 5);
			$Final_Report['West']['Top_ASM'] = array_slice($West_ASMs, 0, 5);;
			$Final_Report['West']['Bottom_ASM'] = array_slice($Bottom_West, 0, 5);
		} else {
			// Filter Type Channel
			foreach ($Total_list as $key => $asm) {
				if ($asm['channel']) {
					$average = $asm['total_value'] / $asm['Ba_count'];
					$asm['average'] = $average;
					switch ($asm['channel']) {
						case 'GT':
							$GT_ASMs[] = $asm;
							break;
						case 'MT':
							$MT_ASMs[] = $asm;
							break;
						case 'MT-CNC':
							$MT_CNC_ASMs[] = $asm;
							break;
						case 'IIA':
							$IIA_ASMs[] = $asm;
							break;
						default:
							break;
					}
				}
			}
			// Top Five
			$Bottom_GT = $GT_ASMs;
			usort($GT_ASMs, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});

			$Bottom_MT = $MT_ASMs;
			usort($MT_ASMs, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});

			$Bottom_MT_CNC = $MT_CNC_ASMs;
			usort($MT_CNC_ASMs, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});

			$Bottom_IIA = $IIA_ASMs;
			usort($IIA_ASMs, function ($a, $b) {
				return $b['total_value'] - $a['total_value'];
			});
			// Bottom 5
			usort($Bottom_GT, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			usort($Bottom_MT, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			usort($Bottom_MT_CNC, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			usort($Bottom_IIA, function ($a, $b) {
				return $a['total_value'] - $b['total_value'];
			});
			$Final_Report = [];
			$Final_Report['GT']['Top_ASM'] = array_slice($GT_ASMs, 0, 5);;
			$Final_Report['GT']['Bottom_ASM'] = array_slice($Bottom_GT, 0, 5);
			$Final_Report['MT']['Top_ASM'] = array_slice($MT_ASMs, 0, 5);;
			$Final_Report['MT']['Bottom_ASM'] = array_slice($Bottom_MT, 0, 5);
			$Final_Report['MT_CNC']['Top_ASM'] = array_slice($MT_CNC_ASMs, 0, 5);;
			$Final_Report['MT_CNC']['Bottom_ASM'] = array_slice($Bottom_MT_CNC, 0, 5);
			$Final_Report['IIA']['Top_ASM'] = array_slice($IIA_ASMs, 0, 5);;
			$Final_Report['IIA']['Bottom_ASM'] = array_slice($Bottom_IIA, 0, 5);
		}

		return response()->json([
			'data'     =>  $Final_Report,
			'count' => sizeof($Final_Report),
			'success' =>  true
		], 200);
	}
}
