<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    /*
       * To get all units
         *
       *@
       */
    public function index()
    {
        $count = 0;
        if (request()->page && request()->rowsPerPage) {
            $customers = request()->company->customers();
            $count = $customers->count();
            $customers = $customers->paginate(request()->rowsPerPage)->toArray();
            $customers = $customers['data'];
        } else {
            $customers = request()->company->customers;
            $count = $customers->count();
        }

        return response()->json([
            'data'     =>  $customers,
            'count'    =>   $count
        ], 200);
    }

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

        $customer = new Customer($request->all());
        $request->company->customers()->save($customer);

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
