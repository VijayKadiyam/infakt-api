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

        $years = ['2020', '2021'];

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
        if (request()->month) {
            $customers = $customers->whereMonth('date', '=', request()->month);
        }
        if (request()->year) {
            $customers = $customers->whereYear('date', '=', request()->year);
        }
        $customers=$customers->get();
        $count = $customers->count();

        return response()->json([
            'data'     =>  $customers,
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
            'data'    =>  $customer
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
