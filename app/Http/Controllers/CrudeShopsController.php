<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CrudeShop;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ShopImport;
use App\RetailerClassification;
use App\Retailer;
use App\ReferencePlan;
use App\User;

class CrudeShopsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['index']);
  }

  public function index()
  {
    return response()->json([
      'data'  =>  CrudeShop::all()
    ]);
  }

  public function uploadShop(Request $request)
  {
    set_time_limit(0);
    
    if ($request->hasFile('shopData')) {
      $file = $request->file('shopData');

      Excel::import(new ShopImport, $file);
      
      return response()->json([
        'data'    =>  CrudeShop::all(),
        'success' =>  true
      ]);
    }
  }

  public function processShop()
  {
    set_time_limit(0);
    
    $crude_shops = CrudeShop::all();

    foreach ($crude_shops as $crude_shop) {
      $retailerClassification = RetailerClassification::where('name', '=', $crude_shop->shop_name)->first();

      if(!$retailerClassification) {
        $data = [
          'name'  => $crude_shop->shop_type,
        ];
        $retailerClassification = new RetailerClassification($data);
        request()->company->retailer_classifications()->save($retailerClassification);
      }

      $referencePlan = ReferencePlan::where('name', '=', $crude_shop->beat)
        ->first();
      if(!$referencePlan) {
        $data = [
          'name'  =>  $crude_shop->beat,
        ];
        $referencePlan = new ReferencePlan($data);
        request()->company->reference_plans()->save($referencePlan);
      }

      $retailer = Retailer::where('name', '=', $crude_shop->shop_name)
        ->where('retailer_code', '=', $crude_shop->outlet_wisdom_code)
        ->first();

      if(!$retailer) {
        $data = [
          'name'          =>  $crude_shop->shop_name,
          'address'       =>  $crude_shop->address,
          'retailer_code'  =>  $crude_shop->outlet_wisdom_code,
        ];
        $retailer = new Retailer($data);
        $referencePlan->retailers()->save($retailer);
      }

      // $user = User::where('email', '=', $crude_shop->email) 
      //   ->first();
    }

    


    return $crude_shops;

    // foreach($crude_users as $user) {
    //   if($user->email) {
    //     $us = User::where('email', '=', $user->email)
    //       ->orWhere('phone', '=', $user->phone)
    //       ->first();
    //     if(!$us) {
    //       $data = [
    //         'name'            =>  $user->name,
    //         'email'           =>  $user->email,
    //         'phone'           =>  $user->phone == '' ? 0 : $user->phone,
    //         'password'        =>  bcrypt('123456'),
    //         'password_backup' =>  bcrypt('123456'),
    //         'active'          =>  1
    //       ];
    //       $us = new User($data);
    //       $us->save();
    //       $us->assignRole(3);
    //       $us->assignCompany(request()->company->id);
    //     }
    //   }
    // }
  }

  public function truncate()
  {
    CrudeShop::truncate();
  }
}
