<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ReferencePlan;
use App\UserReferencePlan;
use App\User;
use App\Order;
use App\Retailer;
use App\Sku;
use App\Stock;
use Carbon\Carbon;

class ReferencePlansController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  /*
   * To get all reference plans
     *
   *@
   */
  public function index(Request $request)
  {
    $reference_plans = [];
    $count = 0;
    if ($request->userId) {
      $user = User::find($request->userId);
      $whichWeek = $request->weekNo / 12;
      if ($user->beat_type_id == 1)
        $whichWeek = 1;
      else if ($user->beat_type_id == 2 && $whichWeek == 3)
        $whichWeek = 1;
      else if ($user->beat_type_id == 2 && $whichWeek == 4)
        $whichWeek = 2;
      // $whichWeek = 1;
      // if($user->beat_type_id != null) {
      //   if($user->beat_type_id != 1) {
      //     $whichWeek = $request->weekNo / $user->beat_type_id;
      //   }
      // }
      $user_reference_plans = UserReferencePlan::where('user_id', '=', $user->id)
        ->where('day', '=', $request->day)
        // ->where('which_week', '=', $whichWeek)
        ->get();

      $totalOrderValue = 0;
      foreach ($user_reference_plans as $user_reference_plan) {
        $referencePlanNames[] = $user_reference_plan->reference_plan->name;
        $totalOutlets = sizeof($user_reference_plan->reference_plan->retailers);
        $rfmtd = 0;
        $rfl3m = 0;
        $ordersTaken = 0;
        foreach ($user_reference_plan->reference_plan->retailers as $retailer) {
          // $retailer['mtd'] = 0;
          // $retailer['l3m']  = 0;;
          // $retailer['is_done'] = 'Y';

          // Current Month
          $orders = Order::where('retailer_id', '=', $retailer->id)
            // ->whereDate('created_at', Carbon::now())
            ->where('order_type', '=', 'Sales')
            ->whereMonth('created_at', Carbon::now()->month)
            ->with('order_details')
            ->get();
          if (sizeof($orders) > 0) {
            $ordersTaken++;
            foreach ($orders as $order) {
              $rfmtd += $order->total;
              $totalOrderValue += $order->total;
            }
            $retailer['mtd'] = $rfmtd;
            $retailer['l3m']  = $rfmtd;;
            $retailer['is_done'] = 'Y';
          } else {
            $retailer['mtd'] = 0;
            $retailer['l3m']  = $rfmtd;;
            $retailer['is_done'] = 'N';
          }
          // Previous Month
          // $orders = Order::where('retailer_id', '=', $retailer->id)
          //   // ->whereDate('created_at', Carbon::now())
          //   ->where('order_type', '=', 'Sales')
          //   ->whereMonth('created_at', Carbon::now()->month - 1)
          //   ->with('order_details')
          //   ->get();
          // if (sizeof($orders) > 0) {
          //   foreach ($orders as $order) {
          //     $rfmtd += $order->total;
          //   }
          //   $retailer['l3m']  = $rfmtd;;
          // }
        }

        $user_reference_plan->reference_plan['total_outlets'] = $totalOutlets;
        $user_reference_plan->reference_plan['billed_outlets'] = $ordersTaken;
        $user_reference_plan->reference_plan['unbilled_outlets'] = $totalOutlets - $ordersTaken;
        $user_reference_plan->reference_plan['mtd'] = $totalOrderValue;
        $user_reference_plan->reference_plan['l3m'] = $rfmtd;

        $reference_plans[] = $user_reference_plan->reference_plan;
      }
    } else if (request()->search == 'all')
      $reference_plans = request()->company->reference_plans;
    else if (request()->search) {
      $reference_plans = request()->company->reference_plans()
        ->where('name', 'LIKE', '%' . $request->search . '%')
        ->get();
    } else if (request()->page && request()->rowsPerPage) {
      $reference_plans = request()->company->reference_plans();
      $count = $reference_plans->count();
      $reference_plans = $reference_plans->paginate(request()->rowsPerPage)->toArray();
      $reference_plans = $reference_plans['data'];
    } else
      $reference_plans = request()->company->reference_plans;

    // $count = sizeof($reference_plans);

    return response()->json([
      'data'     =>  $reference_plans,
      'success'   =>  true,
      'count'     =>  $count,
    ], 200);
  }

  /*
   * To store a new reference plan
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required'
    ]);

    $referencePlan = new ReferencePlan($request->all());
    $request->company->reference_plans()->save($referencePlan);

    return response()->json([
      'data'    =>  $referencePlan
    ], 201);
  }

  /*
   * To view a single reference plan
   *
   *@
   */
  public function show(ReferencePlan $referencePlan)
  {
    return response()->json([
      'data'   =>  $referencePlan
    ], 200);
  }

  /*
   * To update a reference plan
   *
   *@
   */
  public function update(Request $request, ReferencePlan $referencePlan)
  {
    $request->validate([
      'name'  =>  'required',
    ]);

    $referencePlan->update($request->all());

    return response()->json([
      'data'  =>  $referencePlan
    ], 200);
  }

  /*
   * Mapping  Beats To user,Distributor & SKU
   *
   *@
   */
  public function Beats_Mapping(Request $request)
  {
    ini_set('max_execution_time', 1000);

    $AllBeats = ReferencePlan::all();

    // Beats[ReferencePlan] Loop
    foreach ($AllBeats as $key => $beat) {

      // Create beat user
      $user_reference_plans = UserReferencePlan::where('reference_plan_id', $beat->id)->get();

      // Create Distributor Of the User
      $Distributor  = [];
      $Distributor['name'] = $beat->name;
      $Distributor['password'] = bcrypt('123456');
      $Distributor['password_backup'] = bcrypt('123456');
      $Distributor['email'] = str_replace(" ", "", $beat->name) . mt_rand(1, 9999) . '@distributor';
      $Distributor = new User($Distributor);
      $Distributor->save();

      $Distributor->assignRole(10);
      $Distributor->roles = $Distributor->roles;
      $Distributor->assignCompany($request->company->id);
      $Distributor->companies = $Distributor->companies;

      // Map Distributor to beat User
      $Distributor_ID = $Distributor->id;
      foreach ($user_reference_plans as $key => $beat_user) {
        $Update_User = User::where('id', $beat_user->user_id)
          ->update(['distributor_id' => $Distributor_ID]);
      }

      // Create Retailer[Outlet] Of Beat 
      $retailer_Data = [
        'name' => $beat->name,
        'address' => $beat->town
      ];
      $retailer = new Retailer($retailer_Data);
      $beat->retailers()->save($retailer);

      // SKUs Loop
      // // $skus = request()->company->skus;
      // $sku_data = [
      //   'name' => "Sku 1",
      //   'company_id' => $request->company->id,
      // ];
      // $sku = new Sku($sku_data);
      // $sku->save();

      $skus = Sku::all();
      $i = 5000;
      foreach ($skus as $key => $sku) {

        // Create SKUs Stock Based On the Distributor Data  
        $stock_data = [
          'sku_id' => $sku->id,
          'qty' => false,
          'price' => $sku->price,
          'invoice_no' => 'invoice' . $i,
          'total' => false,
          'distributor_id' => $Distributor_ID,
          'sku_type_id' => 1,
        ];
        $stock = new Stock($stock_data);
        $sku->stocks()->save($stock);
        $i++;
      }
    }
    return response()->json([
      'data' => $AllBeats,
      'success'  =>  true
    ], 201);
  }
}
