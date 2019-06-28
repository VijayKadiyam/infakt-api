<?php

namespace App\Http\Controllers;

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

    Storage::disk('s3')->put($path, $realImage, 'public');

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

    Storage::disk('s3')->put($path, $realImage, 'public');

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

    Storage::disk('s3')->put($path, $realImage, 'public');

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

    Storage::disk('s3')->put($path, $realImage, 'public');

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
}
