<?php

namespace App\Http\Controllers;

use App\CrudeUserMapping;
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

        $crude_users = CrudeUserMapping::all();

        foreach ($crude_users as $user) {
            if ($user->store_code) {
                $us = User::where('employee_code', '=', $user->store_code)
                    ->first();
                $email = str_replace(' ', '', $user->store_name);
                if (!$us) {
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
                        'store_type' => $user->store_type,
                        'brand' => $user->brand,
                    ];
                    $us = new User($data);
                    $us->save();
                    $us->assignRole(5);
                    $us->assignCompany(request()->company->id);
                } else {
                    // Employee Code Matches then Update User
                    $user_id = $us['id'];
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
                        'store_type' => $user->store_type,
                        'brand' => $user->brand,
                    ];
                    $us = User::find($user_id);
                    $us->update($data);
                }
            }

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

            // Create Distributor Of the User
            $Distributor  = [];
            $Distributor['name'] = $beat->name;
            $Distributor['password'] = bcrypt('123456');
            $Distributor['password_backup'] = bcrypt('123456');
            $Distributor['email'] = str_replace(" ", "", $beat->name) . mt_rand(1, 9999) . '@distributor';
            $Distributor = new User($Distributor);
            $Distributor->save();

            $Distributor->assignRole(10);
            $Distributor->roles = $Distributor->roles;
            $Distributor->assignCompany(request()->company->id);
            $Distributor->companies = $Distributor->companies;

            // Map Distributor to beat User
            $Distributor_ID = $Distributor->id;
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
            $Update_User = User::where('id', $user_reference_plan->user_id)
                ->update(['distributor_id' => $Distributor_ID]);

            $skus = Sku::all();
            $i = 5000;
            foreach ($skus as $key => $sku) {

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
    }

    public function truncate()
    {
        CrudeUserMapping::truncate();
    }
}
