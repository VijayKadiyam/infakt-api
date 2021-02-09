<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DamageStock;

class DamageStocksController extends Controller
{
    public function __construct()
    {
      $this->middleware(['auth:api', 'company']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $damageStock = request()->company->damage_stocks;

        return response()->json([
        'data'     => $damageStock,
        'success'  => true
        ], 200);
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
        $request->validate([
            'qty'    =>  'required',

          ]);
      
          $damageStock = new DamageStock($request->all());
          $request->company->damage_stocks()->save($damageStock);
      
          return response()->json([
            'data'    =>  $damageStock,
            'success' =>  true
          ], 201); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DamageStock $damageStock)
    {
        return response()->json([
            'data'   =>  $damageStock
          ], 200);
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
    public function update(Request $request, DamageStock $damageStock)
    {
        $request->validate([
            'qty'  =>  'required',
          ]);
      
          $damageStock->update($request->all());
          
          return response()->json([
            'data'  =>  $damageStock,
            'success' =>  true
          ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DamageStock $damageStock)
    {
        $damageStock->delete();
    }
}
