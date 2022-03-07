<?php

namespace App\Http\Controllers;

use App\CustomerDataEntry;
use App\Retailer;
use Illuminate\Http\Request;

class CustomerDataEntriesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function masters(Request $request)
    {
        // $usersController = new UsersController();
        // $request->request->add(['role_id' => '5']);
        // $usersResponse = $usersController->index($request);
        $Retailers = Retailer::all();
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
            'supervisors'           =>  $supervisorsResponse->getData()->data,
            'regions'               =>  $regions,
            'brands'               =>  $brands,
            'channels'               =>  $channels,
            'chain_names'               =>  $chain_names,
            'months'  =>  $months,
            'years'   =>  $years,
            'retailers' => $Retailers,
            // 'users'   =>  $usersResponse->getData()->data,
        ], 200);
    }

    public function index()
    {
        $count = 0;
        $customer_data_entries = request()->company->customer_data_entries();

        if (request()->userid) {
            $customer_data_entries = $customer_data_entries->where('user_id', '=', request()->userid);
        }
        if (request()->month) {
            $customer_data_entries = $customer_data_entries->whereMonth('created_at', '=', request()->month);
        }
        if (request()->year) {
            $customer_data_entries = $customer_data_entries->whereYear('created_at', '=', request()->year);
        }
        $brand = request()->brand;
        if ($brand != '')
            $customer_data_entries = $customer_data_entries->whereHas('user',  function ($q) use ($brand) {
                $q->where('brand', '=', $brand);
            });
        $region = request()->region;
        if ($region != '')
            $customer_data_entries = $customer_data_entries->whereHas('user',  function ($q) use ($region) {
                $q->where('region', '=', $region);
            });
        $channel = request()->channel;
        if ($channel != '')
            $customer_data_entries = $customer_data_entries->whereHas('user',  function ($q) use ($channel) {
                $q->where('channel', '=', $channel);
            });
        $supervisorId = request()->superVisor_id;
        if ($supervisorId != '')
            $customer_data_entries = $customer_data_entries->whereHas('user',  function ($q) use ($supervisorId) {
                $q->where('supervisor_id', '=', $supervisorId);
            });

        $customer_data_entries = $customer_data_entries->get();

        $count = $customer_data_entries->count();
        return response()->json([
            'data'     =>  $customer_data_entries,
            'count'    =>   $count,
            'success'   => true,
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
        $customer_data_entry = new CustomerDataEntry($request->all());
        $request->company->customer_data_entries()->save($customer_data_entry);

        return response()->json([
            'data'    =>  $customer_data_entry
        ], 201);
    }

    /*
       * To view a single unit
       *
       *@
       */
    public function show(CustomerDataEntry $customer_data_entry)
    {
        return response()->json([
            'data'   =>  $customer_data_entry
        ], 200);
    }

    /*
       * To update a unit
       *
       *@
       */
    public function update(Request $request, CustomerDataEntry $customer_data_entry)
    {
        $request->validate([
            'user_id'  =>  'required',
        ]);

        $customer_data_entry->update($request->all());

        return response()->json([
            'data'  =>  $customer_data_entry
        ], 200);
    }
    public function destroy($id)
    {
        $customer_data_entry = CustomerDataEntry::find($id);
        $customer_data_entry->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
