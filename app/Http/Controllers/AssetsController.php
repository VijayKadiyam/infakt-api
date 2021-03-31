<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Asset;
use Mail;
use App\Mail\AssetMail;


class AssetsController extends Controller
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

    public function masters(Request $request)
    {
        $referencePlansController = new ReferencePlansController();
        $referencePlansResponse = $referencePlansController->index($request);

        return response()->json([
            'reference_plans'  =>  $referencePlansResponse->getData()->data,
          ], 200);
    }

    public function index()
    {
        $asset = request()->company->assets;

        return response()->json([
        'data'     => $asset,
        'success'  => true
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
          'asset_name'    =>  'required',
        ]);
      
        $asset = new Asset($request->all());
        $request->company->assets()->save($asset);

        $asset->retailer = $asset->retailer;
        $asset->reference_plan = $asset->reference_plan;
        $asset->manufacturer = $asset->manufacturer;
        $asset->asset_statuses = $asset->asset_statuses;
        Mail::to('kvjkumr@gmail.com')
          ->send(new AssetMail($asset));
      
        return response()->json([
          'data'    =>  $asset,
          'success' =>  true
        ], 201); 
    }

    public function show(Asset $asset)
    {
        return response()->json([
            'data'   =>  $asset
        ], 200);
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'asset_name'  =>  'required',
        ]);

        $asset->update($request->all());

        // if($asset->status == 'NOT WORKING') {
          $asset->retailer = $asset->retailer;
          $asset->reference_plan = $asset->reference_plan;
          $asset->manufacturer = $asset->manufacturer;
          $asset->asset_statuses = $asset->asset_statuses;
          Mail::to('kvjkumr@gmail.com')
            ->send(new AssetMail($asset));
        // }
        
        return response()->json([
        'data'  =>  $asset,
        'success' =>  true
        ], 200);
    }

    public function destroy(Asset $asset)
    {
        $asset->delete(); 
    }
}
