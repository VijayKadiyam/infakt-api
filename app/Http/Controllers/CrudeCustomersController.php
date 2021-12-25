<?php

namespace App\Http\Controllers;

use App\CrudeCustomer;
use App\Customer;
use App\Imports\CustomerImport;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CrudeCustomersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company'])
            ->except(['index']);
    }

    public function index()
    {
        return response()->json([
            'data'  =>  CrudeCustomer::all()
        ]);
    }

    public function uploadCustomer(Request $request)
    {
        set_time_limit(0);
        if ($request->hasFile('customerData')) {
            $file = $request->file('customerData');

            Excel::import(new CustomerImport, $file);
            return response()->json([
                'data'    =>  CrudeCustomer::all(),
                'success' =>  true
            ]);
        }
    }

    public function processCustomer()
    {
        set_time_limit(0);

        $crude_customers = CrudeCustomer::all();

        foreach ($crude_customers as $column =>  $customer) {
            if ($customer->store_code) {
                $us = User::where('employee_code', '=', $customer->store_code)
                    ->first();
                $user_id = $us['id'];

                $data = [
                    'company_id' => $customer->company_id,
                    'user_id' => $user_id,
                    'date' => $customer->date,
                    'no_of_customer' => $customer->no_of_customer,
                    'no_of_billed_customer' => $customer->no_of_billed_customer,
                    'more_than_two' => $customer->more_than_two,
                    'week_number' => Carbon::parse($customer->date)->weekNumberInMonth,
                ];
// return $data;
                $customerData = new Customer($data);
                $customerData->save();
            }
        }
    }

    public function truncate()
    {
        CrudeCustomer::truncate();
    }
}
