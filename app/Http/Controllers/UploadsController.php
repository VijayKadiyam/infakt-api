<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Retailer;
use App\Notice;
use App\Profile;
use App\User;
use App\UserAttendance;
use Maatwebsite\Excel\Facades\Excel;

class UploadsController extends Controller
{
  public function uploadUserImage(Request $request)
  {
    $request->validate([
      'userid'        => 'required',
    ]);

    $imagePath = '';
    if ($request->hasFile('imagepath')) {
      $file = $request->file('imagepath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'users/' .  $request->userid . '/' . $name;
      Storage::disk('local')->put($imagePath, file_get_contents($file), 'public');

      $user = User::where('id', '=', request()->userid)->first();
      $user->image_path = $imagePath;
      $user->update();

      $user->roles = $user->roles;
      $user->companies = $user->companies;
      $user->notifications = $user->notifications;
      $user->salaries = $user->salaries;
      $user->distributor = $user->distributor;

      return response()->json([
        'data'  =>  $user,
        'message' =>  "User is Logged in Successfully",
        'success' =>  true
      ], 200);
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function DocumentImage(Request $request)
  {
    $request->validate([
      'id'        => 'required',
    ]);

    Excel::import($request->file('image_path')->getRealPath(), function ($reader) {
      foreach ($reader->toArray() as $key => $row) {
        $data['id'] = $row['id'];
      }
    });

    return 1;

    $image_path = '';
    if ($request->hasFile('image_path')) {
      $file = $request->file('image_path');
      $name = 'attachment.';
      $name = $name . $file->getClientOriginalExtension();;
      $image_path = 'warden/' .  $request->id . '/' . $name;
      Storage::disk('s3')->put($image_path, file_get_contents($file), 'public');

      $document = Document::where('id', '=', request()->id)->first();
      $document->image_path = $image_path;
      $document->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $image_path
      ],
      'success' =>  true
    ]);
  }
}
