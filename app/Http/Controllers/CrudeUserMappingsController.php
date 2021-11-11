<?php

namespace App\Http\Controllers;

use App\CrudeUserMapping;
use App\ImportBatch;
use App\Imports\UserMappingImport;
use App\ReferencePlan;
use App\Retailer;
use App\Sku;
use App\Stock;
use App\User;
use App\UserReferencePlan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CrudeUserMappingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company'])
            ->except(['index']);
    }

    public function index()
    {
        return response()->json([
            'data'  =>  CrudeUserMapping::all()
        ]);
    }

    public function uploadUserMapping(Request $request)
    {
        set_time_limit(0);
        if ($request->hasFile('userMappingData')) {
            $file = $request->file('userMappingData');

            Excel::import(new UserMappingImport, $file);
            return response()->json([
                'data'    =>  CrudeUserMapping::all(),
                'success' =>  true
            ]);
        }
    }

    public function processUserMapping()
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
        $Conflicts = [];
        $crude_users = CrudeUserMapping::all();
        foreach ($crude_users as $user) {
            $safe_upload_status=FALSE;
            if ($user->store_code) {
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
                            'password' => bcrypt('123456'),
                            'password_backup' => bcrypt('123456'),
                            'email' => str_replace(" ", "", $user->supervisor_name) . mt_rand(1, 9999) . '@supervisor',
                            'batch_no' => $Batch->batch_no,
                            'excel_upload_status'=>true,
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
                        // No Data Found 
                        $data = [
                            // users column name = $user->crude_users column name
                            'name'            =>  $user->store_name,
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
                            'batch_no' => $Batch->batch_no,
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
                            'email'           =>  $user->user_login_id == '' ? $email : $user->user_login_id,
                            'phone'           =>  $user->phone == '' ? 0 : $user->phone,
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
                            'supervisor_id' => $Supervisor->id,
                            'store_type' => $user->store_type,
                            'brand' => $user->brand,
                            'batch_no' => $Batch->batch_no,
                            'excel_status' => false
                        ];
                        $us = User::find($user_id);
                        $us->update($data);
                    }
                    if ($safe_upload_status == TRUE) {
                        // If User's Existing Batch No & Current batch No Doesn't Exist "AND" User's Excel Upload Status isn't Completed
                        $bt = ReferencePlan::where('name', '=', $user->store_name)->first();
                        $data = [
                            'name' => $user->store_name,
                            'town' => $user->location,
                        ];
                        if (!$bt) {
                            $beat = new ReferencePlan($data);
                            request()->company->reference_plans()->save($beat);
                        } else {
                            $beat = ReferencePlan::find($bt['id']);
                            $beat->update($data);
                        }
                        $beat_id = $beat['id'];

                        $rt = Retailer::where('name', '=', $user->store_name)->first();
                        $data = [
                            'name' => $user->store_name,
                            'retailer_code' => $user->store_code,
                            'reference_plan_id' => $beat_id,
                            'address' => $user->store_address == '' ? $user->city : $user->store_address,
                        ];
                        if (!$rt) {
                            $retailer = new Retailer($data);
                            $beat->retailers()->save($retailer);
                        } else {
                            $retailer = Retailer::find($rt['id']);
                            $retailer->update($data);
                        }

                        // Create beat user
                        $user_reference_plan = UserReferencePlan::where('reference_plan_id', $beat_id)
                            ->where('user_id', '=', $us->id)->first();

                        if (!$user_reference_plan) {
                            // insert
                            $days = 7;
                            for ($i = 1; $i <= $days; $i++) {
                                $urp_data = [
                                    'user_id' => $us->id,
                                    'reference_plan_id' => $beat_id,
                                    'day' => $i,
                                    'which_week' => 1
                                ];
                                $user_reference_plan = new UserReferencePlan($urp_data);
                                request()->company->user_reference_plans()->save($user_reference_plan);
                            }
                        } else {
                            // update
                        }
                        if ($us->distributor_id == null) {
                            // Create Distributor Of the User
                            $Distributor  = [
                                'name' => $beat->name,
                                'password' => bcrypt('123456'),
                                'password_backup' => bcrypt('123456'),
                                'email' => str_replace(" ", "", $beat->name) . mt_rand(1, 9999) . '@distributor',
                                'batch_no' => $Batch->batch_no,
                                'excel_upload_status'=>true,
                            ];
                            $Distributor = new User($Distributor);
                            $Distributor->save();

                            $Distributor->assignRole(10);
                            $Distributor->roles = $Distributor->roles;
                            $Distributor->assignCompany(request()->company->id);
                            $Distributor->companies = $Distributor->companies;
                            $Distributor_ID = $Distributor->id;
                        } else {
                            $Distributor_ID = $us->distributor_id;
                        }

                        // Map Distributor to beat User
                        $Update_User = User::where('id', $user_reference_plan->user_id)
                            ->update(['distributor_id' => $Distributor_ID]);

                        $skus = Sku::all();
                        $i = 5000;
                        foreach ($skus as $key => $sku) {
                            $SkuStock = Stock::where('distributor_id', '=', $Distributor_ID)
                                ->where('sku_id', '=', $sku->id)
                                ->first();
                            if (!$SkuStock) {
                                // Create SKUs Stock Based On the Distributor Data  
                                $stock_data = [
                                    'sku_id' => $sku->id,
                                    'qty' => false,
                                    'price' => $sku->price,
                                    'invoice_no' => 'invoice' . $i,
                                    'total' => false,
                                    'distributor_id' => $Distributor_ID,
                                    'sku_type_id' => 1,
                                ];
                                $stock = new Stock($stock_data);
                                $sku->stocks()->save($stock);
                                $i++;
                            }
                        }
                        // When Required All Entries are Done Change User's excel_upload_status to TRUE[Done]
                        $Update_User_Excel_Status = User::where('id', $us->id)
                            ->update(['excel_upload_status' => true]);
                    }
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
        CrudeUserMapping::truncate();
    }
}
