<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SendSmsController extends Controller
{
  public function sendSMS($phone, $otp)
  {
    $endpoint = "http://mobicomm.dove-sms.com//submitsms.jsp?user=PousseM&key=fc53bf6154XX&mobile=+91$phone&message=Your OTP is $otp&senderid=POUSSE&accusage=1";
    $client = new \GuzzleHttp\Client();
    $client->request('GET', $endpoint);
  }

  public function index(Request $request)
  {
    $request->validate([
      'phone' =>  'required',
      'otp'   =>  'required'
    ]);
    $this->sendSMS($request->phone, $request->otp);
  }
}
