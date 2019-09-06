<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserAppointmentLetter;
use App\UserExperienceLetter;
use App\UserRenewalLetter;
use App\UserWarningLetter;
use App\UserPromotionLetter;
use App\UserIncreementalLetter;
use App\UserTerminationLetter;
use App\UserFullFinalLetter;
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

  public function mobileRenewalLetterSign(Request $request)
  {
    $request->validate([
      'letter_id'  =>  'required'
    ]);
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'renewal_letters/' . $request->letter_id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $renewalLetter = UserRenewalLetter::find($request->letter_id);
    $renewalLetter->signed  = 1;
    $renewalLetter->sign_path = $path;
    $renewalLetter->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileWarningLetterSign(Request $request)
  {
    $request->validate([
      'letter_id'  =>  'required'
    ]);
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'warning_letters/' . $request->letter_id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $warningLetter = UserWarningLetter::find($request->letter_id);
    $warningLetter->signed  = 1;
    $warningLetter->sign_path = $path;
    $warningLetter->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobilePromotionLetterSign(Request $request)
  {
    $request->validate([
      'letter_id'  =>  'required'
    ]);
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'promotion_letters/' . $request->letter_id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $promotionLetter = UserPromotionLetter::find($request->letter_id);
    $promotionLetter->signed  = 1;
    $promotionLetter->sign_path = $path;
    $promotionLetter->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileIncreementalLetterSign(Request $request)
  {
    $request->validate([
      'letter_id'  =>  'required'
    ]);
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'increemental_letters/' . $request->letter_id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $increementalLetter = UserIncreementalLetter::find($request->letter_id);
    $increementalLetter->signed  = 1;
    $increementalLetter->sign_path = $path;
    $increementalLetter->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileTerminationLetterSign(Request $request)
  {
    $request->validate([
      'letter_id'  =>  'required'
    ]);
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'termination_letters/' . $request->letter_id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $terminationLetter = UserTerminationLetter::find($request->letter_id);
    $terminationLetter->signed  = 1;
    $terminationLetter->sign_path = $path;
    $terminationLetter->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileFullFinalLetterSign(Request $request)
  {
    $request->validate([
      'letter_id'  =>  'required'
    ]);
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'full_final_letters/' . $request->letter_id . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $fullFinalLetter = UserFullFinalLetter::find($request->letter_id);
    $fullFinalLetter->signed  = 1;
    $fullFinalLetter->sign_path = $path;
    $fullFinalLetter->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobilePdsFormSign(Request $request)
  {
    $request->validate([
      'userid'  =>  'required'
    ]);
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'pds_forms/' . $request->userid . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::find($request->userid);
    $user->pds_form_sign_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileForm2Sign(Request $request)
  {
    $request->validate([
      'userid'  =>  'required'
    ]);
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'form_2/' . $request->userid . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::find($request->userid);
    $user->form_2_sign_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileForm11Sign(Request $request)
  {
    $request->validate([
      'userid'  =>  'required'
    ]);
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'form_11/' . $request->userid . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::find($request->userid);
    $user->form_11_sign_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }

  public function mobileGraduityFormSign(Request $request)
  {
    $request->validate([
      'userid'  =>  'required'
    ]);
    $image = $request->image;
    $name = $request->name;

    $realImage = base64_decode($image);
    $path = 'graduity_forms/' . $request->userid . '/' . $name;

    Storage::disk('s3')->put('documentation/' . $path, $realImage, 'public');

    $user = User::find($request->userid);
    $user->graduity_form_sign_path = $path;
    $user->update();

    return response()->json([
      'data'  => [
        'image_path'  =>  $path
      ],
      'success' =>  true
    ]);
  }
}

