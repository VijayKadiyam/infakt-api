<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\GeocodesController;
use App\UserLocation;
use Illuminate\Http\Request;

class GetLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
      $userLocation = UserLocation::whereDate('created_at', '=', '2020-09-07')
            ->where('user_id', '=', 375)
            ->latest()->first();
      if($userLocation) {
        $request->request->add(['lat' => $userLocation->content['coords']['latitude']]);
        $request->request->add(['lng' => $userLocation->content['coords']['longitude']]);
        $geocodesController = new GeocodesController();
        $address = json_decode($geocodesController->index($request)->getContent())->data;
        $userLocation->address = $address;
        $userLocation->update();
      }

      $userLocation = UserLocation::whereDate('created_at', '=', '2020-09-07')
            ->where('user_id', '=', 376)
            ->latest()->first();
      if($userLocation) {
        $request->request->add(['lat' => $userLocation->content['coords']['latitude']]);
        $request->request->add(['lng' => $userLocation->content['coords']['longitude']]);
        $geocodesController = new GeocodesController();
        $address = json_decode($geocodesController->index($request)->getContent())->data;
        $userLocation->address = $address;
        $userLocation->update();
      }

      $userLocation = UserLocation::whereDate('created_at', '=', '2020-09-07')
            ->where('user_id', '=', 377)
            ->latest()->first();
      if($userLocation) {
        $request->request->add(['lat' => $userLocation->content['coords']['latitude']]);
        $request->request->add(['lng' => $userLocation->content['coords']['longitude']]);
        $geocodesController = new GeocodesController();
        $address = json_decode($geocodesController->index($request)->getContent())->data;
        $userLocation->address = $address;
        $userLocation->update();
      }

      if($userLocation) {
        $userLocation = UserLocation::whereDate('created_at', '=', '2020-09-07')
              ->where('user_id', '=', 379)
              ->latest()->first();
        $request->request->add(['lat' => $userLocation->content['coords']['latitude']]);
        $request->request->add(['lng' => $userLocation->content['coords']['longitude']]);
        $geocodesController = new GeocodesController();
        $address = json_decode($geocodesController->index($request)->getContent())->data;
        $userLocation->address = $address;
        $userLocation->update();
      }

      if($userLocation) {
        $userLocation = UserLocation::whereDate('created_at', '=', '2020-09-07')
              ->where('user_id', '=', 381)
              ->latest()->first();
        $request->request->add(['lat' => $userLocation->content['coords']['latitude']]);
        $request->request->add(['lng' => $userLocation->content['coords']['longitude']]);
        $geocodesController = new GeocodesController();
        $address = json_decode($geocodesController->index($request)->getContent())->data;
        $userLocation->address = $address;
        $userLocation->update();
      }

    }
}
