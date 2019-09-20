<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\User;
use App\UserAppointmentLetter;

use App\Mail\WelcomeEmail;
use App\Mail\AppointmentLetterEmail;

class EmailsController extends Controller
{
  public function welcomeEmail(Request $request)
  {
    $user_id = $request->userid;
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references', 'roles', 'companies', 'company_designation', 'company_state_branch', 'supervisors')
      ->where('id', '=', $user_id)
      ->first();

    $endpoint = "http://mobicomm.dove-sms.com//submitsms.jsp?user=PousseM&key=fc53bf6154XX&mobile=+91". $user->phone ."&message=Welcome to PMS family! We congratulate you on your selection as " . $user->company_designation['name'] .". Please CLICK THE LINK FOR sending KYC and Statutory documents for us to issue appointment letter. We have also mailed same link on your mail id. Your user name will be " . $user->email . " and password is 123456&senderid=POUSSE&accusage=1";
    $client = new \GuzzleHttp\Client();
    $client->request('GET', $endpoint);

    Mail::to($user->email)->send(new WelcomeEmail($user));
  }

  public function appointmentLetterEmail(Request $request)
  {
    $letter_id =  $request->letter_id;
    $letter = UserAppointmentLetter::where('id', '=', $letter_id)->first();

    $user_id = $request->userid;
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references', 'roles', 'companies', 'company_designation', 'company_state_branch', 'supervisors')
      ->where('id', '=', $user_id)
      ->first();

    Mail::to('kvjkumr@gmail.com')->send(new AppointmentLetterEmail($user, $letter));
  }
}
