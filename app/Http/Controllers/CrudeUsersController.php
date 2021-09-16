<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\UserImport;
use App\CrudeUser;
use Maatwebsite\Excel\Facades\Excel;
use App\User;

class CrudeUsersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['index']);
  }

  public function index()
  {
    return response()->json([
      'data'  =>  CrudeUser::all()
    ]);
  }

  public function uploadUser(Request $request)
  {
    set_time_limit(0);
    if ($request->hasFile('userData')) {
      $file = $request->file('userData');

      Excel::import(new UserImport, $file);
      return response()->json([
        'data'    =>  CrudeUser::all(),
        'success' =>  true
      ]);
    }
  }

  public function processUser()
  {
    set_time_limit(0);

    $crude_users = CrudeUser::all();

    foreach ($crude_users as $user) {
      if ($user->store_code) {
        $us = User::where('employee_code', '=', $user->store_code)
          ->first();
        // if (!$us) {
        //   $data = [
        //     // users column name = $user->crude_users column name
        //     'name'            =>  $user->name,
        //     'email'           =>  $user->email,
        //     'phone'           =>  $user->phone == '' ? 0 : $user->phone,
        //     'password'        =>  bcrypt('123456'),
        //     'password_backup' =>  bcrypt('123456'),
        //     'active'          =>  1,
        //     'region' => $user->region,
        //     'channel' => $user->channel,
        //     'chain_name' => $user->chain_name,
        //     'billing_code' => $user->billing_code,
        //     'employee_code' => $user->store_code,
        //     'name' => $user->store_name,
        //     'address' => $user->store_address,
        //     'ba_name' => $user->ba_name,
        //     'location' => $user->location,
        //     'city' => $user->city,
        //     'state' => $user->state,
        //     'rsm' => $user->rsm,
        //     'asm' => $user->asm,
        //     'supervisor_name' => $user->supervisor_name,
        //     'store_type' => $user->store_type,
        //     'brand' => $user->brand,
        //   ];
        //   $us = new User($data);
        //   $us->save();
        //   $us->assignRole(3);
        //   $us->assignCompany(request()->company->id);
        // } else
        if($us) {
          // Employee Code Matches then Update User
          $user_id = $us['id'];
          $data = [
            // users column name = $user->crude_users column name
            'region' => $user->region,
            'channel' => $user->channel,
            'chain_name' => $user->chain_name,
            'billing_code' => $user->billing_code,
            'employee_code' => $user->store_code,
            'name' => $user->store_name,
            'address' => $user->store_address,
            'ba_name' => $user->ba_name,
            'location' => $user->location,
            'city' => $user->city,
            'state' => $user->state,
            'rsm' => $user->rsm,
            'asm' => $user->asm,
            'supervisor_name' => $user->supervisor_name,
            'store_type' => $user->store_type,
            'brand' => $user->brand,
          ];
          $userData = User::find($user_id);
          $userData->update($data);
        }
      }
    }
  }

  public function truncate()
  {
    CrudeUser::truncate();
  }
}
