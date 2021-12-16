<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DailyPhoto;
use Illuminate\Support\Facades\Storage;
use App\User;

class DailyPhotosController extends Controller
{
    public function __construct()
    {
      $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
      if ($request->userId) {
        $dailyPhotos = request()->company->daily_photos()->where('user_id', '=', $request->userId);

        if ($request->date && $request->month == null && $request->year == null && $request->userId == null) {
          $dailyPhotos = $dailyPhotos->where('date', '=', $request->date);
        }
        if ($request->date && $request->month == null && $request->year == null) {
          $dailyPhotos = $dailyPhotos->where('date', '=', $request->date);
        }
        if ($request->month) {
          $dailyPhotos = $dailyPhotos->whereMonth('date', '=', $request->month);
        }
        if ($request->year) {
          $dailyPhotos = $dailyPhotos->whereYear('date', '=', $request->year);
        }
        $dailyPhotos = $dailyPhotos->get();
        if (count($dailyPhotos) != 0) {
          foreach ($dailyPhotos as $dailyPhoto) {
            $daily_photos[] = $dailyPhoto;
          }
        }
      }
      else {
        $daily_photos = [];

        $supervisors = User::with('roles')
          ->where('active', '=', 1)
          ->whereHas('roles',  function ($q) {
            $q->where('name', '=', 'SUPERVISOR');
          })->orderBy('name')
        // ->take(1)
        ->get();

        foreach ($supervisors as $supervisor) {

          $users = User::where('supervisor_id', '=', $supervisor->id)->get();

          foreach ($users as $user) {

            $dailyPhotos = request()->company->daily_photos()->where('user_id', '=', $user->id);

            if ($request->date && $request->month == null && $request->year == null && $request->userId == null) {
              $dailyPhotos = $dailyPhotos->where('date', '=', $request->date);
            }
            if ($request->date && $request->month == null && $request->year == null) {
              $dailyPhotos = $dailyPhotos->where('date', '=', $request->date);
            }
            if ($request->month) {
              $dailyPhotos = $dailyPhotos->whereMonth('date', '=', $request->month);
            }
            if ($request->year) {
              $dailyPhotos = $dailyPhotos->whereYear('date', '=', $request->year);
            }
            $dailyPhotos = $dailyPhotos->get();
            if (count($dailyPhotos) != 0) {
              foreach ($dailyPhotos as $dailyPhoto) {
                $daily_photos[] = $dailyPhoto;
              }
            }
          }
        }
      }
      

      // $daily_photos = request()->company->daily_photos; 
      $count = sizeof($daily_photos);
  
      return response()->json([
        'data'     =>  $daily_photos,
        'count'    =>   $count,
        'success' =>  true,
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
        'description'    =>  'required',
        'imagepath'       =>  'required',
      ]);
  
      $dailyPhoto = new DailyPhoto($request->all());
      $request->company->daily_photos()->save($dailyPhoto);

      if ($request->hasFile('imagepath')) {
        $file = $request->file('imagepath');
        $name = $request->filename . time() ?? 'photo.jpg' . time();
        // $name = $name . $file->getClientOriginalExtension();;
        $imagePath = 'daily_photos/' . $name;
        Storage::disk('local')->put($imagePath, file_get_contents($file), 'public');

        $dailyPhoto->image_path = $imagePath;
        $dailyPhoto->update();
      }
    
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
