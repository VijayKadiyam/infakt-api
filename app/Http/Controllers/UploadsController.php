<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Retailer;
use App\Notice;
use App\Profile;
use App\User;
use App\UserAttendance;

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


  public function uploadRetailerImage(Request $request)
  {
    $request->validate([
      'retailerid'        => 'required',
    ]);

    $imagePath = '';
    if ($request->hasFile('imagepath')) {
      $file = $request->file('imagepath');
      $name = $request->filename ?? 'photo.jpg';
      // $name = $name . $file->getClientOriginalExtension();
      $imagePath = 'retailers/' .  $request->retailerid . '/' . $name;
      Storage::disk('local')->put($imagePath, file_get_contents($file), 'public');

      $retailer = Retailer::where('id', '=', request()->retailerid)->first();
      if($request->lat) {
        $retailer->lat =$request->lat;
        $retailer->latitude =$request->lat;
      }
      if($request->lng) {
        $retailer->lng =$request->lng;
        $retailer->longitude =$request->lng;
      }
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

  public function uploadSelfieImage(Request $request)
  {
    $request->validate([
      'userAttendanceId'        => 'required',
    ]);

    $imagePath = '';
    if ($request->hasFile('imagepath')) {
      $file = $request->file('imagepath');
      $name = $request->filename ?? 'photo.jpg';
      // $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'user_attendances/' .  $request->userAttendanceId . '/' . $name;
      Storage::disk('local')->put($imagePath, file_get_contents($file), 'public');

      $userAttendance = UserAttendance::where('id', '=', request()->userAttendanceId)->first();
      $userAttendance->selfie_path = $imagePath;
      $userAttendance->update();
    }

    return response()->json([
      'data'  => [
        'image_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function uploadLogoutSelfieImage(Request $request)
  {
    $request->validate([
      'userAttendanceId'        => 'required',
      'imagepath'  =>  'required'
    ]);

    $logoutSelfiePath = '';
    if ($request->hasFile('imagepath')) {
      $file = $request->file('imagepath');
      $name = $request->filename ?? 'photo.jpg';
      $logoutSelfiePath = 'user_attendances/' .  $request->userAttendanceId . '/' . $name;
      Storage::disk('local')->put($logoutSelfiePath, file_get_contents($file), 'public');

      $userAttendance = UserAttendance::where('id', '=', request()->userAttendanceId)->first();
      $userAttendance->logout_selfie_path = $logoutSelfiePath;
      $userAttendance->update();
    }

    return response()->json([
      'data'  => [
        'logout_selfie_path'  =>  $logoutSelfiePath
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

  public function uploadReportListAttchment(Request $request)
  {
    $request->validate([
      'reportid'        => 'required',
    ]);

    $imagePath = '';
    if ($request->hasFile('imagepath')) {
      $file = $request->file('imagepath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $imagePath = 'Report-Attachments/' .  $request->noticeid . '/' . $name;
      Storage::disk('local')->put($imagePath, file_get_contents($file), 'public');

      $notice = Notice::where('id', '=', request()->reportid)->first();
      $notice->attachment_path = $imagePath;
      $notice->update();
    }

    return response()->json([
      'data'  => [
        'attachment_path'  =>  $imagePath
      ],
      'success' =>  true
    ]);
  }

  public function ProfilePhotoPath(Request $request)
  {
    $request->validate([
      'profileid'        => 'required',
    ]);

    $photoPath = '';
    if ($request->hasFile('photoPath')) {
      $file = $request->file('photoPath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();;
      $photoPath = 'profile/photo/' .  $request->profileid . '/' . $name;
      Storage::disk('local')->put($photoPath, file_get_contents($file), 'public');

      $profile = Profile::where('id', '=', request()->profileid)->first();
      $profile->photo_1_path = $photoPath;
      $profile->update();
    }

    return response()->json([
      'data'  => [
        'photo_1_path'  =>  $photoPath
      ],
      'success' =>  true
    ]);
  }
}
