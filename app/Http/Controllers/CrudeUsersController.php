<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\UserImport;
use App\CrudeUser;
use App\ImportBatch;
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
    // Batch Section
    $type = 'Users';
    $Batch = ImportBatch::where('type', '=', $type)
      ->latest()
      ->first();
    $is_existing_batch = request()->is_existing_batch; //   true[1] or false[0]
    if (!$is_existing_batch) {
      $batch_no = 1;
      if ($Batch) {
        $batch_no = $Batch->batch_no + 1;
      }
      $Batch_data = [
        'type' => $type,
        'batch_no' => $batch_no,
      ];
      $Batch = new ImportBatch($Batch_data);
      request()->company->import_batches()->save($Batch);
    }
    $crude_users = CrudeUser::all();
    $Conflicts = [];

    foreach ($crude_users as $user) {
      $safe_upload_status = FALSE;
      if ($user->store_code && $user->store_code != "New Store") {
        $location = ($user->location) ? '@' . str_replace(' ', '', $user->location) : '';
        $email = str_replace(' ', '', $user->store_name) . '' . $location;

        $is_Existing_email = true;
        for ($i = 1; $is_Existing_email != false; $i++) {
          // Fetch User by Email for Email Unique  
          $Existing_email = User::where('email', '=', $email)->first();
          if ($Existing_email) {
            $is_Existing_email = true;
            $email = str_replace(' ', '', $user->store_name) . '' . $i . '' . $location;
          } else {
            $is_Existing_email = false;
          }
        }
        // Fetch User by store code
        $user_array = User::where('employee_code', '=', $user->store_code)
          ->get();
        if (count($user_array) > 1) {
          // Store Code Assigned to Multiple Stores 
          foreach ($user_array as $key => $conflict_user) {
            $conflict_user['source'] = 'database';
            array_push($Conflicts, $conflict_user);
          }
          $user['source'] = 'excel';
          array_push($Conflicts, $user);
        } else {

          // fetch Supervisor by Supervisor name
          $Supervisor = User::where('name', "=", $user->supervisor_name)
            ->first();
          if (!$Supervisor) {
            // Create Supervisor
            $Supervisor_data = [
              'name' => $user->supervisor_name,
              'active'          =>  1,
              'password' => bcrypt('123456'),
              'password_backup' => bcrypt('123456'),
              'email' => str_replace(" ", "", $user->supervisor_name) . mt_rand(1, 9999) . '@supervisor',
              'batch_no' => $Batch->batch_no,
              'excel_upload_status' => true,
            ];

            $Supervisor = new User($Supervisor_data);
            $Supervisor->save();

            $Supervisor->assignRole(4);
            $Supervisor->roles = $Supervisor->roles;
            $Supervisor->assignCompany(request()->company->id);
            $Supervisor->companies = $Supervisor->companies;
          }
          if (count($user_array) == 0) {
            $safe_upload_status = TRUE;

            // Insert New User 
            $data = [
              // users column name = $user->crude_users column name
              'name' => $user->store_name,
              'email'           =>  $user->user_login_id == '' ? $email : $user->user_login_id,
              'phone'           =>  $user->phone == '' ? 0 : $user->phone,
              'password'        =>  bcrypt('123456'),
              'password_backup' =>  bcrypt('123456'),
              'active'          =>  1,
              'region' => $user->region,
              'channel' => $user->channel,
              'chain_name' => $user->chain_name,
              'billing_code' => $user->billing_code,
              'employee_code' => $user->store_code,
              'address' => $user->store_address,
              'ba_name' => $user->ba_name,
              'location' => $user->location,
              'city' => $user->city,
              'state' => $user->state,
              'rsm' => $user->rsm,
              'asm' => $user->asm,
              'supervisor_name' => $user->supervisor_name,
              'supervisor_id' => $Supervisor->id,
              'store_type' => $user->store_type,
              'brand' => $user->brand,
              'ba_status' => $user->ba_status,
              'pms_emp_id' => $user->empid,
              'batch_no' => $Batch->batch_no,
              'beat_type_id' => 1,
            ];
            $us = new User($data);
            $us->save();
            $us->assignRole(5);
            $us->assignCompany(request()->company->id);
          } else {
            $safe_upload_status = FALSE;
            // Employee Code Matches then Update User
            if ($user_array[0]->batch_no != $Batch->batch_no || ($user_array[0]->batch_no == $Batch->batch_no && $user_array[0]->excel_upload_status != TRUE)) {
              // If User's Existing Batch No & Current batch No Doesn't Match "AND" User's Excel Upload Status isn't Completed
              $safe_upload_status = TRUE;
            }
            $user_id = $user_array[0]->id;
            $data = [
              // users column name = $user->crude_users column name
              'phone'           =>  $user->phone == '' ? 0 : $user->phone,
              'region' => $user->region,
              'channel' => $user->channel,
              'chain_name' => $user->chain_name,
              'billing_code' => $user->billing_code,
              // 'employee_code' => $user->store_code,
              'name' => $user->store_name,
              'address' => $user->store_address,
              'ba_name' => $user->ba_name,
              'location' => $user->location,
              'city' => $user->city,
              'state' => $user->state,
              'rsm' => $user->rsm,
              'asm' => $user->asm,
              'supervisor_name' => $user->supervisor_name,
              'supervisor_id' => $Supervisor->id,
              'store_type' => $user->store_type,
              'brand' => $user->brand,
              'ba_status' => $user->ba_status,
              'pms_emp_id' => $user->empid,
              'batch_no' => $Batch->batch_no,
              'excel_status' => false,
              'beat_type_id' => 1,
            ];
            $userData = User::find($user_id);
            $userData->update($data);
          }
          // When Required All Entries are Done Change User's excel_upload_status to TRUE[Done]
          $Update_User_Excel_Status = User::where('id', $us->id)
            ->update(['excel_upload_status' => true]);
        }
      }
    }
    return response()->json([
      'data'    =>  $Conflicts,
      'success' =>  true
    ]);
  }

  public function truncate()
  {
    CrudeUser::truncate();
  }
}
