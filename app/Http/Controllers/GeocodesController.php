<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeocodesController extends Controller
{
  public function index(Request $request)
  {
    $endpoint = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $request->lat . "," . $request->lng . "&key=AIzaSyDSECwAUD8Ynppe3u_MGuczSeDsH7uP2FQ";
    $client = new \GuzzleHttp\Client();

    $response = $client->request('GET', $endpoint);
    $statusCode = $response->getStatusCode();
    $content = json_decode($response->getBody(), true);

    return response()->json([
      'data'  =>  $content
    ]);
  }
  
}
