<?php

namespace App\Http\Controllers;

use App\CrudePjp;
use App\Imports\PjpsImport;
use App\Pjp;
use App\PjpMarket;
use App\PjpSupervisor;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CrudePjpsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company'])
            ->except(['index']);
    }

    public function index()
    {
        return response()->json([
            'data'  =>  CrudePjp::all()
        ]);
    }

    public function uploadPjp(Request $request)
    {
        set_time_limit(0);
        if ($request->hasFile('pjpsData')) {
            $file = $request->file('pjpsData');

            Excel::import(new PjpsImport, $file);
            return response()->json([
                'data'    =>  CrudePjp::all(),
                'success' =>  true
            ]);
        }
    }



    public function processPjp()
    {
        set_time_limit(0);
        $crude_pjps = CrudePjp::all();

        foreach ($crude_pjps as $pjp) {
            //  Check Existing pjp
            $pjp_data = Pjp::where('location', '=', $pjp->location)->where('region', '=', $pjp->region)
                ->first();
            $data = [
                // pjp column name = $pjp->crude_pjps column name
                'location' => $pjp->location,
                'region' => $pjp->region,
            ];
            if ($pjp_data) {
                // Udpate Existing Pjp
                $pjp_id = $pjp_data['id'];

                $pjp_data = Pjp::find($pjp_id);
                $pjp_data->update($data);
            } else {
                // Insert New Pjp 
                $pjp_data = new Pjp($data);
                request()->company->pjps()->save($pjp_data);
            }
            $pjp_id = $pjp_data['id'];

            // Split Individual Pjp Market via , 

            $market_names = explode(',', $pjp->market_working_details);
            foreach ($market_names as $market_name) {
                // Check Existing pjp market
                $pjpMarket = PjpMarket::where('pjp_id', '=', $pjp_id)->where('market_name', '=', $market_name)
                    ->first();
                $Marketdata = [
                    // pjp_markets column name = $pjp->crude_pjps column name
                    'pjp_id' => $pjp_id,
                    'market_name' => $market_name,
                    'gps_address' => $pjp->location,
                ];
                if ($pjpMarket) {
                    // Update Existing Pjp Markt
                    $pjpMarket_id = $pjpMarket['id'];
                    $pjpMarket = PjpMarket::find($pjpMarket_id);
                    $pjpMarket->update($Marketdata);
                } else {
                    $pjpMarket = new PjpMarket($Marketdata);
                    request()->company->pjp_markets()->save($pjpMarket);
                }
                // fetch User using Employee Code
                $Supervisor = User::where('employee_code', '=', $pjp->employee_code)->first();
                if ($Supervisor) {
                    // If Supervisor Exist Check Mapping
                    $pjpSupervisor = PjpSupervisor::where('user_id', '=', $Supervisor->id)->where('date', '=', $pjp->visit_date)
                        ->first();

                    $Supervisordata = [
                        // pjp_supervisors column name = $pjp->crude_pjps column name
                        'user_id' => $Supervisor->id,
                        'date' => $pjp->visit_date,
                        'actual_pjp_id' => $pjp_data->id,
                        'actual_pjp_market_id' => $pjpMarket->id,
                    ];
                    if ($pjpSupervisor) {
                        // Update Existing pjp Supervisor 
                        $pjpSupervisor_id = $pjpSupervisor['id'];

                        $pjpSupervisor = PjpSupervisor::find($pjpSupervisor_id);
                        $pjpSupervisor->update($Supervisordata);
                    } else {
                        // Insert New pjp Supervisor
                        $pjpSupervisor = new PjpSupervisor($Supervisordata);
                        request()->company->pjp_supervisors()->save($pjpSupervisor);
                    }
                } else {
                    // Create a New SuperVisor User
                    $Supervisor  = [];
                    $Supervisor['name'] = $pjp->supervisor_name;
                    $Supervisor['password'] = bcrypt('123456');
                    $Supervisor['password_backup'] = bcrypt('123456');
                    $Supervisor['email'] = str_replace(" ", "", $pjp->supervisor_name) . mt_rand(1, 9999) . '@supervisor';
                    $Supervisor = new User($Supervisor);
                    $Supervisor->save();

                    $Supervisor->assignRole(4);
                    $Supervisor->roles = $Supervisor->roles;
                    $Supervisor->assignCompany(request()->company->id);
                    $Supervisor->companies = $Supervisor->companies;

                    $Supervisordata = [
                        // pjp_supervisors column name = $pjp->crude_pjps column name
                        'user_id' => $Supervisor->id,
                        'date' => $pjp->visit_date,
                        'actual_pjp_id' => $pjp_data->id,
                        'actual_pjp_market_id' => $pjpMarket->id,
                    ];
                    $pjpSupervisor = new PjpSupervisor($Supervisordata);
                    request()->company->pjp_supervisors()->save($pjpSupervisor);
                }
            }
        }
    }

    public function truncate()
    {
        CrudePjp::truncate();
    }
}
