<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserAppointmentLetter;
use App\UserExperienceLetter;
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

  public function mobileBirthCertificateImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->birth_certificate_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobilePassportImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->passport_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileDrivingLicenseImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->driving_license_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileSchoolLeavingCertificateImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->school_leaving_certificate_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileMarkSheetImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->mark_sheet_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileExperienceCertificateImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->experience_certificate_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobilePrevEmpAppLetterImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->prev_emp_app_letter_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileForm2Image(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->form_2_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileForm11Image(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->form_11_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileGraduityFormImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->graduity_form_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileAppLetterImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->app_letter_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobilePdsFormImage(Request $request)
  {
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'users/' . request()->user()->id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::where('id', '=', request()->user()->id)->first();
    $user->pds_form_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileAppointmentLetterSign(Request $request)
  {
    $request->validate([
      'letter_id'  =>  'required'
    ]);
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'appointment_letters/' . $request->letter_id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $appointmentLetter = UserAppointmentLetter::find($request->letter_id);
    $appointmentLetter->signed  = 1;
    $appointmentLetter->sign_path = $path;
    $appointmentLetter->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileExperienceLetterSign(Request $request)
  {
    $request->validate([
      'letter_id'  =>  'required'
    ]);
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'experience_letters/' . $request->letter_id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $experienceLetter = UserExperienceLetter::find($request->letter_id);
    $experienceLetter->signed  = 1;
    $experienceLetter->sign_path = $path;
    $experienceLetter->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }
}

