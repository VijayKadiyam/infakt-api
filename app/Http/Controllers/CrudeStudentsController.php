<?php

namespace App\Http\Controllers;

use App\CrudeStudent;
use App\Imports\StudentImport;
use App\Mail\RegistrationMail;
use App\User;
use App\UserClasscode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class CrudeStudentsController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index()
    {
        return response()->json([
            'data'  =>  CrudeStudent::all()
        ]);
    }

    public function uploadStudents(Request $request)
    {
        if ($request->hasFile('students')) {
            $file = $request->file('students');

            Excel::import(new StudentImport, $file);

            return response()->json([
                'data'    =>  CrudeStudent::all(),
                'success' =>  true
            ]);
        }
    }

    public function processStudents(Request $request)
    {
        $crude_students = CrudeStudent::all();
        foreach ($crude_students as $student) {
            $us = User::where('email', '=', $student->email)
                // ->orWhere('id_given_by_school', '=', $student->id_given_by_school)
                ->first();
            $role_id = 5;
            $data = [
                'name'               =>  $student->first_name . " " . $student->last_name,
                'first_name'         =>  $student->first_name,
                'last_name'          =>  $student->last_name,
                'contact_number'     =>  $student->contact_number,
                // 'joining_date'       =>  $student->joining_date,
                'gender'             =>  $student->gender == 'MALE' ? 1 : 0,
                'active'             =>  $student->active == 'YES' ? 1 : 0,
            ];
            if (!$us) {
                // New Student
                $data['email'] = $student->email;
                $data['password'] = bcrypt('123456');
                $data['id_given_by_school'] = $student->id_given_by_school;
                $user = new User($data);
                $user->save();
                $user->assignCompany(request()->company->id);
                $user->assignRole($role_id);
            } else {
                // Existing Student
                $user_id = $us->id;
                $user = User::find($user_id);
                $user->update($data);
            }

            if ($request->is_mail_sent == true) {
                Mail::to($user->email)->send(new RegistrationMail($user));
                $user->is_mail_sent = true;
                $user->update();
            }
            $user_id = $user->id;

            // Manditory Classcode 
            $standard_name = $student->standard;
            $standard = $request->company->standards()->where('name', $standard_name)->first();
            $section_name = $student->section;
            $section = $standard->sections()->where('is_deleted', false)->where('name', $section_name)->first();
            $classcodes = $request->company->classcodes()->where('standard_id', $standard->id)->where('section_id', $section->id)->where('is_optional', false)->get();
            if ($classcodes) {
                foreach ($classcodes as $key => $classcode) {
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

            // Optional Classcode if Entered
            for ($i = 1; $i <= 10; $i++) {
                $name = 'optional_classcode_' . $i;
                $excel_class_code = $student->$name;
                if ($excel_class_code) {
                    $optional_classcode = $request->company->classcodes()->where('classcode', $excel_class_code)->first();
                    if ($optional_classcode) {
                        $UserClasscode = $request->company->user_classcodes()->where('classcode_id', $optional_classcode->id)->where('user_id', $user_id)->first();
                        if (!$UserClasscode) {
                            // No Previously Existing User Classcode 
                            $user_classcode = [
                                'user_id' => $user_id,
                                'classcode_id' => $optional_classcode->id,
                                'standard_id' => $optional_classcode->standard_id,
                                'section_id' => $optional_classcode->section_id,
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
        CrudeStudent::truncate();
    }
}
