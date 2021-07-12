<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DailyPhoto;

class DailyPhotosController extends Controller
{
    public function __construct()
    {
      $this->middleware(['auth:api', 'company']);
    }

    public function index()
    {
      $count = 0;
      if(request()->page && request()->rowsPerPage) {
        $daily_photos = request()->company->daily_photos();
        $count = $daily_photos->count();
        $daily_photos = $daily_photos->paginate(request()->rowsPerPage)->toArray();
        $daily_photos = $daily_photos['data'];
      } else {
        $daily_photos = request()->company->daily_photos; 
        $count = $daily_photos->count();
      }
  
      return response()->json([
        'data'     =>  $daily_photos,
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
        'description'    =>  'required'
      ]);
  
      $dailyPhoto = new DailyPhoto($request->all());
      $request->company->daily_photos()->save($dailyPhoto);
  
      return response()->json([
        'data'    =>  $dailyPhoto
      ], 201); 
    }
    
    public function show(DailyPhoto $dailyPhoto)
    {
      return response()->json([
        'data'   =>  $dailyPhoto
      ], 200);   
    }
    
    public function update(Request $request, DailyPhoto $dailyPhoto)
    {
      $request->validate([
        'description'  =>  'required',
      ]);
  
      $dailyPhoto->update($request->all());
        
      return response()->json([
        'data'  =>  $dailyPhoto
      ], 200);
    }
}
