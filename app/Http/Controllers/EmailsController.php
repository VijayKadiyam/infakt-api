<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Mail;
use App\User;
use App\UserOfferLetter;
use App\UserAppointmentLetter;
use App\UserRenewalLetter;
use App\UserExperienceLetter;
use App\UserWarningLetter;
use App\UserPromotionLetter;
use App\UserIncreementalLetter;
use App\UserTerminationLetter;
use App\UserFullFinalLetter;

use App\Mail\WelcomeEmail;
use App\Mail\OfferLetterEmail;
use App\Mail\AppointmentLetterEmail;
use App\Mail\RenewalLetterEmail;
use App\Mail\ExperienceLetterEmail;
use App\Mail\WarningLetterEmail;
use App\Mail\PromotionLetterEmail;
use App\Mail\IncreementalLetterEmail;
use App\Mail\TerminationLetterEmail;
use App\Mail\FullFinalLetterEmail;

class EmailsController extends Controller
{
  public function sendWelcomeSMS($phone, $designation, $email, $password)
  {
    $endpoint = "http://mobicomm.dove-sms.com//submitsms.jsp?user=PousseM&key=fc53bf6154XX&mobile=+91". $phone ."&message=Welcome to PMS family! %0AWe congratulate you on your selection as " . $designation .". %0A %0APlease Click  on the link https://play.google.com/store/apps/details?id=org.pms.dastavej and install the app for sending KYC and Statutory documents for us to issue appointment letter. %0A %0AWe have also mailed same link on your email id. Your user name will be " . $email . " and password is " . $password . "&senderid=POUSSE&accusage=1";
    $client = new \GuzzleHttp\Client();
    $client->request('GET', $endpoint);
  }

  public function welcomeEmail(Request $request)
  {
    $user_id = $request->userid;
    $user = User::where('id', '=', $user_id)
      ->first();

    $this->sendWelcomeSMS($user->phone, $user->company_designation['name'], $user->email, '123456');
    // $this->sendWelcomeSMS('9920121063', $user->company_designation['name'], 'kvjkumr@gmail.com', '123456');

    Mail::to($user->email)
      ->cc('letters@pousse.in')
      ->send(new WelcomeEmail($user));
  }

  public function offerLetterEmail(Request $request)
  {
    $letter_id =  $request->letter_id;
    $letter = UserOfferLetter::where('id', '=', $letter_id)->first();

    $user_id = $request->userid;
    $user = User::where('id', '=', $user_id)
      ->first();

    Mail::to($user->email)
      ->cc('letters@pousse.in')
      ->send(new OfferLetterEmail($user, $letter));
  }

  public function appointmentLetterEmail(Request $request)
  {
    $letter_id =  $request->letter_id;
    $letter = UserAppointmentLetter::where('id', '=', $letter_id)->first();

    $user_id = $request->userid;
    $user = User::where('id', '=', $user_id)
      ->first();

    Mail::to($user->email)
      ->cc('letters@pousse.in')
      ->send(new AppointmentLetterEmail($user, $letter));
  }

  public function renewalLetterEmail(Request $request)
  {
    $letter_id =  $request->letter_id;
    $letter = UserRenewalLetter::where('id', '=', $letter_id)->first();

    $user_id = $request->userid;
    $user = User::where('id', '=', $user_id)
      ->first();

    Mail::to($user->email)
      ->cc('letters@pousse.in')
      ->send(new RenewalLetterEmail($user, $letter));
  }

  public function experienceLetterEmail(Request $request)
  {
    $letter_id =  $request->letter_id;
    $letter = UserExperienceLetter::where('id', '=', $letter_id)->first();

    $user_id = $request->userid;
    $user = User::where('id', '=', $user_id)
      ->first();

    Mail::to($user->email)
      ->cc('letters@pousse.in')
      ->send(new ExperienceLetterEmail($user, $letter));
  }

  public function warningLetterEmail(Request $request)
  {
    $letter_id =  $request->letter_id;
    $letter = UserWarningLetter::where('id', '=', $letter_id)->first();

    $user_id = $request->userid;
    $user = User::where('id', '=', $user_id)
      ->first();

    Mail::to($user->email)
      ->cc('letters@pousse.in')
      ->send(new WarningLetterEmail($user, $letter));
  }

  public function promotionLetterEmail(Request $request)
  {
    $letter_id =  $request->letter_id;
    $letter = UserPromotionLetter::where('id', '=', $letter_id)->first();

    $user_id = $request->userid;
    $user = User::where('id', '=', $user_id)
      ->first();

    Mail::to($user->email)
      ->cc('letters@pousse.in')
      ->send(new PromotionLetterEmail($user, $letter));
  }

  public function increementalLetterEmail(Request $request)
  {
    $letter_id =  $request->letter_id;
    $letter = UserIncreementalLetter::where('id', '=', $letter_id)->first();

    $user_id = $request->userid;
    $user = User::where('id', '=', $user_id)
      ->first();

    Mail::to($user->email)
      ->cc('letters@pousse.in')
      ->send(new IncreementalLetterEmail($user, $letter));
  }

  public function terminationLetterEmail(Request $request)
  {
    $letter_id =  $request->letter_id;
    $letter = UserTerminationLetter::where('id', '=', $letter_id)->first();

    $user_id = $request->userid;
    $user = User::where('id', '=', $user_id)
      ->first();

    Mail::to($user->email)
      ->cc('letters@pousse.in')
      ->send(new TerminationLetterEmail($user, $letter));
  }

  public function fullFinalLetterEmail(Request $request)
  {
    $letter_id =  $request->letter_id;
    $letter = UserFullFinalLetter::where('id', '=', $letter_id)->first();

    $user_id = $request->userid;
    $user = User::where('id', '=', $user_id)
      ->first();

    Mail::to($user->email)
      ->cc('letters@pousse.in')
      ->send(new FullFinalLetterEmail($user, $letter));
  }
}
