<?php

namespace App\Http\Controllers;

use App\ImportBatch;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use \Carbon\Carbon;
use App\Sku;
use App\Stock;

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

    $type = 'Users';
    $batches = ImportBatch::where('type', '=', $type)->get();

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

    $supervisorsController = new UsersController();
    $request->request->add(['role_id' => 4]);
    $supervisorsResponse = $supervisorsController->index($request);

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
    $request->request->add(['role_id' => 10, 'status' =>  'all']);
    $distributorsResponse = $distributorsController->index($request);

    $regions = [
      'NORTH',
      'EAST',
      'WEST',
      'SOUTH',
      'CENTRAL'
    ];

    $brands = [
      'MamaEarth',
      'Derma'
    ];
    $channels = [
      'GT',
      'MT',
      'MT - CNC',
      'IIA',
    ];
    $chain_names = [
      'GT',
      'Big Bazar',
      'Dmart',
      'Guardian',
      'H&G',
      'Lee Merche',
      'LuLu',
      'Metro CNC',
      'More Retail',
      'MT',
      'Reliance',
      'Spencer',
      'Walmart',
      'Lifestyle',
      'INCS',
      'Ximivogue',
      'Shopper Stop'
    ];

    return response()->json([
      'roles'                 =>  $rolesResponse->getData()->data,
      'company_designations'  =>  $companyDesignationsResponse->getData()->data,
      'company_states'        =>  $companyStatesResponse->getData()->data,
      'beat_types'            =>  $beatTypes,
      'supervisors'           =>  $supervisorsResponse->getData()->data,
      'sales_officers'        =>  $salesOfficersResponse->getData()->data,
      'area_managers'         =>  $areaManagersResponse->getData()->data,
      'regional_managers'     =>  $regionalManagersResponse->getData()->data,
      'national_managers'     =>  $nationalManagersResponse->getData()->data,
      'distributors'          =>  $distributorsResponse->getData()->data,
      'regions'               =>  $regions,
      'batches'               =>  $batches,
      'brands'               =>  $brands,
      'channels'               =>  $channels,
      'chain_names'               =>  $chain_names,
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
    if (request()->page && request()->rowsPerPage) {
      $users = request()->company->users();
      $count = $users->count();
      $users = $users->paginate(request()->rowsPerPage)->toArray();
      $users = $users['data'];
    } else if ($request->search == 'all') {
      $users = $request->company->users()
        ->whereHas('roles',  function ($q) {
          // $q->where('name', '!=', 'Admin');
        })
        ->latest()->get();
    } else if ($request->search) {
      $users = $request->company->users()->with('roles')
        ->whereHas('roles',  function ($q) {
          $q->where('name', '!=', 'Admin');
        })
        ->where('name', 'LIKE', $request->search . '%')
        ->orWhere('employee_code', 'LIKE', '%' . $request->search . '%');
      if ($request->superVisor_id) {
        $supervisorId = $request->superVisor_id;
        $users =  $users->where('supervisor_id', '=', $supervisorId);
      }
      $users =  $users->latest()->get();
    } else if ($request->searchEmp) {
      $users = $request->company->users()->with('roles')
        ->whereHas('roles',  function ($q) {
          $q->where('name', '!=', 'Admin');
        })
        ->where('name', 'LIKE', '%' . $request->searchEmp . '%')
        ->orWhere('email', 'LIKE', '%' . $request->searchEmp . '%')
        ->orWhere('phone', 'LIKE', '%' . $request->searchEmp . '%')
        ->orWhere('employee_code', 'LIKE', '%' . $request->searchEmp . '%')
        ->orWhere('ba_name', 'LIKE', '%' . $request->searchEmp . '%')
        ->orWhere('employee_code', 'LIKE', '%' . $request->searchEmp . '%')
        ->latest()->get();
    } else if ($request->report) {
      $now = Carbon::now();
      $role = Role::find($request->role_id);
      $users = $request->company->users()
        ->whereMonth('doj', '=', $now->format('m'))
        ->whereYear('doj', '=', $now->format('Y'))
        ->whereHas('roles', function ($q) use ($role) {
          $q->where('name', '=', $role->name);
        })->latest()->get();
    } else if ($request->month && $request->year) {
      $role = Role::find($request->role_id);
      $users = $request->company->users()
        ->whereMonth('doj', '=', $request->month)
        ->whereYear('doj', '=', $request->year)
        ->whereHas('roles', function ($q) use ($role) {
          $q->where('name', '=', $role->name);
        })->latest()->get();
    } else if ($request->endreport) {
      $now = Carbon::now();
      $role = Role::find($request->role_id);
      $users = $request->company->users()
        ->whereHas('user_appointment_letters', function ($q) use ($role, $now) {
          $q->whereMonth('end_date', '=', $now->format('m'));
        })
        ->get();
    } else if ($request->birthday) {
      $now = Carbon::now();
      $role = Role::find($request->role_id);
      $users = $request->company->users()
        ->where('dob', '=', $now->format('Y-m-d'))
        ->get();
    } else if ($request->role_id) {
      $role = Role::find($request->role_id);
      $users = $request->company->allUsers()
        ->whereHas('roles', function ($q) use ($role) {
          $q->where('name', '=', $role->name);
        });
      if ($request->status != 'all')
        $users = $users->where('active', '=', 1);
      if ($request->superVisor_id)
        $users = $users->where('supervisor_id', '=', $request->superVisor_id);
      $users = $users->latest()->get();
    } elseif ($request->batch_no) {
      $users = $request->company->users()
        ->where('batch_no', '=', $request->batch_no)
        ->get();
    } elseif ($request->supervisorId) {
      $supervisorId = $request->supervisorId;
      $users = User::where('supervisor_id', '=', $supervisorId)
        ->get();
    }
    // $count = $users->count();
    return response()->json([
      'data'  =>  $users,
      'count' =>   $count,
      'success' =>  true,
    ], 200);
  }

  public function search(Request $request)
  {
    $count = 0;
    $users = [];
    // return $request->superVisor_id;
    $users = request()->company->users();
    if (request()->page && request()->rowsPerPage) {
      $users = request()->company->users();
      $count = $users->count();
      $users = $users->paginate(request()->rowsPerPage)->toArray();
      $users = $users['data'];
    }
    if ($request->batch_no) {
      $users = $users
        ->where('batch_no', '=', $request->batch_no);
    }
    if ($request->region) {
      $users = $users
        ->where('region', 'LIKE', '%' . $request->region . '%');
    }
    if ($request->channel) {
      $users = $users
        ->where('channel', 'LIKE', '%' . $request->channel . '%');
    }
    if ($request->chain_name) {
      $users = $users
        ->where('chain_name', 'LIKE', '%' . $request->chain_name . '%');
    }
    if ($request->brand) {
      $users = $users
        ->where('brand', 'LIKE', '%' . $request->brand . '%');
    }
    if ($request->superVisor_id || $request->supervisor_id) {
      $supervisorId = $request->superVisor_id ? $request->superVisor_id : $request->supervisor_id;
      $users =  $users->where('supervisor_id', '=', $supervisorId);
    }
    $users = $users->paginate(request()->rowsPerPage)->toArray();
    $users = $users['data'];
    return response()->json([
      'data'     =>  $users,
      'count' =>   $count,
      'success'   =>  true
    ], 200);
  }

  public function searchByRole(Request $request)
  {
    ini_set('max_execution_time', -1);
    ini_set('memory_limit', '1000M');
    set_time_limit(0);
    $count = 0;
    $users = [];

    if ($request->role_id && request()->page && request()->rowsPerPage) {
      $role = Role::find($request->role_id);
      $users = $request->company->allUsers()
        ->whereHas('roles', function ($q) use ($role) {
          $q->where('name', '=', $role->name);
        });
      if ($request->status != 'all')
        $users = $users->where('active', '=', 1);
      if ($request->superVisor_id) {

        $users = $users->where('supervisor_id', '=', $request->superVisor_id);
      }
      $count = $users->count();
      $users = $users->paginate(request()->rowsPerPage)->toArray();
      $users = $users['data'];
    }
    return response()->json([
      'data'     =>  $users,
      'count' =>   $count,
      'success'   =>  true
    ], 200);
  }

  public function excelDownload(Request $request)
  {
    ini_set('max_execution_time', -1);
    ini_set('memory_limit', '1000M');
    set_time_limit(0);
    $count = 0;
    $users = [];

    if ($request->role_id) {
      // return 'yees';
      $role = Role::find($request->role_id);
      $users = $request->company->allUsers()
        ->whereHas('roles', function ($q) use ($role) {
          $q->where('name', '=', $role->name);
        });
      if ($request->status != 'all') {
        $users = $users->where('active', '=', 1);
      }
      if ($request->superVisor_id) {
        $users = $users->where('supervisor_id', '=', $request->superVisor_id);
      }
      $users = $users->get();
      // return $users;
    } else {
      $users = $request->company->users();
      if ($request->batch_no) {
        $users = $users
          ->where('batch_no', '=', $request->batch_no);
      }
      if ($request->region) {
        $users = $users
          ->where('region', 'LIKE', '%' . $request->region . '%');
      }
      if ($request->channel) {
        $users = $users
          ->where('channel', 'LIKE', '%' . $request->channel . '%');
      }
      if ($request->chain_name) {
        $users = $users
          ->where('chain_name', 'LIKE', '%' . $request->chain_name . '%');
      }
      if ($request->brand) {
        $users = $users
          ->where('brand', 'LIKE', '%' . $request->brand . '%');
      }
      if ($request->superVisor_id) {
        $supervisorId = $request->superVisor_id;
        // return $supervisorId;
        $users =  $users->where('supervisor_id', '=', $supervisorId);
      }
      $users = $users->get();
      $count = $users->count();
    }
    return response()->json([
      'data'     =>  $users,
      'count' =>   $count,
      'success'   =>  true
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
      // 'email'                   => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'email'                   => ['required', 'string', 'max:255', 'unique:users'],
      'phone'                   => ['required', 'unique:users'],
      // 'doj'                     =>  'required',
      // 'dob'                     =>  'required',
      // 'company_designation_id'  =>  'required',
      'role_id'                 =>  'required',
    ]);

    // $user  = $request->all();
    $user['name'] = $request->name;
    $user['email'] = $request->email;
    $user['phone'] = $request->phone;
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

    if ($request->role_id == 10) {
      $skus = Sku::all();
      $i = 5000;
      foreach ($skus as $key => $sku) {
        // Create SKUs Stock Based On the Distributor Data  
        $stock_data = [
          'sku_id' => $sku->id,
          'qty' => false,
          'price' => $sku->price,
          'invoice_no' => 'invoice' . $user->name . $i,
          'total' => false,
          'distributor_id' => $user->id,
          'sku_type_id' => 1,
        ];
        $stock = new Stock($stock_data);
        $sku->stocks()->save($stock);
        $i++;
      }
    }

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
    $user = User::where('id', '=', $id)
      ->with('roles', 'companies', 'company_designation', 'company_state_branch', 'supervisors', 'notifications', 'salaries', 'distributors')->first();

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
      // 'email'                   => ['required', 'string', 'email', 'max:255'],
      'email'                   => ['required', 'string', 'max:255'],
      'phone'                   =>  'required',
      // 'doj'                     =>  'required',
      // 'dob'                     =>  'required',
      // 'company_designation_id'  =>  'required',
    ]);

    $user->update($request->all());

    if ($request->role_id)
      $user->assignRole($request->role_id);

    $user->assignCompany(1);

    // if ($request['company-id'])
    //   $user->assignCompany($request['company-id']);

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
    if ($user->unique_id == null | $user->unique_id == '') {
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
      ->whereHas('roles', function ($q) {
        $q->where('name', '=', 'Employee');
      })->count();

    return response()->json([
      'data'  =>  $count
    ], 200);
  }
}
