<?php

namespace App\Http\Controllers;

use App\CrudeTeacher;
use App\Imports\TeacherImport;
use App\User;
use App\UserClasscode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\RegistrationMail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class CrudeTeachersController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index()
    {
        return response()->json([
            'data'  =>  CrudeTeacher::all()
        ]);
    }

    public function uploadTeachers(Request $request)
    {
        if ($request->hasFile('teachers')) {
            $file = $request->file('teachers');

            Excel::import(new TeacherImport, $file);

            return response()->json([
                'data'    =>  CrudeTeacher::all(),
                'success' =>  true
            ]);
        }
    }

    public function processTeachers(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", "-1");
        $crude_teachers = CrudeTeacher::all();
        foreach ($crude_teachers as $teacher) {
            $us = User::where('email', '=', $teacher->email)
                // ->orWhere('id_given_by_school', '=', $teacher->id_given_by_school)
                ->first();
            $role_id = 3;
            $data = [
                'name'           =>  $teacher->first_name . " " . $teacher->last_name,
                'first_name'     =>  $teacher->first_name,
                'last_name'      =>  $teacher->last_name,
                'contact_number' =>  $teacher->contact_number,
                'joining_date'   =>  $teacher->joining_date,
                'gender'         =>  $teacher->gender == 'MALE' ? 1 : 0,
                'active'         =>  $teacher->active == 'YES' ? 1 : 0,
            ];
            if (!$us) {
                // New Teacher
                $data['email'] = $teacher->email;
                $data['password'] = bcrypt('123456');
                $data['id_given_by_school'] = $teacher->id_given_by_school;
                $user = new User($data);
                $user->save();
                $user->assignCompany(request()->company->id);
                $user->assignRole($role_id);
            } else {
                // Existing Teacher
                $user_id = $us->id;
                $user = User::find($user_id);
                $user->update($data);
            }
            // send email
            if ($request->is_mail_sent == true) {
                Mail::to($user->email)->send(new RegistrationMail($user));
                $user->is_mail_sent = true;
                $user->update();
            }
            $user_id = $user->id;
            for ($i = 1; $i <= 10; $i++) {
                $name = 'classcode_' . $i;
                $excel_class_code = $teacher->$name;
                if ($excel_class_code) {
                    $classcode = $request->company->classcodes()->where('classcode', $excel_class_code)->first();
                    if ($classcode) {
                        $UserClasscode = $request->company->user_classcodes()->where('classcode_id', $classcode->id)->where('user_id', $user_id)->first();
                        if (!$UserClasscode) {
                            // No Previously Existing User Classcode 
                            $user_classcode = [
                                'user_id' => $user_id,
                                'classcode_id' => $classcode->id,
                                'standard_id' => $classcode->standard_id,
                                'section_id' => $classcode->section_id,
                                'start_date' => Carbon::now()->format('Y-m-d'),
                                'end_date' => Carbon::now()->format('Y-m-d'),
                            ];
                            $user_classcodes = new UserClasscode($user_classcode);
                            $request->company->user_classcodes()->save($user_classcodes);
                        }
                    }
                }
            }
        }
    }

    public function truncate()
    {
        CrudeTeacher::truncate();
    }
}
