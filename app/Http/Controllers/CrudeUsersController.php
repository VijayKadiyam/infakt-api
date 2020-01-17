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

    foreach($crude_users as $user) {
      if($user->email) {
        $us = User::where('email', '=', $user->email)
          ->orWhere('phone', '=', $user->phone)
          ->first();
        if(!$us) {
          $data = [
            'name'            =>  $user->name,
            'email'           =>  $user->email,
            'phone'           =>  $user->phone == '' ? 0 : $user->phone,
            'password'        =>  bcrypt('123456'),
            'password_backup' =>  bcrypt('123456'),
            'active'          =>  1
          ];
          $us = new User($data);
          $us->save();
          $us->assignRole(3);
          $us->assignCompany(request()->company->id);
        }
      }
    }
  }

  public function truncate()
  {
    CrudeUser::truncate();
  }
}
