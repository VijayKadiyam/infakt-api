<?php

namespace App\Http\Controllers;

use App\CompetitorData;
use Illuminate\Http\Request;

class CompetitorDatasController extends Controller
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

        $years = ['2020', '2021','2022'];

        return response()->json([
            'months'  =>  $months,
            'years'   =>  $years,
            // 'retailers' => $Retailers,
            'users'   =>  $usersResponse->getData()->data,
        ], 200);
    }

    public function index()
    {
        $count = 0;
        $competitor_datas = request()->company->competitor_datas();

        if (request()->userid) {
            $competitor_datas = $competitor_datas->where('user_id', '=', request()->userid);
        }
        if (request()->month) {
            $competitor_datas = $competitor_datas->where('month', '=', request()->month);
        }
        if (request()->year) {
            $competitor_datas = $competitor_datas->where('year', '=', request()->year);
        }
        $supervisorId = request()->superVisor_id;
        if ($supervisorId != '')
            $competitor_datas = $competitor_datas->whereHas('user',  function ($q) use ($supervisorId) {
                $q->where('supervisor_id', '=', $supervisorId);
            });
        $competitor_datas = $competitor_datas->get();

        $count = $competitor_datas->count();
        return response()->json([
            'data'     =>  $competitor_datas,
            'count'    =>   $count,
            'success'   =>  true
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
        $competitor_data = new CompetitorData($request->all());
        $request->company->competitor_datas()->save($competitor_data);

        return response()->json([
            'data'    =>  $competitor_data
        ], 201);
    }

    /*
       * To view a single unit
       *
       *@
       */
    public function show(CompetitorData $competitor_data)
    {
        return response()->json([
            'data'   =>  $competitor_data
        ], 200);
    }

    /*
       * To update a unit
       *
       *@
       */
    public function update(Request $request, CompetitorData $competitor_data)
    {
        $request->validate([
            'user_id'  =>  'required',
        ]);

        $competitor_data->update($request->all());

        return response()->json([
            'data'  =>  $competitor_data
        ], 200);
    }
    public function destroy($id)
    {
        $competitor_data = CompetitorData::find($id);
        $competitor_data->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}

