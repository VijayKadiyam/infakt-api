<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Target;
use App\FocusedTarget;

class FocusedTargetsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function masters(Request $request)
    {

        $supervisorsController = new UsersController();
        $request->request->add(['role_id' => 4]);
        $supervisorsResponse = $supervisorsController->index($request);
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
            'regions'               =>  $regions,
            'brands'               =>  $brands,
            'channels'               =>  $channels,
            'supervisors'           =>  $supervisorsResponse->getData()->data,
            'chain_names'               =>  $chain_names,
        ], 200);
    }

    public function search(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", "-1");
        $users = $request->company->users()->with('roles')
            ->whereHas('roles',  function ($q) {
                $q->where('name', '!=', 'Admin');
                $q->where('name', '!=', 'Distributor');
            });
        $region = $request->region;
        if ($region) {
            $users = $users->where('region', 'LIKE', '%' . $region . '%');
        }
        $brand = $request->brand;
        if ($brand) {
            $users = $users->where('brand', 'LIKE', '%' . $brand . '%');
        }

        $channel = $request->channel;
        if ($channel) {
            $users = $users->where('channel', 'LIKE', '%' . $channel . '%');
        }

        $supervisorId = $request->supervisor_id;
        if ($supervisorId != '') {
            $users = $users->where('supervisor_id', '=', $supervisorId);
        }
        $users = $users->get();
        $targets = [];
        foreach ($users as $user) {
            if ($request->from_month && $request->to_month && $request->year) {
                $user['monthly_targets'] = FocusedTarget::where('user_id', '=', $user->id)
                    ->whereBetween('month', [$request->from_month, $request->to_month])
                    ->where('year', '=', $request->year)
                    ->get();
            }
            $targets[] = $user;
        }

        return response()->json([
            'data'     =>  $targets,
            'success'   =>  true
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
