<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\CareerRequest;
use App\Content;
use App\ContentMedia;
use App\EtXml;
use App\Subject;
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
      $imagePath = 'infakt/users/' .  $request->userid . '/' . $name;
      Storage::disk('s3')->put($imagePath, file_get_contents($file), 'public');

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
      $xml_path = 'infakt/toi-xmls/' . $name;
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

    $mediapath = '';
    $content_media = [];
    for ($i = 0; $i < request()->mediapath_count; $i++) {
      $id = "id" . $i;
      if ($request->hasFile('mediapath' . $i)) {
        $file = $request->file('mediapath' . $i);
        $f_name = 'mediapath' . $i;
        $name = $request->filename ?? "$f_name.";
        $name = $name . $file->getClientOriginalExtension();
        $mediapath = 'infakt/content-medias/' .  $request->id . '/' . $name;
        Storage::disk('local')->put($mediapath, file_get_contents($file), 'public');
        // Storage::disk('s3')->put($mediapath, file_get_contents($file), 'public');

        $content_media = ContentMedia::where('id', '=', $request->$id)->first();
        $content_media->mediapath = $mediapath;
        $content_media->update();
      }
    }


    return response()->json([
      'data'  =>  $content_media,
      'image_path'  =>  $mediapath,
      'message' =>  "Content Medias Media Image Upload Successfully",
      'success' =>  true
    ], 200);
  }

  // Upload Function For Assignment Document path
  public function uploadAssignmentDocument(Request $request)
  {
    $request->validate([
      'assignmentid'        => 'required',
      'documentpath'        => 'required',
    ]);

    $documentpath = '';
    $assignment = [];
    if ($request->hasFile('documentpath')) {
      $file = $request->file('documentpath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();
      $documentpath = 'infakt/assignments/' .  $request->assignmentid . '/' . $name;
      Storage::disk('s3')->put($documentpath, file_get_contents($file), 'public');

      $assignment = Assignment::where('id', '=', request()->assignmentid)->first();
      $assignment->documentpath = $documentpath;
      $assignment->update();
    }
    return response()->json([
      'data'  =>  $assignment,
      'image_path'  =>  $documentpath,
      'message' =>  "Assignment Image Upload Successfully",
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

  // Upload Function For Et XMLs
  public function et_xml(Request $request)
  {
    $request->validate([
      'xml_path'        => 'required',
    ]);

    $xml_path = '';
    $msg = '';
    if ($request->hasFile('xml_path')) {
      $file = $request->file('xml_path');
      $name = $file->getClientOriginalName();
      $xml_path = 'infakt/et-xmls/' . $name;
      $et_xml_data = EtXml::where(['xmlpath' => $xml_path])->first();

      if (!$et_xml_data) {
        // Storage::disk('local')->put($xml_path, file_get_contents($file), 'public');
        Storage::disk('s3')->put($xml_path, file_get_contents($file), 'public');

        $et_xml['xmlpath'] = $xml_path;

        $et_xml = new EtXml($et_xml);
        $et_xml->save();
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

  // Upload Function For Career Attachment
  public function upload_career_attachment(Request $request)
  {
    $request->validate([
      'id'        => 'required',
      'attachment'        => 'required',
    ]);

    $attachment = '';
    $careers = [];
    if ($request->hasFile('attachment')) {
      $file = $request->file('attachment');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();
      $attachment = 'infakt/career-requests-attachment/' .  $request->id . '/' . $name;
      // Storage::disk('local')->put($attachment, file_get_contents($file), 'public');
      Storage::disk('s3')->put($attachment, file_get_contents($file), 'public');

      $careers = CareerRequest::where('id', '=', request()->id)->first();
      $careers->attachment = $attachment;
      $careers->update();
    }
    return response()->json([
      'data'  =>  $careers,
      'attachment'  =>  $attachment,
      'message' =>  "Career Attchament Upload Successfully",
      'success' =>  true
    ], 200);
  }
  // Upload Function For Assignment Document path
  public function uploadContentFeaturedImage(Request $request)
  {
    $request->validate([
      'contentid'             => 'required',
      'featuredimagepath'     => 'required',
    ]);

    $featuredimagepath = '';
    $content = [];
    if ($request->hasFile('featuredimagepath')) {
      $file = $request->file('featuredimagepath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();
      $featuredimagepath = 'infakt/contents/featured-images' .  $request->contentid . '/' . $name;
      Storage::disk('local')->put($featuredimagepath, file_get_contents($file), 'public');
      // Storage::disk('s3')->put($featuredimagepath, file_get_contents($file), 'public');

      $content = Content::where('id', '=', request()->contentid)->first();
      $content->featured_image_path = $featuredimagepath;
      $content->update();
    }
    return response()->json([
      'data'  =>  $content,
      'image_path'  =>  $featuredimagepath,
      'message' =>  "Content Featured Image Upload Successfully",
      'success' =>  true
    ], 200);
  }
  // Upload Function For Subject Image
  public function upload_subject_imagepath(Request $request)
  {
    $request->validate([
      'id'        => 'required',
      'imagepath'        => 'required',
    ]);

    $imagepath = '';
    $subject = [];
    if ($request->hasFile('imagepath')) {
      $file = $request->file('imagepath');
      $name = $request->filename ?? 'photo.';
      $name = $name . $file->getClientOriginalExtension();
      $imagepath = 'infakt/subject-imagepath/' .  $request->id . '/' . $name;
      // Storage::disk('local')->put($imagepath, file_get_contents($file), 'public');
      Storage::disk('s3')->put($imagepath, file_get_contents($file), 'public');

      $subject = Subject::where('id', '=', request()->id)->first();
      $subject->imagepath = $imagepath;
      $subject->update();
    }
    return response()->json([
      'data'  =>  $subject,
      'imagepath'  =>  $imagepath,
      'message' =>  "Subject Imagepath Upload Successfully",
      'success' =>  true
    ], 200);
  }
}
