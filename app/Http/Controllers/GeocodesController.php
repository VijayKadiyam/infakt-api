<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeocodesController extends Controller
{
  public function index(Request $request)
  {
    try {
      $endpoint = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $request->lat . "," . $request->lng . "&key=AIzaSyDSECwAUD8Ynppe3u_MGuczSeDsH7uP2FQ";
      $client = new \GuzzleHttp\Client();

      $response = $client->request('GET', $endpoint);
      $statusCode = $response->getStatusCode();
      $content = json_decode($response->getBody(), true);
    }
    catch(Exception $ex) {

    }
    

    return response()->json([
      'data'  =>  $content['results'] ? $content['results'][0]['formatted_address'] : ''
    ]);
  }

  public function getLoc($lat, $lng)
  {
    try {
      $endpoint = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lng . "&key=AIzaSyDSECwAUD8Ynppe3u_MGuczSeDsH7uP2FQ";
      $client = new \GuzzleHttp\Client();

      $response = $client->request('GET', $endpoint);
      $statusCode = $response->getStatusCode();
      $content = json_decode($response->getBody(), true);
    }
    catch(Exception $ex) {

    }
    

    return response()->json([
      'data'  =>  $content['results'] ? $content['results'][0]['formatted_address'] : ''
    ]);
  }

  public function kanhaiLoc(Request $request) {
    return "12345";
    $lat = $request->lat;
    $lng = $request->lng;
    try {
      // $endpoint = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lng . "&key=AIzaSyDSECwAUD8Ynppe3u_MGuczSeDsH7uP2FQ";
      $endpoint = "https://test.easyhrworld.com/api/v2/attendance/getGeoLocation?lat=$lat&log=$lng";
      $client = new \GuzzleHttp\Client(
        [
          'headers' => [
            'Accept'  => 'application/json',
            'Content-Type'  => 'application/json',
            'X-API-KEY' => '356a192b7913b04c54574d18c28d46e6395428ab'
          ]
        ]
      );

      $response = $client->request('GET', $endpoint);
      $statusCode = $response->getStatusCode();
      $content = json_decode($response->getBody(), true);

      return $content;
    }
    catch(Exception $ex) {

    }
  }
  
}
