<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Storage;

class MobileUploadsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function mobilePhotoImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->photo_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileResidentialProofImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->residential_proof_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileEducationProofImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->education_proof_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobilePanCardImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->pan_card_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileAdhaarCardImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->adhaar_card_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileEsiCardImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->esi_card_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileCancelledChequeImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->cancelled_cheque_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileSalarySlipImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->salary_slip_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }
}
