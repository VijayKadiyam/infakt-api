<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\CareerRequest;
use App\Company;
use App\ContactRequest;
use App\Content;
use App\ContentSubject;
use App\EtArticle;
use App\Subject;
use App\ToiArticle;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function masters()
    {
        $months = [
            ['text'  =>  'JANUARY', 'value' =>  1],
            ['text'  =>  'FEBRUARY', 'value' =>  2],
            ['text'  =>  'MARCH', 'value' =>  3],
            ['text'  =>  'APRIL', 'value' =>  4],
            ['text'  =>  'MAY', 'value' =>  5],
            ['text'  =>  'JUNE', 'value' =>  6],
            ['text'  =>  'JULY', 'value' =>  7],
            ['text'  =>  'AUGUST', 'value' =>  8],
            ['text'  =>  'SEPTEMBER', 'value' =>  9],
            ['text'  =>  'OCTOBER', 'value' =>  10],
            ['text'  =>  'NOVEMBER', 'value' =>  11],
            ['text'  =>  'DECEMBER', 'value' =>  12],
        ];
        $years = ['2020', '2021', '2022'];
        $schools = Company::all();

        return response()->json([
            'months'      =>  $months,
            'years'       =>  $years,
            'schools'   =>  $schools
        ], 200);
    }


    public function superadminDashboard(Request $request)
    {
        $schoolsCount =   Company::all()->count();
        $teachersCount =  User::where('is_deleted', false)
            ->whereHas('roles', function ($q) {
                $q->where('name', '=', 'TEACHER');
            })->count();
        $studentsCount =  User::where('is_deleted', false)
            ->whereHas('roles', function ($q) {
                $q->where('name', '=', 'STUDENT');
            })->count();
        $contentsCount = Content::all()->count();
        $toi_papersCount = ToiArticle::all()->count();
        $et_papersCount = EtArticle::all()->count();
        $contactRequestsCount = ContactRequest::where('is_deleted', false)->count();
        $careerRequestsCount = CareerRequest::where('is_deleted', false)->count();
        $data = [
            'schoolsCount'  =>  $schoolsCount,
            'studentsCount'  =>  $studentsCount,
            'paidStudentsCount'  =>  $studentsCount,
            'freeStudentsCount'  =>  0,
            'teachersCount'  =>  $teachersCount,
            'contentsCount'   =>  $contentsCount,
            'toi_papersCount'   =>  $toi_papersCount,
            'et_papersCount'   =>  $et_papersCount,
            'RequestsCount'   =>  $contactRequestsCount + $careerRequestsCount,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }

    public function adminDashboard(Request $request)
    {
        $studentsCount =  $request->company->allUsers()
            ->whereHas('roles', function ($q) {
                $q->where('name', '=', 'STUDENT');
            })->count();
        $teachersCount =  $request->company->allUsers()
            ->whereHas('roles', function ($q) {
                $q->where('name', '=', 'TEACHER');
            })->count();
        $classesCount = $request->company->classcodes()->count();
        $data = [
            'studentsCount'  =>  $studentsCount,
            'teachersCount'  =>  $teachersCount,
            'classesCount'   =>  $classesCount,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }

    public function teacherDashboard(Request $request)
    {
        $studentsCount =  $request->company->allUsers()
            ->whereHas('roles', function ($q) {
                $q->where('name', '=', 'STUDENT');
            })->count();

        $classesCount = $request->company->classcodes()->count();
        $assignmentsCount = $request->company->assignments()->count();
        $data = [
            'studentsCount'  =>  $studentsCount,
            'classesCount'  =>  $classesCount,
            'assignmentsCount'   =>  $assignmentsCount,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }

    public function studentDashboard()
    {
        $data = [
            'assignmentsCompetedCount'  =>  0,
            'articlesReadCount'         =>  0,
            'videosWatchedCount'        =>  0,
            'assignmentsPendingCount'   =>  0,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }

    public function contentBasedCount()
    {
        $content_based_count = [];
        // For Grade
        if (request()->type == 1) {
            return request()->type;
        }
        // For Board
        if (request()->type == 2) {
            return request()->type;
        }
        // For Scholls
        if (request()->type == 3) {
            $schools = Company::get();
            foreach ($schools as $key => $school) {
                $contents =  Content::where('school_id', $school->id)->get();
                $content_based_count[$key]['name'] = $school->name;
                $content_based_count[$key]['count'] = sizeof($contents);
            }
        }
        // For Subject
        if (request()->type == 4) {
            $subjects = Subject::get();
            foreach ($subjects as $key => $subject) {
                $contents =  ContentSubject::where('subject_id', $subject->id)->get();
                $content_based_count[$key]['name'] = $subject->name;
                $content_based_count[$key]['count'] = sizeof($contents);
            }
        }
        $data = [
            'contentBasedCount'   =>  $content_based_count,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }

    public function SchoolWiseOverview()
    {
        $month = request()->month;
        $year = request()->year;
        $end_date = Carbon::parse("$year-$month")->endOfMonth()->format('Y-n-d');
        $start_date = Carbon::parse("$year-$month")->subMonths(3)->startOfMonth()->format('Y-n-d');

        if (request()->company_id) {
            $company = Company::find(request()->company_id);

            $teachersCount = $company->users()->where('is_deleted', false)->with('roles');
            $L3M_Assignment_contents_count = $company->assignments()->where('is_deleted', false);
            $assignments_count = $company->assignments()->where('is_deleted', false);
        } else {
            $teachersCount = User::where('is_deleted', false)->with('roles');
            $L3M_Assignment_contents_count = Assignment::where('is_deleted', false);
            $assignments_count = Assignment::where('is_deleted', false);
        }

        $teachersCount = $teachersCount->whereHas('roles', function ($q) {
            $q->where('name', '=', 'TEACHER');
        })->count();

        $L3M_Assignment_contents_count = $L3M_Assignment_contents_count
            ->whereBetween("created_at", [$start_date, $end_date])
            ->count();

        $assignments_count = $assignments_count
            ->whereMonth("created_at", $month)
            ->count();

        $data = [
            'teachersCount'  =>  $teachersCount,
            'L3M_Assignment_contents_count'  =>  $L3M_Assignment_contents_count,
            'assignments_count'  =>  $assignments_count,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }

    public function ClasscodeWiseOverview()
    {
        $type = request()->type;
        $month = request()->month;
        $year = request()->year;

        if (request()->company) {
            $teachers = request()->company->allUsers()->where('is_deleted', false)->with('roles', 'user_classcodes', 'assignments');
            $studentsCount = request()->company->allUsers()->where('is_deleted', false)->with('roles', 'user_classcodes', 'user_assignments');
            $classesCount = request()->company->classcodes();
        }
        if ($type == 1) {
            // Classcode
            $teachers = $teachers->whereHas('user_classcodes', function ($uc) {
                $uc->where('classcode_id', '=', request()->type_id);
            });
            $studentsCount = $studentsCount->whereHas('user_classcodes', function ($uc) {
                $uc->where('classcode_id', '=', request()->type_id);
            });
            $classesCount = $classesCount->where('id', request()->type_id);
        }
        if ($type == 2) {
            // Teacher

        }
        if ($type == 3) {
            // Student
        }
        if ($type == 4) {
            // Assignment
        }

        $teachers = $teachers->whereHas('roles', function ($q) {
            $q->where('name', '=', 'TEACHER');
        })->get();
        foreach ($teachers as $key => $teacher) {
            $teacher->assignment_count = 0;
            if ($teacher->assignments) {
                $teacher->assignment_count = sizeof($teacher->assignments);
            }
            // Top Teachers Based on Number of Assignment posted
            $top_teachers = $teacher;
        }

        return $teachers;
        $studentsCount = $studentsCount->whereHas('roles', function ($q) {
            $q->where('name', '=', 'STUDENT');
        })->count();
        $classesCount = $classesCount->count();


        $data = [
            'teachers'  =>  $teachers,
            'teachersCount'  =>  $teachers->count(),
            'studentsCount'  =>  $studentsCount,
            'classesCount'  =>  $classesCount,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }
}
