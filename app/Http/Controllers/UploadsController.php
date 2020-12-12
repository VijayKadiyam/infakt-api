<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Retailer;
use App\Notice;

class UploadsController extends Controller
{
  public function uploadRetailerImage(Request $request)
  {
    $request->validate([
      'retailerid'        => 'required',
    ]);

    $imagePath = '';
    if ($request->hasFile('imagepath')) {
      $file = $request->file('imagepath');
      $name = $request->filename ?? 'photo.jpg';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'retailers/' .  $request->retailerid . '/' . $name;
      Storage::disk('local')->put($imagePath, file_get_contents($file), 'public');

      $retailer = Retailer::where('id', '=', request()->retailerid)->first();
      if($request->lat)
        $retailer->lat =$request->lat;
      if($request->lng)
        $retailer->lng =$request->lng;
      $retailer->image_path = $imagePath;
      $retailer->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function uploadNoticeImage(Request $request)
  {
    $request->validate([
      'noticeid'        => 'required',
    ]);

    $imagePath = '';
    if ($request->hasFile('imagepath')) {
      $file = $request->file('imagepath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'notices/' .  $request->noticeid . '/' . $name;
      Storage::disk('local')->put($imagePath, file_get_contents($file), 'public');

      $notice = Notice::where('id', '=', request()->noticeid)->first();
      $notice->imagepath = $imagePath;
      $notice->update();
    }

    return response()->json([
      'data'  => [
        'imagepath'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }
}
