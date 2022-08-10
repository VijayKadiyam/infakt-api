<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\ToiXml;
use App\User;
use Carbon\Carbon;

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

  public function toi_xml(Request $request)
  {
    $request->validate([
      'xml_path'        => 'required',
    ]);

    $xml_path = '';
    $msg = '';
    if ($request->hasFile('xml_path')) {
      $file = $request->file('xml_path');
      $name = $file->getClientOriginalName();
      $toi_xml_data = ToiXml::where(['xmlpath' => $name, 'is_process' => true])->first();

      if (!$toi_xml_data) {
        $xml_path = 'infact/toi-xmls/' . $name;
        Storage::disk('local')->put($xml_path, file_get_contents($file), 'public');
        // Storage::disk('s3')->put($xml_path, file_get_contents($file), 'public');

        $toi_xml['xmlpath'] = $xml_path;

        $toi_xml = new ToiXml($toi_xml);
        $toi_xml->save();
      }
    } else {
      $msg = 'File Already Exist';
    }

    return response()->json([
      'data'  => [
        'xml_path'  =>  $xml_path,
        'msg ' => $msg,
      ],
      'success' =>  true
    ]);
  }
}
