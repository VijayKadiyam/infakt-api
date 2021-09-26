<?php

namespace App\Http\Controllers;

use App\ChannelFilterDetail;
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

  public function uploadChannelFilterDetailPhotos(Request $request)
  {
    $request->validate([
      'id'        => 'required',
    ]);

    $id = request()->id;

    $brand_block_imagepath = '';
    if ($request->hasFile('brand_block_imagepath')) {
      $file = $request->file('brand_block_imagepath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $brand_block_imagepath = 'channel-Filter-detail-photos/' .  $request->id . '/' . $name;
      Storage::disk('local')->put($brand_block_imagepath, file_get_contents($file), 'public');

      $ChannelFilterDetails = ChannelFilterDetail::where('id', '=', $id)->first();
      $ChannelFilterDetails->ChannelFilterDetails = $ChannelFilterDetails;
      $ChannelFilterDetails->update();
    }

    $primary_category_imagepath='';
    if ($request->hasFile('primary_category_imagepath')) {
      $file = $request->file('primary_category_imagepath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $primary_category_imagepath = 'channel-Filter-detail-photos/' .  $request->id . '/' . $name;
      Storage::disk('local')->put($primary_category_imagepath, file_get_contents($file), 'public');

      $ChannelFilterDetails = ChannelFilterDetail::where('id', '=', $id)->first();
      $ChannelFilterDetails->primary_category_imagepath = $primary_category_imagepath;
      $ChannelFilterDetails->update();
    }
    $secondary_category_imagepath='';
    if ($request->hasFile('secondary_category_imagepath')) {
      $file = $request->file('secondary_category_imagepath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $secondary_category_imagepath = 'channel-Filter-detail-photos/' .  $request->id . '/' . $name;
      Storage::disk('local')->put($secondary_category_imagepath, file_get_contents($file), 'public');

      $ChannelFilterDetails = DailyPhoto::where('id', '=', $id)->first();
      $ChannelFilterDetails->secondary_category_imagepath = $secondary_category_imagepath;
      $ChannelFilterDetails->update();
    }
    $secondary_category_fsu_imagepath='';
    if ($request->hasFile('secondary_category_fsu_imagepath')) {
      $file = $request->file('secondary_category_fsu_imagepath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $secondary_category_fsu_imagepath = 'channel-Filter-detail-photos/' .  $request->id . '/' . $name;
      Storage::disk('local')->put($secondary_category_fsu_imagepath, file_get_contents($file), 'public');

      $ChannelFilterDetails = ChannelFilterDetail::where('id', '=', $id)->first();
      $ChannelFilterDetails->secondary_category_fsu_imagepath = $secondary_category_fsu_imagepath;
      $ChannelFilterDetails->update();
    }
    $secondary_category_parasite_imagepath='';
    if ($request->hasFile('secondary_category_parasite_imagepath')) {
      $file = $request->file('secondary_category_parasite_imagepath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $secondary_category_parasite_imagepath = 'channel-Filter-detail-photos/' .  $request->id . '/' . $name;
      Storage::disk('local')->put($secondary_category_parasite_imagepath, file_get_contents($file), 'public');

      $ChannelFilterDetails = ChannelFilterDetail::where('id', '=', $id)->first();
      $ChannelFilterDetails->secondary_category_parasite_imagepath = $secondary_category_parasite_imagepath;
      $ChannelFilterDetails->update();
    }
    $gandola_imagepath='';
    if ($request->hasFile('gandola_imagepath')) {
      $file = $request->file('gandola_imagepath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $gandola_imagepath = 'channel-Filter-detail-photos/' .  $request->id . '/' . $name;
      Storage::disk('local')->put($gandola_imagepath, file_get_contents($file), 'public');

      $ChannelFilterDetails = ChannelFilterDetail::where('id', '=', $id)->first();
      $ChannelFilterDetails->gandola_imagepath = $gandola_imagepath;
      $ChannelFilterDetails->update();
    }
    $selfie_imagepath='';
    if ($request->hasFile('selfie_imagepath')) {
      $file = $request->file('selfie_imagepath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $selfie_imagepath = 'channel-Filter-detail-photos/' .  $request->id . '/' . $name;
      Storage::disk('local')->put($selfie_imagepath, file_get_contents($file), 'public');

      $ChannelFilterDetails = ChannelFilterDetail::where('id', '=', $id)->first();
      $ChannelFilterDetails->selfie_imagepath = $selfie_imagepath;
      $ChannelFilterDetails->update();
    }
    return response()->json([
      'data'  => [
        'brand_block_imagepath'  =>  $brand_block_imagepath,
        'primary_category_imagepath' => $primary_category_imagepath,
        'secondary_category_imagepath' => $secondary_category_imagepath,
        'secondary_category_fsu_imagepath' => $secondary_category_fsu_imagepath,
        'secondary_category_parasite_imagepath' => $secondary_category_parasite_imagepath,
        'gandola_imagepath' => $gandola_imagepath,
        'selfie_imagepath' => $selfie_imagepath,
      ],
      'success' =>  true
    ]);
  
  }
}
