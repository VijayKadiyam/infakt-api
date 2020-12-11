<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\MasterImport;
use App\CrudeMaster;
use Maatwebsite\Excel\Facades\Excel;
use App\User;
use App\CompanyDesignation;
use App\CompanyState;
use App\CompanyStateBranch;
use App\RetailerCategory;
use App\RetailerClassification;
use App\ReferencePlan;
use App\Retailer;
use App\UserReferencePlan;

class CrudeMastersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['index']);
  }

  public function index()
  {
    return response()->json([
      'data'  =>  CrudeMaster::all()
    ]);
  }

  public function uploadMaster(Request $request)
  {
    set_time_limit(0);
    
    if ($request->hasFile('masters')) {
      $file = $request->file('masters');

      Excel::import(new MasterImport, $file);
      
      return response()->json([
        'data'    =>  CrudeMaster::all(),
        'success' =>  true
      ]);
    }
  }

  public function processMaster()
  {
    set_time_limit(0);
    
    $crude_masters = CrudeMaster::all();

    foreach($crude_masters as $master) {
      if($master->email) {

        // Save User
        $us = User::where('email', '=', $master->email)
          ->first();
        if(!$us) {
          $data = [
            'name'            =>  $master->salesman_name,
            'email'           =>  $master->email,
            'phone'           =>  0,
            'employee_code'   =>  $master->empl_id,
            'password'        =>  bcrypt('123456'),
            'password_backup' =>  bcrypt('123456'),
            'active'          =>  1
          ];
          $us = new User($data);
          $us->save();
          $us->assignRole(5);
          $us->assignCompany(request()->company->id);
        }

        // Save SSM Designation
        $companyDesignation = CompanyDesignation::where('name', '=', 'SSM')
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$companyDesignation) {
          $data = [
            'name'  =>  'SSM'
          ];
          $companyDesignation = new CompanyDesignation($data);
          request()->company->company_designations()->save($companyDesignation);
        }

        // Save DISTRIBUTOR Designation
        $companyDesignation = CompanyDesignation::where('name', '=', 'DISTRIBUTOR')
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$companyDesignation) {
          $data = [
            'name'  =>  'DISTRIBUTOR'
          ];
          $companyDesignation = new CompanyDesignation($data);
          request()->company->company_designations()->save($companyDesignation);
        }

        // Save SALES OFFICER Designation
        $companyDesignation = CompanyDesignation::where('name', '=', 'SALES OFFICER')
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$companyDesignation) {
          $data = [
            'name'  =>  'SALES OFFICER'
          ];
          $companyDesignation = new CompanyDesignation($data);
          request()->company->company_designations()->save($companyDesignation);
        }

        // Save AREA MANAGER Designation
        $companyDesignation = CompanyDesignation::where('name', '=', 'AREA HEAD')
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$companyDesignation) {
          $data = [
            'name'  =>  'AREA HEAD'
          ];
          $companyDesignation = new CompanyDesignation($data);
          request()->company->company_designations()->save($companyDesignation);
        }

        // Save REGIONAL HEAD Designation
        $companyDesignation = CompanyDesignation::where('name', '=', 'REGIONAL HEAD')
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$companyDesignation) {
          $data = [
            'name'  =>  'REGIONAL HEAD'
          ];
          $companyDesignation = new CompanyDesignation($data);
          request()->company->company_designations()->save($companyDesignation);
        }

        // Save NATIONAL HEAD Designation
        $companyDesignation = CompanyDesignation::where('name', '=', 'NATIONAL HEAD')
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$companyDesignation) {
          $data = [
            'name'  =>  'NATIONAL HEAD'
          ];
          $companyDesignation = new CompanyDesignation($data);
          request()->company->company_designations()->save($companyDesignation);
        }

        // Save STATE
        $companyState = CompanyState::where('name', '=', $master->region)
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$companyState) {
          $data = [
            'name'  =>  $master->region
          ];
          $companyState = new CompanyState($data);
          request()->company->company_states()->save($companyState);
        }

        // Save BRANCHES
        $companyStateBranch = CompanyStateBranch::where('name', '=', $master->branch)
          ->where('company_state_id', '=', $companyState->id)
          ->first();
        if(!$companyStateBranch) {
          $data = [
            'name'  =>  $master->branch
          ];
          $companyStateBranch = new CompanyStateBranch($data);
          $companyState->company_state_branches()->save($companyStateBranch);
        }

        // Save Retailer Categories
        $retailerCategory = RetailerCategory::where('name', '=', $master->category)
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$retailerCategory) {
          $data = [
            'name'  =>  $master->category
          ];
          $retailerCategory = new RetailerCategory($data);
          request()->company->retailer_categories()->save($retailerCategory);
        }

        // Save Retailer Classification
        $retailerClassification = RetailerClassification::where('name', '=', $master->class)
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$retailerClassification) {
          $data = [
            'name'  =>  $master->class
          ];
          $retailerClassification = new RetailerClassification($data);
          request()->company->retailer_classifications()->save($retailerClassification);
        }

        // Save Distributor Name
        $distributor = User::where('name', '=', $master->distributor)
          ->whereHas('roles',  function($q) {
            $q->where('name', '=', 'DISTRIBUTOR');
          })
          ->whereHas('companies',  function($q) {
            $q->where('name', '=', request()->company->name);
          })
          ->first();
        if(!$distributor) {
          $data = [
            'name'            =>  $master->distributor,
            'email'           =>  str_replace(' ', '.', $master->distributor) . '@gmail.com',
            'phone'           =>  0,
            'employee_code'   =>  '',
            'password'        =>  bcrypt('123456'),
            'password_backup' =>  bcrypt('123456'),
            'active'          =>  1
          ];
          $distributor = new User($data);
          $distributor->save();
          $distributor->assignRole(10);
          $distributor->assignCompany(request()->company->id);
        }

        // Save Sales Officer Name
        $salesOfficer = User::where('name', '=', $master->sales_officer)
          ->whereHas('roles',  function($q) {
            $q->where('name', '=', 'SALES OFFICER');
          })
          ->whereHas('companies',  function($q) {
            $q->where('name', '=', request()->company->name);
          })
          ->first();
        if(!$salesOfficer) {
          $data = [
            'name'            =>  $master->sales_officer,
            'email'           =>  str_replace(' ', '.', $master->sales_officer) . '@gmail.com',
            'phone'           =>  0,
            'employee_code'   =>  '',
            'password'        =>  bcrypt('123456'),
            'password_backup' =>  bcrypt('123456'),
            'active'          =>  1
          ];
          $salesOfficer = new User($data);
          $salesOfficer->save();
          $salesOfficer->assignRole(6);
          $salesOfficer->assignCompany(request()->company->id);
        }

        // Save Area Manager Name
        $areaManager = User::where('name', '=', $master->area_manager)
          ->whereHas('roles',  function($q) {
            $q->where('name', '=', 'AREA HEAD');
          })
          ->whereHas('companies',  function($q) {
            $q->where('name', '=', request()->company->name);
          })
          ->first();
        if(!$areaManager) {
          $data = [
            'name'            =>  $master->area_manager,
            'email'           =>  str_replace(' ', '.', $master->area_manager) . '@gmail.com',
            'phone'           =>  0,
            'employee_code'   =>  '',
            'password'        =>  bcrypt('123456'),
            'password_backup' =>  bcrypt('123456'),
            'active'          =>  1
          ];
          $areaManager = new User($data);
          $areaManager->save();
          $areaManager->assignRole(7);
          $areaManager->assignCompany(request()->company->id);
        }

        // Save Regional Manager Name
        $regionalManager = User::where('name', '=', $master->regional)
          ->whereHas('roles',  function($q) {
            $q->where('name', '=', 'REGIONAL HEAD');
          })
          ->whereHas('companies',  function($q) {
            $q->where('name', '=', request()->company->name);
          })
          ->first();
        if(!$regionalManager) {
          $data = [
            'name'            =>  $master->regional,
            'email'           =>  str_replace(' ', '.', $master->regional) . '@gmail.com',
            'phone'           =>  0,
            'employee_code'   =>  '',
            'password'        =>  bcrypt('123456'),
            'password_backup' =>  bcrypt('123456'),
            'active'          =>  1
          ];
          $regionalManager = new User($data);
          $regionalManager->save();
          $regionalManager->assignRole(8);
          $regionalManager->assignCompany(request()->company->id);
        }

        // Save National Manager Name
        $nationalManager = User::where('name', '=', $master->national)
          ->whereHas('roles',  function($q) {
            $q->where('name', '=', 'NATIONAL HEAD');
          })
          ->whereHas('companies',  function($q) {
            $q->where('name', '=', request()->company->name);
          })
          ->first();
        if(!$nationalManager) {
          $data = [
            'name'            =>  $master->national,
            'email'           =>  str_replace(' ', '.', $master->national) . '@gmail.com',
            'phone'           =>  0,
            'employee_code'   =>  '',
            'password'        =>  bcrypt('123456'),
            'password_backup' =>  bcrypt('123456'),
            'active'          =>  1
          ];
          $nationalManager = new User($data);
          $nationalManager->save();
          $nationalManager->assignRole(9);
          $nationalManager->assignCompany(request()->company->id);
        }

        // Save Beat
        $referencePlan = ReferencePlan::where('name', '=', $master->beat_name)
          ->where('town', '=', $master->town)
          ->where('company_id', '=', request()->company->id)
          ->first();
        if(!$referencePlan) {
          $data = [
            'name'  =>  $master->beat_name,
            'town'  =>  $master->town,
          ];
          $referencePlan = new ReferencePlan($data);
          request()->company->reference_plans()->save($referencePlan);
        }

        // Save Outlet
        $retailer = Retailer::where('name', '=', $master->outlet_name)
          ->where('reference_plan_id', '=', $referencePlan->id)
          ->first();
        if(!$retailer) {
          $data = [
            'name'                      =>  $master->outlet_name,
            'address'                   =>  $master->outlet_address,
            'retailer_code'             =>  $master->uid,
            'proprietor_name'           =>  $master->contact_person,
            'phone'                     =>  $master->mobile_no,
            'retailer_category_id'      =>  $retailerCategory->id,
            'retailer_classification_id'  =>  $retailerClassification->id,
          ];
          $retailer = new Retailer($data);
          $referencePlan->retailers()->save($retailer);
        }

        // Map Beat
        $day = 7;
        if($master->day == 'MONDAY')
          $day = 1;
        if($master->day == 'TUESDAY')
          $day = 2;
        if($master->day == 'WEDNESDAY')
          $day = 3;
        if($master->day == 'THURSDAY')
          $day = 4;
        if($master->day == 'FRIDAY')
          $day = 5;
        if($master->day == 'SATURDAY')
          $day = 6;
        $userReferencePlan = UserReferencePlan::where('user_id', '=', $us->id)
          ->where('reference_plan_id', '=', $referencePlan->id)
          ->where('day', '=', $day)
          ->where('which_week', '=', $master->which_week)
          ->first();
        $data = [
          'user_id'           =>  $us->id,
          'reference_plan_id' =>  $referencePlan->id,
          'day'               =>  $day,
          'which_week'        =>  $master->which_week
        ];
        if(!$userReferencePlan) {
          $userReferencePlan = new UserReferencePlan($data);
          request()->company->user_reference_plans()->save($userReferencePlan);
        } else {
          $userReferencePlan->update($data);
        }

        // Update beat type of user
        if($master->beat_type == 'WEEKLY')
          $us->beat_type_id = 1;
        if($master->beat_type == 'FORTNIGHTLY')
          $us->beat_type_id = 2;
        if($master->beat_type == 'MONTHLY')
          $us->beat_type_id = 4;

        // Map SSM with DISTRIBUTOR, SALES OFFICER, AREA HEAD, REGIONAL HEAD, NATIONAL HEAD
        $us->so_id = $salesOfficer ? $salesOfficer->id : null;
        $us->asm_id = $areaManager ? $areaManager->id : null;
        $us->rms_id = $regionalManager ? $regionalManager->id : null;
        $us->nsm_id = $nationalManager ? $nationalManager->id : null;
        $us->distributor_id = $distributor ? $distributor->id : null;
        $us->update();

        // Map SALES OFFICER with AREA HEAD, REGIONAL HEAD, NATIONAL HEAD
        $salesOfficer->asm_id = $areaManager ? $areaManager->id : null;
        $salesOfficer->rms_id = $regionalManager ? $regionalManager->id : null;
        $salesOfficer->nsm_id = $nationalManager ? $nationalManager->id : null;
        $salesOfficer->update();

        // Map AREA HEAD with REGIONAL HEAD, NATIONAL HEAD
        $areaManager->rms_id = $regionalManager ? $regionalManager->id : null;
        $areaManager->nsm_id = $nationalManager ? $nationalManager->id : null;
        $areaManager->update();

        // Map REGIONAL HEAD with NATIONAL HEAD
        $regionalManager->nsm_id = $nationalManager ? $nationalManager->id : null;
        $regionalManager->update();

        // Map DISTRIBUTOR with AREA HEAD
        $distributor->asm_id = $areaManager ? $areaManager->id : null;
        $distributor->update();
      }
    }

    return $crude_masters;
  }

  public function truncate()
  {
    CrudeMaster::truncate();
  }
}
