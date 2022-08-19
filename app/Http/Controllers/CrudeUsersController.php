<?php

namespace App\Http\Controllers;

use App\CrudeUser;
use App\Imports\UserImport;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CrudeUsersController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index()
    {
        return response()->json([
            'data'  =>  CrudeUser::all()
        ]);
    }

    public function uploadUsers(Request $request)
    {
        if ($request->hasFile('users')) {
            $file = $request->file('users');

            Excel::import(new UserImport, $file);

            return response()->json([
                'data'    =>  CrudeUser::all(),
                'success' =>  true
            ]);
        }
    }

    public function processUsers(Request $request)
    {
        $crude_users = CrudeUser::all();
        foreach ($crude_users as $user) {
            $us = User::where('email', '=', $user->email)
                ->orWhere('id_given_by_school', '=', $user->id_given_by_school)
                ->first();
            $role_id = $user->role_id;
            if (!$us) {
                $data = [
                    'name'               =>  $user->first_name . " " . $user->last_name,
                    'first_name'         =>  $user->first_name,
                    'last_name'          =>  $user->first_name,
                    'email'              =>  $user->email,
                    'password'           =>  bcrypt('123456'),
                    'id_given_by_school' =>  $user->id_given_by_school,
                    'contact_number'     =>  $user->contact_number,
                    'joining_date'     =>  $user->joining_date,
                    'gender'             =>  $user->gender == 'MALE' ? 1 : 0,
                    'active'             =>  $user->active == 'YES' ? 1 : 0,
                ];
                $user = new User($data);
                $user->save();
                $user->assignCompany(request()->company->id);
                $user->assignRole($role_id);
            }
        }
    }

    public function truncate()
    {
        CrudeUser::truncate();
    }
}
