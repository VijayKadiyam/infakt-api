<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AssetStatus;

class AssetStatusesController extends Controller
{
    public function __construct()
    {
      $this->middleware(['auth:api', 'company']);
    }

    public function masters(Request $request)
    {
      $assetController = new AssetsController();
      $assetsResponse = $assetController->index($request);

      return response()->json([
        'assets'  =>  $assetsResponse->getData()->data,
      ], 200);
    }

    public function index(Request $request)
    {
        $assetStatus = request()->company->asset_status;
        return response()->json([
        'data'     => $assetStatus,
        'success'  => true
        ], 200);
    }

    public function store(Request $request)
    {
      $request->validate([
          'status'            =>  'required',
          'description'       =>  'required',
          'date'              =>  'required',

        ]);
    
        $assetStatus = new AssetStatus($request->all());
        $request->company->asset_status()->save($assetStatus);
    
        return response()->json([
          'data'    =>  $assetStatus,
          'success' =>  true
        ], 201); 
    }

    public function show(AssetStatus $assetStatus)
    {
      return response()->json([
          'data'   =>  $assetStatus
        ], 200);
    }

    public function update(Request $request, AssetStatus $assetStatus)
    {
      $request->validate([
        'status'            =>  'required',
        'description'       =>  'required',
        'date'              =>  'required',
        ]);
    
        $assetStatus->update($request->all());
        
        return response()->json([
          'data'  =>  $assetStatus,
          'success' =>  true
        ], 200);
    }
    public function destroy(AssetStatus $assetStatus)
    {
      $assetStatus->delete(); 
    }
}
