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
	public function exports(Request $request)
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
