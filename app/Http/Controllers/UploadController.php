<?php

namespace App\Http\Controllers;

use App\DailyPhoto;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Storage;
use App\PlanTravellingDetail;
use App\Retailer;

class UploadController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function uploadProfileImage(Request $request)
  {
    $request->validate([
      'user_id'        => 'required',
      'profile_image'  =>  'required'
    ]);

    $image_path = optional($request->file('profile_image'))
      ->store('profileImages', 'public');

    $user = User::where('id', '=', $request->user_id)->first();
    $user->image_path = $image_path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $image_path
      ],
      'success' =>  true
    ]);
  }

  public function uploadProfile(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = "profileImages/" . request()->user()->id . '/' . $name;

    Storage::disk('local')->put($path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->image_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function uploadSignature(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = "signatureImages/" . request()->user()->id . '/' . $name;

    Storage::disk('local')->put($path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->terms_accepted = '1';
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function uploadBill(Request $request, $id)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = "billImages/" . $id . '/' . $name;

    Storage::disk('local')->put($path, $realImage, 'public');

    $planTravellingDetail = PlanTravellingDetail::where('id', '=', $id)->first();
    $planTravellingDetail->image_path = $path;
    $planTravellingDetail->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path 
      ],
      'success' =>  true
    ]);
  }

  public function uploadRetailer(Request $request, $id)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = "retailerImages/" . $id . '/' . $name;

    Storage::disk('local')->put($path, $realImage, 'public');

    $retailer = Retailer::where('id', '=', $id)->first();
    $retailer->image_path = $path;
    $retailer->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path 
      ],
      'success' =>  true
    ]);
  }

  public function uploadDailyPhotos(Request $request)
  {
    $request->validate([
      'image_path'        => 'required',
    ]);

    $imagePath = '';
    if ($request->hasFile('image_path')) {
      $file = $request->file('image_path');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'dailyphotos/' .  $request->id . '/' . $name;
      Storage::disk('local')->put($imagePath, file_get_contents($file), 'public');

      $dailyphoto = DailyPhoto::where('id', '=', request()->id)->first();
      $dailyphoto->image_path = $imagePath;
      $dailyphoto->update();
    }

    $imagePath1='';
    if ($request->hasFile('image_path1')) {
      $file = $request->file('image_path1');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath1 = 'dailyphotos/' .  $request->id . '/' . $name;
      Storage::disk('local')->put($imagePath1, file_get_contents($file), 'public');

      $dailyphoto = DailyPhoto::where('id', '=', request()->id)->first();
      $dailyphoto->image_path1 = $imagePath1;
      $dailyphoto->update();
    }
    $imagePath2='';
    if ($request->hasFile('image_path2')) {
      $file = $request->file('image_path2');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath2 = 'dailyphotos/' .  $request->id . '/' . $name;
      Storage::disk('local')->put($imagePath2, file_get_contents($file), 'public');

      $dailyphoto = DailyPhoto::where('id', '=', request()->id)->first();
      $dailyphoto->image_path2 = $imagePath2;
      $dailyphoto->update();
    }
    $imagePath3='';
    if ($request->hasFile('image_path3')) {
      $file = $request->file('image_path3');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath3 = 'dailyphotos/' .  $request->id . '/' . $name;
      Storage::disk('local')->put($imagePath3, file_get_contents($file), 'public');

      $dailyphoto = DailyPhoto::where('id', '=', request()->id)->first();
      $dailyphoto->image_path3 = $imagePath3;
      $dailyphoto->update();
    }
    $imagePath4='';
    if ($request->hasFile('image_path4')) {
      $file = $request->file('image_path4');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath4 = 'dailyphotos/' .  $request->id . '/' . $name;
      Storage::disk('local')->put($imagePath4, file_get_contents($file), 'public');

      $dailyphoto = DailyPhoto::where('id', '=', request()->id)->first();
      $dailyphoto->image_path4 = $imagePath4;
      $dailyphoto->update();
    }
    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath,
        'image_path1' => $imagePath1,
        'image_path2' => $imagePath2,
        'image_path3' => $imagePath3,
        'image_path4' => $imagePath4,
      ],
      'success' =>  true
    ]);
  
  }
}
