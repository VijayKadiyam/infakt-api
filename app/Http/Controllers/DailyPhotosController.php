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

  public function masters(Request $request)
  {

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
    ], 200);
  }

  public function index(Request $request)
  {
    ini_set('max_execution_time', -1);

    $daily_photos = [];
    if ($request->userId && $request->is_panel_request != "YES") {
      $dailyPhotos = request()->company->daily_photos()->where('user_id', '=', $request->userId);

      if ($request->from_date && $request->to_date && $request->month == null && $request->year == null && $request->userId == null) {
        $dailyPhotos = $dailyPhotos->whereBetween('date', [$request->from_date, $request->to_date]);
      }
      if ($request->from_date != '' && $request->to_date != '') {
        $dailyPhotos = $dailyPhotos->whereBetween('date', [$request->from_date, $request->to_date]);
      }
      if ($request->month != '' && $request->year != '') {
        $dailyPhotos = $dailyPhotos->whereMonth('date', '=', $request->month);
        $dailyPhotos = $dailyPhotos->whereYear('date', '=', $request->year);
      }
      // if (request()->page && request()->rowsPerPage) {
      //   $count = $dailyPhotos->count();
      //   $dailyPhotos = $dailyPhotos->paginate(request()->rowsPerPage)->toArray();
      // }

      $dailyPhotos = $dailyPhotos->get();
      if (count($dailyPhotos) != 0) {
        foreach ($dailyPhotos as $dailyPhoto) {
          $daily_photos[] = $dailyPhoto;
        }
      }
    } elseif ($request->is_panel_request == "YES") {
      // If Request Created From Panel
      $dailyPhotos = request()->company->daily_photos();

      if ($request->userId) {
        $dailyPhotos = $dailyPhotos->where('user_id', '=', $request->userId);
      }
      if ($request->from_date != '' && $request->to_date != '') {
        $dailyPhotos = $dailyPhotos->where('date', '>=', $request->from_date)
          ->where('date', '<=', $request->to_date);
      }
      if ($request->month != '' && $request->year != '') {
        $dailyPhotos = $dailyPhotos->whereMonth('date', '=', $request->month);
        $dailyPhotos = $dailyPhotos->whereYear('date', '=', $request->year);
      }
      if ($request->supervisor_id != '' && $request->supervisor_id != 'null') {
        $supervisor_id = $request->supervisor_id;
        $dailyPhotos = $dailyPhotos->whereHas('user',  function ($q) use ($supervisor_id) {
          $q->where('supervisor_id', 'LIKE', '%' . $supervisor_id . '%');
        });
      }
      if ($request->brand != '' && $request->brand != 'null') {
        $brand = $request->brand;
        $dailyPhotos = $dailyPhotos->whereHas('user',  function ($q) use ($brand) {
          $q->where('brand', 'LIKE', '%' . $brand . '%');
        });
      }
      if ($request->region != "" && $request->region != 'null') {
        $region = $request->region;
        $dailyPhotos = $dailyPhotos->whereHas('user',  function ($q) use ($region) {
          $q->where('region', 'LIKE', '%' . $region . '%');
        });
      }
      if ($request->channel != "" && $request->channel != "null") {
        $channel = $request->channel;
        $dailyPhotos = $dailyPhotos->whereHas('user',  function ($q) use ($channel) {
          $q->where('channel', '=', $channel);
        });
      }
      // if (request()->page && request()->rowsPerPage) {
      //   $count = $dailyPhotos->count();
      //   $dailyPhotos = $dailyPhotos->paginate(request()->rowsPerPage)->toArray();
      // }

      $dailyPhotos = $dailyPhotos->get();
      if (count($dailyPhotos) != 0) {
        foreach ($dailyPhotos as $dailyPhoto) {
          $daily_photos[] = $dailyPhoto;
        }
      }
    } else {
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
            // $dailyPhotos = $dailyPhotos->where('date', '=', $request->date);
          }
          if ($request->date && $request->month == null && $request->year == null) {
            // $dailyPhotos = $dailyPhotos->where('date', '=', $request->date);
          }
          if ($request->month) {
            $dailyPhotos = $dailyPhotos->whereMonth('date', '=', $request->month);
          }
          if ($request->year) {
            $dailyPhotos = $dailyPhotos->whereYear('date', '=', $request->year);
          }
          // if (request()->page && request()->rowsPerPage) {
          //   $count = $dailyPhotos->count();
          //   $dailyPhotos = $dailyPhotos->paginate(request()->rowsPerPage)->toArray();
          //   $dailyPhotos = $dailyPhotos['data'];
          // }
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
