<?php

namespace App\Http\Controllers;

use App\ContentMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\ToiXml;
use App\User;
use App\UserAssignment;
use App\UserAssignmentSelectedAnswer;
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
      $name = $name . $file->getClientOriginalExtension();
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
      $xml_path = 'infact/toi-xmls/' . $name;
      $toi_xml_data = ToiXml::where(['xmlpath' => $xml_path])->first();

      if (!$toi_xml_data) {
        // Storage::disk('local')->put($xml_path, file_get_contents($file), 'public');
        Storage::disk('s3')->put($xml_path, file_get_contents($file), 'public');

        $toi_xml['xmlpath'] = $xml_path;

        $toi_xml = new ToiXml($toi_xml);
        $toi_xml->save();
      }
    } else {
      $xml_path = '';
      $msg = 'File Already Exist';
    }

    return response()->json([
      'data'  => [
        'xml_path'  =>  $xml_path,
        'msg' => $msg,
      ],
      'success' =>  true
    ]);
  }

  // Upload Function For Content Medias Media Path
  public function upload_content_mediapath(Request $request)
  {
    $request->validate([
      'id'        => 'required',
      'mediapath'        => 'required',
    ]);

    $mediapath = '';
    $content_media = [];
    if ($request->hasFile('mediapath')) {
      $file = $request->file('mediapath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();
      $mediapath = 'infakt/content-medias/' .  $request->id . '/' . $name;
      Storage::disk('s3')->put($mediapath, file_get_contents($file), 'public');

      $content_media = ContentMedia::where('id', '=', request()->id)->first();
      $content_media->mediapath = $mediapath;
      $content_media->update();
    }
    return response()->json([
      'data'  =>  $content_media,
      'image_path'  =>  $mediapath,
      'message' =>  "Content Medias Media Image Upload Successfully",
      'success' =>  true
    ], 200);
  }

  // Upload Function For User Assignment Document Path
  public function upload_user_assignment_documentpath(Request $request)
  {
    $request->validate([
      'id'        => 'required',
      'documentpath'        => 'required',
    ]);

    $documentpath = '';
    $user_assignment = [];
    if ($request->hasFile('documentpath')) {
      $file = $request->file('documentpath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();
      $documentpath = 'infakt/user-assignments/' .  $request->id . '/' . $name;
      Storage::disk('s3')->put($documentpath, file_get_contents($file), 'public');

      $user_assignment = UserAssignment::where('id', '=', request()->id)->first();
      $user_assignment->documentpath = $documentpath;
      $user_assignment->update();
    }
    return response()->json([
      'data'  =>  $user_assignment,
      'image_path'  =>  $documentpath,
      'message' =>  "User Assignment Document Image Upload Successfully",
      'success' =>  true
    ], 200);
  }

  // Upload Function For User Assignment Selected Answers Document Path
  public function upload_uasa_documentpath(Request $request)
  {
    $request->validate([
      'id'        => 'required',
      'documentpath'        => 'required',
    ]);

    $documentpath = '';
    $user_assignment_selected_answer = [];
    if ($request->hasFile('documentpath')) {
      $file = $request->file('documentpath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();
      $documentpath = 'infakt/user-assignment-selected-answers/' .  $request->id . '/' . $name;
      Storage::disk('s3')->put($documentpath, file_get_contents($file), 'public');

      $user_assignment_selected_answer = UserAssignmentSelectedAnswer::where('id', '=', request()->id)->first();
      $user_assignment_selected_answer->documentpath = $documentpath;
      $user_assignment_selected_answer->update();
    }
    return response()->json([
      'data'  =>  $user_assignment_selected_answer,
      'image_path'  =>  $documentpath,
      'message' =>  "User Assignment Selected Answer Document Image Upload Successfully",
      'success' =>  true
    ], 200);
  }
}
