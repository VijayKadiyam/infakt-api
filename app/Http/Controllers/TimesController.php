<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimesController extends Controller
{
  public function index()
  {
    // $endpoint = "http://worldtimeapi.org/api/timezone/Asia/Kolkata";
    $endpoint = "http://worldclockapi.com/api/json/est/now";
    $client = new \GuzzleHttp\Client();

    $response = $client->request('GET', $endpoint);
    $statusCode = $response->getStatusCode();
    $content = json_decode($response->getBody(), true);
    $content['datetime'] = $content['currentDateTime'];

    return response()->json([
      'data'  =>  $content
    ]);
  }
}
