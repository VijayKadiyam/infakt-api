<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use \Carbon\Carbon;

class UsersController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function masters(Request $request)
  {
    $rolesController = new RolesController();
    $rolesResponse = $rolesController->index($request);

    $companyDesignationsController = new CompanyDesignationsController();
    $companyDesignationsResponse = $companyDesignationsController->index($request->company);

    $companyStatesController = new CompanyStatesController();
    $companyStatesResponse = $companyStatesController->index($request->company);

    $beatTypes = [
      0 =>  [
        'text'  =>  'WEEKLY',
        'value' =>  1,
      ],
      1 =>  [
        'text'  =>  'FORTNIGHTLY',
        'value' =>  2,
      ],
      2 =>  [
        'text'  =>  'MONTHLY',
        'value' =>  4
      ]
    ];

    $salesOfficersController = new UsersController();
    $request->request->add(['role_id' => 6]);
    $salesOfficersResponse = $salesOfficersController->index($request);

    $areaManagersController = new UsersController();
    $request->request->add(['role_id' => 7]);
    $areaManagersResponse = $areaManagersController->index($request);

    $regionalManagersController = new UsersController();
    $request->request->add(['role_id' => 8]);
    $regionalManagersResponse = $regionalManagersController->index($request);

    $nationalManagersController = new UsersController();
    $request->request->add(['role_id' => 9]);
    $nationalManagersResponse = $nationalManagersController->index($request);

    $distributorsController = new UsersController();
    $request->request->add(['role_id' => 10]);
    $distributorsResponse = $distributorsController->index($request);

    return response()->json([
      'roles'                 =>  $rolesResponse->getData()->data,
      'company_designations'  =>  $companyDesignationsResponse->getData()->data,
      'company_states'        =>  $companyStatesResponse->getData()->data,
      'beat_types'            =>  $beatTypes,
      'sales_officers'        =>  $salesOfficersResponse->getData()->data,
      'area_managers'         =>  $areaManagersResponse->getData()->data,
      'regional_managers'     =>  $regionalManagersResponse->getData()->data,
      'national_managers'     =>  $nationalManagersResponse->getData()->data,
      'distributors'          =>  $distributorsResponse->getData()->data,
    ], 200);
  }

  /*
   * To get all the users
   *
   *@
   */
  public function index(Request $request)
  {
    $count = 0;
    $role = 3;
    $users = [];
    if(request()->page && request()->rowsPerPage) {
      $users = request()->company->users();
      $count = $users->count();
      $users = $users->paginate(request()->rowsPerPage)->toArray();
      $users = $users['data'];
    } else if($request->search == 'all')
      $users = $request->company->users()
        ->whereHas('roles',  function($q) {
          // $q->where('name', '!=', 'Admin');
        })
        ->latest()->get();
    else if($request->searchEmp) {
      $users = $request->company->users()->with('roles')
        ->whereHas('roles',  function($q) {
          $q->where('name', '!=', 'Admin');
        })
        ->where('name', 'LIKE', '%' . $request->searchEmp . '%')
        ->orWhere('email', 'LIKE', '%' . $request->searchEmp . '%')
        ->orWhere('phone', 'LIKE', '%' . $request->searchEmp . '%')
        ->latest()->get();
    }
    else if($request->report) {
      $now = Carbon::now();
      $role = Role::find($request->role_id);
      $users = $request->company->users()
        ->whereMonth('doj', '=', $now->format('m'))
        ->whereYear('doj', '=', $now->format('Y'))
        ->whereHas('roles', function($q) use($role) { 
          $q->where('name', '=', $role->name);
        })->latest()->get();
    }
    else if($request->month && $request->year) {
      $role = Role::find($request->role_id);
      $users = $request->company->users()
        ->whereMonth('doj', '=', $request->month)
        ->whereYear('doj', '=', $request->year)
        ->whereHas('roles', function($q) use($role) { 
          $q->where('name', '=', $role->name);
        })->latest()->get();
    }
    else if($request->endreport) {
      $now = Carbon::now();
      $role = Role::find($request->role_id);
      $users = $request->company->users()
        ->whereHas('user_appointment_letters', function($q) use($role, $now) { 
          $q->whereMonth('end_date', '=', $now->format('m'));
        })
        ->get();
    }
    else if($request->birthday) {
      $now = Carbon::now();
      $role = Role::find($request->role_id);
      $users = $request->company->users()
        ->where('dob', '=', $now->format('Y-m-d'))
        ->get();
    }
    else 
      if($request->role_id) {
        $role = Role::find($request->role_id);
        $users = $request->company->users()
          ->whereHas('roles', function($q) use($role) { 
            $q->where('name', '=', $role->name);
          })->latest()->get();
      }

    return response()->json([
      'data'  =>  $users,
      'count' =>   $count
    ], 200);
  }

  /*
   * To store a new company user
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'                    => ['required', 'string', 'max:255'],
      'email'                   => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'phone'                   => ['required', 'unique:users'],
      // 'doj'                     =>  'required',
      // 'dob'                     =>  'required',
      // 'company_designation_id'  =>  'required',
      'role_id'                 =>  'required',
    ]);

    $user  = $request->all();
    $user['password'] = bcrypt('123456');
    $user['password_backup'] = bcrypt('123456');
    // $password = mt_rand(100000, 999999);
    // $user['password'] = $password;
    // $user['password_backup'] = $password;

    $user = new User($user);
    $user->save();

    $user->assignRole($request->role_id);
    $user->roles = $user->roles;
    $user->assignCompany($request->company_id);
    $user->companies = $user->companies;

    return response()->json([
      'data'     =>  $user
    ], 201); 
  }

  /*
   * To show particular user
   *
   *@
   */
  public function show($id)
  {
    $user = User::where('id' , '=', $id)
      ->with('roles', 'companies', 'company_designation', 'company_state_branch', 'supervisors', 'notifications', 'salaries')->first();

    return response()->json([
      'data'  =>  $user,
      'success' =>  true
    ], 200); 
  }

  /*
   * To update user details
   *
   *@
   */
  public function update(Request $request, User $user)
  {
    $request->validate([
      'name'                    => ['required', 'string', 'max:255'],
      'email'                   => ['required', 'string', 'email', 'max:255'],
      'phone'                   =>  'required',
      // 'doj'                     =>  'required',
      // 'dob'                     =>  'required',
      // 'company_designation_id'  =>  'required',
    ]);

    $user->update($request->all());

    if($request->role_id)
      $user->assignRole($request->role_id);

    $user->roles = $user->roles;
    $user->companies = $user->companies;
    $user->notifications = $user->notifications;
    $user->salaries = $user->salaries;
    $user->distributor = $user->distributor;
    
    return response()->json([
      'data'  =>  $user,
      'message' =>  "User is Logged in Successfully",
      'success' =>  true
    ], 200);
  }

  /*
   * To check or update unique id
   *
   *@
   */
  public function checkOrUpdateUniqueID(Request $request, User $user)
  {
    if($user->unique_id == null | $user->unique_id == '') {
      $user->update($request->all());
    }

    return response()->json([
      'data'  =>  $user,
      'success' =>  $user->unique_id == $request->unique_id ? true : false
    ], 200);
  }

  public function countUsers(Request $request)
  {
    $count = $request->company->users()
      ->whereHas('roles', function($q) { 
        $q->where('name', '=', 'Employee');
      })->count();

    return response()->json([
      'data'  =>  $count
    ], 200);
  }
}