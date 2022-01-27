<?php

namespace App\Http\Controllers;

use App\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function masters(Request $request)
    {
        $usersController = new UsersController();
        $request->request->add(['role_id' => '5']);
        $usersResponse = $usersController->index($request);

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

        return response()->json([
            'months'  =>  $months,
            'years'   =>  $years,
            'users'   =>  $usersResponse->getData()->data,
        ], 200);
    }

    public function index()
    {
        $count = 0;

        $customers = request()->company->customers();
        if (request()->userid) {
            $customers = $customers->where('user_id', '=', request()->userid);
        }
        if (request()->month) {
            $customers = $customers->whereMonth('date', '=', request()->month);
        }
        if (request()->year) {
            $customers = $customers->whereYear('date', '=', request()->year);
        }

        $supervisorId = request()->superVisor_id;
        if ($supervisorId != '')
            $customers = $customers->whereHas('user',  function ($q) use ($supervisorId) {
                $q->where('supervisor_id', '=', $supervisorId);
            });
        if (request()->app == 'YES') {
            return response()->json([
                'data'     =>  $customers->latest()->get(),
                'success'  =>   true
            ], 200);
        }


        $customers = $customers->get();
        $total_no_of_customers_w1 = 0;
        $total_no_of_customers_w2 = 0;
        $total_no_of_customers_w3 = 0;
        $total_no_of_customers_w4 = 0;
        $total_no_of_customers_w5 = 0;
        $total_no_of_billed_customers_w1 = 0;
        $total_no_of_billed_customers_w2 = 0;
        $total_no_of_billed_customers_w3 = 0;
        $total_no_of_billed_customers_w4 = 0;
        $total_no_of_billed_customers_w5 = 0;
        $total_more_than_two_w1 = 0;
        $total_more_than_two_w2 = 0;
        $total_more_than_two_w3 = 0;
        $total_more_than_two_w4 = 0;
        $total_more_than_two_w5 = 0;
        $total_productivity_no_of_customers = 0;
        $total_productivity_no_of_billed_customers = 0;
        $total_productivity_more_than_two = 0;
        $customer_array = [];
        $user_id_log = [];
        $w1 = 0;
        foreach ($customers as $key => $customer) {
            $user = $customer->user->toArray();
            $user_id = $user['id'];
            $user_key = array_search($user_id, array_column($customer_array, 'id'));
            unset($customer['user']);
            $is_exist = in_array($user_id, $user_id_log);

            $total_no_of_customers_w1 = 0;
            $total_no_of_billed_customers_w1 = 0;
            $total_more_than_two_w1 = 0;

            $total_no_of_customers_w2 = 0;
            $total_no_of_billed_customers_w2 = 0;
            $total_more_than_two_w2 = 0;
            $percent_of_w2 = 0;

            $total_no_of_customers_w3 = 0;
            $total_no_of_billed_customers_w3 = 0;
            $total_more_than_two_w3 = 0;
            $percent_of_w3 = 0;

            $total_no_of_customers_w4 = 0;
            $total_no_of_billed_customers_w4 = 0;
            $total_more_than_two_w4 = 0;
            $percent_of_w4 = 0;

            $total_no_of_customers_w5 = 0;
            $total_no_of_billed_customers_w5 = 0;
            $total_more_than_two_w5 = 0;
            $percent_of_w5 = 0;

            if (!$user_key && !$is_exist) {
                $user['customer'] = $customer;
                $user_id_log[] = $user_id;

                // Calculation

                // For Week 1
                if ($customer->week_number == 1) {
                    $w1++;
                    $total_no_of_customers_w1 = $customer->no_of_customer;
                    $total_no_of_billed_customers_w1 = $customer->no_of_billed_customer;
                    $total_more_than_two_w1 = $customer->more_than_two;
                    if ($total_no_of_billed_customers_w1 > 0 && $total_no_of_customers_w1 > 0) {
                        $percent_of_w1 = $total_no_of_billed_customers_w1 * 100 / $total_no_of_customers_w1;
                    } else {
                        $percent_of_w1 = 0;
                    }
                }
                $user['total_no_of_customers_w1'] = $total_no_of_customers_w1;
                $user['total_no_of_billed_customers_w1'] = $total_no_of_billed_customers_w1;
                $user['total_more_than_two_w1'] = $total_more_than_two_w1;
                $user['percent_of_w1'] = $percent_of_w1;

                // For Week 2
                if ($customer->week_number == 2) {
                    // $w2++;
                    $total_no_of_customers_w2 = $customer->no_of_customer;
                    $total_no_of_billed_customers_w2 = $customer->no_of_billed_customer;
                    $total_more_than_two_w2 = $customer->more_than_two;
                    if ($total_no_of_billed_customers_w2 > 0 && $total_no_of_customers_w2 > 0) {
                        $percent_of_w2 = $total_no_of_billed_customers_w2 * 100 / $total_no_of_customers_w2;
                    } else {
                        $percent_of_w2 = 0;
                    }
                }
                $user['total_no_of_customers_w2'] = $total_no_of_customers_w2;
                $user['total_no_of_billed_customers_w2'] = $total_no_of_billed_customers_w2;
                $user['total_more_than_two_w2'] = $total_more_than_two_w2;
                $user['percent_of_w2'] = $percent_of_w2;

                // For Week 3
                if ($customer->week_number == 3) {
                    // $w3++;
                    $total_no_of_customers_w3 = $customer->no_of_customer;
                    $total_no_of_billed_customers_w3 = $customer->no_of_billed_customer;
                    $total_more_than_two_w3 = $customer->more_than_two;
                    if ($total_no_of_billed_customers_w3 > 0 && $total_no_of_customers_w3 > 0) {
                        $percent_of_w3 = $total_no_of_billed_customers_w3 * 100 / $total_no_of_customers_w3;
                    } else {
                        $percent_of_w3 = 0;
                    }
                }
                $user['total_no_of_customers_w3'] = $total_no_of_customers_w3;
                $user['total_no_of_billed_customers_w3'] = $total_no_of_billed_customers_w3;
                $user['total_more_than_two_w3'] = $total_more_than_two_w3;
                $user['percent_of_w3'] = $percent_of_w3;

                // For Week 4
                if ($customer->week_number == 4) {
                    // $w4++;
                    $total_no_of_customers_w4 = $customer->no_of_customer;
                    $total_no_of_billed_customers_w4 = $customer->no_of_billed_customer;
                    $total_more_than_two_w4 = $customer->more_than_two;
                    if ($total_no_of_billed_customers_w4 > 0 && $total_no_of_customers_w4 > 0) {
                        $percent_of_w4 = $total_no_of_billed_customers_w4 * 100 / $total_no_of_customers_w4;
                    } else {
                        $percent_of_w4 = 0;
                    }
                }
                $user['total_no_of_customers_w4'] = $total_no_of_customers_w4;
                $user['total_no_of_billed_customers_w4'] = $total_no_of_billed_customers_w4;
                $user['total_more_than_two_w4'] = $total_more_than_two_w4;
                $user['percent_of_w4'] = $percent_of_w4;

                // For Week 5
                if ($customer->week_number == 5) {
                    // $w5++;
                    $total_no_of_customers_w5 = $customer->no_of_customer;
                    $total_no_of_billed_customers_w5 = $customer->no_of_billed_customer;
                    $total_more_than_two_w5 = $customer->more_than_two;
                    if ($total_no_of_billed_customers_w5 > 0 && $total_no_of_customers_w5 > 0) {
                        $percent_of_w5 = $total_no_of_billed_customers_w5 * 100 / $total_no_of_customers_w5;
                    } else {
                        $percent_of_w5 = 0;
                    }
                }

                $user['total_no_of_customers_w5'] = $total_no_of_customers_w5;
                $user['total_no_of_billed_customers_w5'] = $total_no_of_billed_customers_w5;
                $user['total_more_than_two_w5'] = $total_more_than_two_w5;
                $user['percent_of_w5'] = $percent_of_w5;

                $total_productivity_no_of_customers =
                    $total_no_of_customers_w1
                    + $total_no_of_customers_w2
                    + $total_no_of_customers_w3
                    + $total_no_of_customers_w4
                    + $total_no_of_customers_w5;

                $total_productivity_no_of_billed_customers =
                    $total_no_of_billed_customers_w1
                    + $total_no_of_billed_customers_w2
                    + $total_no_of_billed_customers_w3
                    + $total_no_of_billed_customers_w4
                    + $total_no_of_billed_customers_w5;

                $total_productivity_more_than_two =
                    $total_more_than_two_w1
                    + $total_more_than_two_w2
                    + $total_more_than_two_w3
                    + $total_more_than_two_w4
                    + $total_more_than_two_w5;

                if ($total_productivity_no_of_billed_customers > 0 && $total_productivity_no_of_customers > 0) {
                    $total_productivity_percentage = $total_productivity_no_of_billed_customers * 100 / $total_productivity_no_of_customers;
                } else {
                    $total_productivity_percentage = 0;
                }

                $user['total_productivity_no_of_customers'] = $total_productivity_no_of_customers;
                $user['total_productivity_no_of_billed_customers'] = $total_productivity_no_of_billed_customers;
                $user['total_productivity_more_than_two'] = $total_productivity_more_than_two;
                $user['total_productivity_percentage'] = $total_productivity_percentage;

                $customer_array[] = $user;
                // return $customer_array;
            } else {
                // Calculation For  Update 

                // For Week 1
                if ($customer->week_number == 1) {
                    $total_no_of_customers_w1 = $customer_array[$user_key]['total_no_of_customers_w1'] + $customer->no_of_customer;
                    $total_no_of_billed_customers_w1 = $customer_array[$user_key]['total_no_of_billed_customers_w1'] + $customer->no_of_billed_customer;
                    $total_more_than_two_w1 = $customer_array[$user_key]['total_more_than_two_w1'] + $customer->more_than_two;
                    if ($total_no_of_billed_customers_w1 > 0 && $total_no_of_customers_w1 > 0) {
                        $percent_of_w1 = $total_no_of_billed_customers_w1 * 100 / $total_no_of_customers_w1;
                    } else {
                        $percent_of_w1 = 0;
                    }
                    $customer_array[$user_key]['total_no_of_customers_w1'] = $total_no_of_customers_w1;
                    $customer_array[$user_key]['total_no_of_billed_customers_w1'] = $total_no_of_billed_customers_w1;
                    $customer_array[$user_key]['total_more_than_two_w1'] = $total_more_than_two_w1;
                    $customer_array[$user_key]['percent_of_w1'] = $percent_of_w1;
                }
                // For Week 2
                if ($customer->week_number == 2) {
                    $total_no_of_customers_w2 = $customer_array[$user_key]['total_no_of_customers_w2'] + $customer->no_of_customer;
                    $total_no_of_billed_customers_w2 = $customer_array[$user_key]['total_no_of_billed_customers_w2'] + $customer->no_of_billed_customer;
                    $total_more_than_two_w2 = $customer_array[$user_key]['total_more_than_two_w2'] + $customer->more_than_two;
                    if ($total_no_of_billed_customers_w2 > 0 && $total_no_of_customers_w2 > 0) {
                        $percent_of_w2 = $total_no_of_billed_customers_w2 * 100 / $total_no_of_customers_w2;
                    } else {
                        $percent_of_w2 = 0;
                    }
                    $customer_array[$user_key]['total_no_of_customers_w2'] = $total_no_of_customers_w2;
                    $customer_array[$user_key]['total_no_of_billed_customers_w2'] = $total_no_of_billed_customers_w2;
                    $customer_array[$user_key]['total_more_than_two_w2'] = $total_more_than_two_w2;
                    $customer_array[$user_key]['percent_of_w2'] = $percent_of_w2;
                }
                // For Week 3
                if ($customer->week_number == 3) {
                    $total_no_of_customers_w3 = $customer_array[$user_key]['total_no_of_customers_w3'] + $customer->no_of_customer;
                    $total_no_of_billed_customers_w3 = $customer_array[$user_key]['total_no_of_billed_customers_w3'] + $customer->no_of_billed_customer;
                    $total_more_than_two_w3 = $customer_array[$user_key]['total_more_than_two_w3'] + $customer->more_than_two;
                    if ($total_no_of_billed_customers_w3 > 0 && $total_no_of_customers_w3 > 0) {
                        $percent_of_w3 = $total_no_of_billed_customers_w3 * 100 / $total_no_of_customers_w3;
                    } else {
                        $percent_of_w3 = 0;
                    }
                    $customer_array[$user_key]['total_no_of_customers_w3'] = $total_no_of_customers_w3;
                    $customer_array[$user_key]['total_no_of_billed_customers_w3'] = $total_no_of_billed_customers_w3;
                    $customer_array[$user_key]['total_more_than_two_w3'] = $total_more_than_two_w3;
                    $customer_array[$user_key]['percent_of_w3'] = $percent_of_w3;
                }
                // For Week 4
                if ($customer->week_number == 4) {
                    $total_no_of_customers_w4 = $customer_array[$user_key]['total_no_of_customers_w4'] + $customer->no_of_customer;
                    $total_no_of_billed_customers_w4 = $customer_array[$user_key]['total_no_of_billed_customers_w4'] + $customer->no_of_billed_customer;
                    $total_more_than_two_w4 = $customer_array[$user_key]['total_more_than_two_w4'] + $customer->more_than_two;
                    if ($total_no_of_billed_customers_w4 > 0 && $total_no_of_customers_w4 > 0) {
                        $percent_of_w4 = $total_no_of_billed_customers_w4 * 100 / $total_no_of_customers_w4;
                    } else {
                        $percent_of_w4 = 0;
                    }
                    $customer_array[$user_key]['total_no_of_customers_w4'] = $total_no_of_customers_w4;
                    $customer_array[$user_key]['total_no_of_billed_customers_w4'] = $total_no_of_billed_customers_w4;
                    $customer_array[$user_key]['total_more_than_two_w4'] = $total_more_than_two_w4;
                    $customer_array[$user_key]['percent_of_w4'] = $percent_of_w4;
                }
                // For Week 5
                if ($customer->week_number == 5) {
                    $total_no_of_customers_w5 = $customer_array[$user_key]['total_no_of_customers_w5'] + $customer->no_of_customer;
                    $total_no_of_billed_customers_w5 = $customer_array[$user_key]['total_no_of_billed_customers_w5'] + $customer->no_of_billed_customer;
                    $total_more_than_two_w5 = $customer_array[$user_key]['total_more_than_two_w5'] + $customer->more_than_two;
                    if ($total_no_of_billed_customers_w5 > 0 && $total_no_of_customers_w5 > 0) {
                        $percent_of_w5 = $total_no_of_billed_customers_w5 * 100 / $total_no_of_customers_w5;
                    } else {
                        $percent_of_w5 = 0;
                    }
                    $customer_array[$user_key]['total_no_of_customers_w5'] = $total_no_of_customers_w5;
                    $customer_array[$user_key]['total_no_of_billed_customers_w5'] = $total_no_of_billed_customers_w5;
                    $customer_array[$user_key]['total_more_than_two_w5'] = $total_more_than_two_w5;
                    $customer_array[$user_key]['percent_of_w5'] = $percent_of_w5;
                }

                $total_productivity_no_of_customers =
                    $customer_array[$user_key]['total_no_of_customers_w1']
                    + $customer_array[$user_key]['total_no_of_customers_w2']
                    + $customer_array[$user_key]['total_no_of_customers_w3']
                    + $customer_array[$user_key]['total_no_of_customers_w4']
                    + $customer_array[$user_key]['total_no_of_customers_w5'];

                $total_productivity_no_of_billed_customers =
                    $customer_array[$user_key]['total_no_of_billed_customers_w1']
                    + $customer_array[$user_key]['total_no_of_billed_customers_w2']
                    + $customer_array[$user_key]['total_no_of_billed_customers_w3']
                    + $customer_array[$user_key]['total_no_of_billed_customers_w4']
                    + $customer_array[$user_key]['total_no_of_billed_customers_w5'];

                $total_productivity_more_than_two =
                    $customer_array[$user_key]['total_more_than_two_w1']
                    + $customer_array[$user_key]['total_more_than_two_w2']
                    + $customer_array[$user_key]['total_more_than_two_w3']
                    + $customer_array[$user_key]['total_more_than_two_w4']
                    + $customer_array[$user_key]['total_more_than_two_w5'];

                if ($total_productivity_no_of_billed_customers > 0 && $total_productivity_no_of_customers > 0) {
                    $total_productivity_percentage = $total_productivity_no_of_billed_customers * 100 / $total_productivity_no_of_customers;
                } else {
                    $total_productivity_percentage = 0;
                }

                $customer_array[$user_key]['total_productivity_no_of_customers'] = $total_productivity_no_of_customers;
                $customer_array[$user_key]['total_productivity_no_of_billed_customers'] = $total_productivity_no_of_billed_customers;
                $customer_array[$user_key]['total_productivity_more_than_two'] = $total_productivity_more_than_two;
                $customer_array[$user_key]['total_productivity_percentage'] = $total_productivity_percentage;
            }
        }
        // return $customer_array;
        return response()->json([
            'data'     =>  $customer_array,
            'count'    =>   $count
        ], 200);
    }





    //     public function index()
    //     {
    //         // $count = 0;
    //         // if (request()->page && request()->rowsPerPage) {
    //             $weeks = [];
    //             $customers = request()->company->customers()->get();
    //             foreach ($customers as $customer) {
    //                 $date = $customer->date;

    //                 $week = Carbon::parse($date)->weekNumberInMonth;
    //                 $Week_number="Week".$week;
    //                 // return $Week_number;
    //                 // $Week_number = 
    //                 $weeks[$Week_number]=$customer;
    //                 // $customers['weeks'][$weeks];
    //             // $customers = $customers['data'];
    //         }
    // // return $weeks;

    //         // else {
    //         //     $customers = request()->company->customers;
    //         //     $count = $customers->count();
    //         // }
    //         // $count = 0;
    //         // if (request()->page && request()->rowsPerPage) {
    //         //     $customers = request()->company->customers();
    //         //     $count = $customers->count();
    //         //     $customers = $customers->paginate(request()->rowsPerPage)->toArray();
    //         //     $customers = $customers['data'];
    //         // } else {
    //         //     $customers = request()->company->customers;
    //         //     $count = $customers->count();
    //         // }

    //         return response()->json([
    //             'data'     =>  $weeks,
    //             // 'count'    =>   $count
    //         ], 200);
    //     }

    /*
       * To store a new units
       *
       *@
       */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'    =>  'required'
        ]);
        $customer = new Customer();
        $customer->company_id = $request->company_id;
        $customer->user_id = $request->user_id;
        $customer->date = $request->date;
        $customer->no_of_customer = $request->no_of_customer;
        $customer->no_of_billed_customer = $request->no_of_billed_customer;
        $customer->more_than_two = $request->more_than_two;
        $customer->week_number = Carbon::parse($request->date)->weekNumberInMonth;
        $customer->save();
        // return $customer;

        return response()->json([
            'data'    =>  $customer,
        ], 201);
    }

    /*
       * To view a single unit
       *
       *@
       */
    public function show(Customer $customer)
    {
        return response()->json([
            'data'   =>  $customer
        ], 200);
    }

    /*
       * To update a unit
       *
       *@
       */
    public function update(Request $request, Customer $customer)
    {
        // return $customer;
        $request->validate([
            'user_id'  =>  'required',
        ]);

        $customer->update($request->all());

        return response()->json([
            'data'  =>  $customer
        ], 200);
    }
}
