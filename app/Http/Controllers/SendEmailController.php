<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

use App\Mail\GeoFenceMail;

class SendEmailController extends Controller
{
    public function index(Request $request)
    {
      Mail::to('kvjkumr@gmail.com')->send(new GeoFenceMail($request->activity));
      
      return $request->all();
    }
}
