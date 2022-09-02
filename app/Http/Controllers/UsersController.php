<?php

namespace App\Http\Controllers;

use App\ImportBatch;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use \Carbon\Carbon;
use App\Sku;
use App\Stock;
use App\UserClasscode;
use App\Value;

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

    $standardsController = new StandardsController();
    $standardsResponse = $standardsController->index($request);

    $sectionsController = new SectionsController();
    $sectionsResponse = $sectionsController->all_sections($request);

    $classcodesController = new ClasscodesController();
    $classcodesResponse = $classcodesController->all_classcodes($request);

    $boardsController = new BoardsController();
    $boardsResponse = $boardsController->index($request);

    return response()->json([
      'roles'      =>  $rolesResponse->getData()->data,
      'standards'  =>  $standardsResponse->getData()->data,
      'sections'   =>  $sectionsResponse->getData()->data,
      'classcodes' =>  $classcodesResponse->getData()->data,
      'boards' =>  $boardsResponse->getData()->data,
    ], 200);
  }

  /*
   * To get all the users
   *
   *@
   */
  public function index(Request $request)
  {
    $users = User::where('is_deleted', false)->with('roles');
    if ($request->role_id) {
      // return $request->role_id;
      $role = Role::find($request->role_id);
      $users = User::with('roles')->whereHas('roles', function ($q) use ($role) {
        $q->where('name', '=', $role->name);
      });
    }
    if ($request->standard_id) {
      $users = $users->whereHas('user_classcodes', function ($uc) {
        $uc->where('standard_id', '=', request()->standard_id);
      });
    }
    if ($request->section_id) {
      $users = $users->whereHas('user_classcodes', function ($uc) {
        $uc->where('section_id', '=', request()->section_id);
      });
    }
    if ($request->classcode_id) {
      $users = $users->whereHas('user_classcodes', function ($uc) {
        $uc->where('classcode_id', '=', request()->classcode_id);
      });
    }
    $users = $users->get();
    return response()->json([
      'data'  =>  $users,
      'count' =>   sizeof($users),
      'success' =>  true,
    ], 200);
  }
  // {
  //   $count = 0;
  //   // $role = 3;
  //   $users = [];
  //   if (request()->page && request()->rowsPerPage) {
  //     $users = request()->company->users()
  //       ->whereHas('roles',  function ($q) {
  //         $q->where('name', '!=', 'ADMIN');
  //       });
  //     $users = $users->paginate(request()->rowsPerPage)->toArray();
  //     $users = $users['data'];
  //   } else if ($request->search == 'all') {
  //     $users = $request->company->users()
  //       ->whereHas('roles',  function ($q) {
  //         $q->where('name', '!=', 'ADMIN');
  //       })
  //       ->latest()->get();
  //   } else if ($request->searchEmp) {
  //     $users = $request->company->users()->with('roles')
  //       ->whereHas('roles',  function ($q) {
  //         $q->where('name', '!=', 'ADMIN');
  //       });

  //     $users = $users->where('name', 'LIKE', '%' . $request->searchEmp . '%')
  //       ->orWhere('email', 'LIKE', '%' . $request->searchEmp . '%')
  //       ->orWhere('phone', 'LIKE', '%' . $request->searchEmp . '%')
  //       ->latest()->get();
  //     // return $users;
  //   } else if ($request->role_id) {
  //     $role = Role::find($request->role_id);
  //     $users = $request->company->allUsers()
  //       ->whereHas('roles', function ($q) use ($role) {
  //         $q->where('name', '=', $role->name);
  //       });
  //     if ($request->status != 'all')
  //       $users = $users->where('active', '=', 1);
  //     $users = $users->latest()->get();
  //   } else {
  //     $users = $request->company->users()->with('roles')
  //       ->whereHas('roles',  function ($q) {
  //         $q->where('name', '!=', 'ADMIN');
  //       })->latest()->get();
  //   }
  //   $count = sizeOf($users);
  //   return response()->json([
  //     'data'  =>  $users,
  //     'count' =>   $count,
  //     'success' =>  true,
  //   ], 200);
  // }

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
      $users = $users->paginate(request()->rowsPerPage)->latest()->toArray();
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

    $users = $request->company->users()
      ->whereHas('roles',  function ($q) {
        $q->where('name', '!=', 'ADMIN');
      });;
    if ($request->region) {
      $users = $users
        ->where('region', 'LIKE', '%' . $request->region . '%');
    }
    $users = $users->get();
    $count = $users->count();
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
  public function store_1(Request $request)
  {
    $request->validate([
      'first_name'                    => ['required', 'string', 'max:255'],
      'last_name'                    => ['required', 'string', 'max:255'],
      'email'                   => ['required', 'string', 'max:255', 'unique:users'],
      'role_id'                 =>  'required',
    ]);

    $user['first_name'] = $request->first_name;
    $user['last_name'] = $request->last_name;
    $user['name'] =  $request->first_name . ' ' . $request->last_name;
    $user['email'] = $request->email;
    $user['active'] = $request->active;
    $user['contact_number'] = $request->contact_number;
    $user['id_given_by_school'] = $request->id_given_by_school;
    $user['joining_date'] = $request->joining_date;
    $user['gender'] = $request->gender;
    $user['password'] = bcrypt('123456');
    // $user['password_backup'] = bcrypt('123456');

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
    $user = User::where('id', '=', $id)
      ->with('roles', 'companies', 'user_classcodes')->first();

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
      'email'                   => ['required', 'string', 'max:255'],
    ]);

    $user->update($request->all());

    if ($request->role_id)
      $user->assignRole($request->role_id);

    if ($request->user_classcodes) {
      foreach ($request->user_classcodes as $key => $uc) {
        $user_classcodes = new UserClasscode($uc);
        $user->user_classcodes()->save($user_classcodes);
      }
    }
    // $user->assignCompany(1);
    $user->roles = $user->roles;
    $user->companies = $user->companies;

    return response()->json([
      'data'  =>  $user,
      'message' =>  "User is Logged in Successfully",
      'success' =>  true
    ], 200);
  }

  public function store(Request $request)
  {
    if ($request->id == null || $request->id == '') {
      $request->validate([
        'first_name'                    => ['required', 'string', 'max:255'],
        'last_name'                    => ['required', 'string', 'max:255'],
        'email'                   => ['required', 'string', 'max:255', 'unique:users'],
        'role_id'                 =>  'required',
      ]);
      // Save User
      $user['first_name'] = $request->first_name;
      $user['last_name'] = $request->last_name;
      $user['name'] =  $request->first_name . ' ' . $request->last_name;
      $user['email'] = $request->email;
      $user['active'] = $request->active;
      $user['contact_number'] = $request->contact_number;
      $user['id_given_by_school'] = $request->id_given_by_school;
      $user['joining_date'] = $request->joining_date;
      $user['gender'] = $request->gender;
      $user['board_id'] = $request->board_id;
      $user['password'] = bcrypt('123456');
      $user = new User($user);
      $user->save();

      $user->assignRole($request->role_id);
      $user->roles = $user->roles;
      if ($request->company)
        $user->assignCompany($request->company->id);
      if ($request->company_id)
        $user->assignCompany($request->company_id);
      $user->companies = $user->companies;

      // Save User Classcodes
      if (isset($request->user_classcodes))
        foreach ($request->user_classcodes as $classcode) {
          $classcode = new UserClasscode($classcode);
          $user->user_classcodes()->save($classcode);
        }
      // ---------------------------------------------------
    } else {
      // Update User
      $request->validate([
        'name'                    => ['required', 'string', 'max:255'],
        'email'                   => ['required', 'string', 'max:255'],
      ]);

      $user = User::find($request->id);
      $user->update($request->all());

      // Check if User Classcode deleted
      if (isset($request->user_classcodes)) {
        $userClasscodeIdResponseArray = array_pluck($request->user_classcodes, 'id');
      } else
        $userClasscodeIdResponseArray = [];
      $userId = $user->id;
      $userClasscodeIdArray = array_pluck(UserClasscode::where('user_id', '=', $userId)->get(), 'id');
      $differenceUserClasscodeIds = array_diff($userClasscodeIdArray, $userClasscodeIdResponseArray);
      // Delete which is there in the database but not in the response
      if ($differenceUserClasscodeIds)
        foreach ($differenceUserClasscodeIds as $differenceUserClasscodeId) {
          $userClasscode = UserClasscode::find($differenceUserClasscodeId);
          $userClasscode->delete();
        }

      // Update User Classcode
      if (isset($request->user_classcodes))
        foreach ($request->user_classcodes as $classcode) {
          if (!isset($classcode['id'])) {
            $user_classcode = new UserClasscode($classcode);
            $user->user_classcodes()->save($user_classcode);
          } else {
            $user_classcode = UserClasscode::find($classcode['id']);
            $user_classcode->update($classcode);
          }
        }

      // ---------------------------------------------------
    }
    $user->user_classcodes = $user->user_classcodes;
    return response()->json([
      'data'  =>  $user
    ], 201);
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
