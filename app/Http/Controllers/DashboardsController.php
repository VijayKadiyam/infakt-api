<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Board;
use App\CareerRequest;
use App\Classcode;
use App\Company;
use App\ContactRequest;
use App\Content;
use App\ContentMetadata;
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

    public function getSubTypes(Request $request)
    {
        $request->validate([
            'schoolId'  =>  'required',
            'type'  =>  'required'
        ]);
        $subTypes = [];
        if ($request->type == 'Classcode') {
            $subTypes = Classcode::where('company_id', '=', $request->schoolId)
                ->get();
        }
        if ($request->type == 'Teacher') {
            $company = Company::find($request->schoolId);
            $subTypes = $company->users()
                ->whereHas('roles', function ($q) {
                    $q->where('name', '=', 'TEACHER');
                })
                ->get();
        }

        return response()->json([
            'data'  =>  $subTypes
        ]);
    }

    public function superadminDashboard(Request $request)
    {
        // Board Wise School Count
        $boards = Board::where('is_active', TRUE)->get();
        $BoardSchoolCount = [];
        foreach ($boards as $key => $board) {
            $board_detail = [];
            $schools = $board->schools;
            $board_name = $board->name;
            $board_detail = [
                'name' => $board_name,
                'count' => sizeof($schools),
            ];
            $BoardSchoolCount[] = $board_detail;
        }
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
        // Contact Request Count Section
        $contactRequestsCount = ContactRequest::where('is_deleted', false)->get();
        $settled_contact_request = 0;
        $pending_contact_request = 0;
        foreach ($contactRequestsCount as $key => $request) {
            if ($request->status == "SETTLED") {
                $settled_contact_request++;
            } else {
                $pending_contact_request++;
            }
        }
        // Career Request Count Section
        $careerRequestsCount = CareerRequest::where('is_deleted', false)->get();
        $settled_career_request = 0;
        $pending_career_request = 0;
        foreach ($careerRequestsCount as $key => $request) {
            if ($request->status == "SETTLED") {
                $settled_career_request++;
            } else {
                $pending_career_request++;
            }
        }

        $RequestsCount = [
            'settled_contact_request' => $settled_contact_request,
            'pending_contact_request' => $pending_contact_request,
            'total_contact_request' => $contactRequestsCount->count(),
            'settled_career_request' => $settled_career_request,
            'pending_career_request' => $pending_career_request,
            'total_career_request' => $careerRequestsCount->count(),
            'total' => $contactRequestsCount->count() + $careerRequestsCount->count(),
        ];
        $data = [
            'BoardSchoolCount'  =>  $BoardSchoolCount,
            'studentsCount'  =>  $studentsCount,
            'paidStudentsCount'  =>  $studentsCount,
            'freeStudentsCount'  =>  0,
            'teachersCount'  =>  $teachersCount,
            'contentsCount'   =>  $contentsCount,
            'toi_papersCount'   =>  $toi_papersCount,
            'et_papersCount'   =>  $et_papersCount,
            'RequestsCount'   =>  $RequestsCount,
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
            $L3M_Assignment_contents_count = $company->assignments()->where('is_deleted', false);
            $assignments_count = $company->assignments()->where('is_deleted', false);
        } else {
            $L3M_Assignment_contents_count = Assignment::where('is_deleted', false);
            $assignments_count = Assignment::where('is_deleted', false);
        }

        $L3M_Assignment_contents_count = $L3M_Assignment_contents_count
            ->whereBetween("created_at", [$start_date, $end_date])
            ->count();

        $assignments_count = $assignments_count
            ->whereMonth("created_at", $month)
            ->count();

        $data = [
            'avg_time_spent_by_student'  =>  0,
            'avg_time_spent_by_teacher'  =>  0,
            'L3M_Assignment_contents_count'  =>  $L3M_Assignment_contents_count,
            'assignments_count'  =>  $assignments_count,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }

    public function topSchoolBasedOnAssignments()
    {
        $schools = Company::get();
        $topSchool = [];
        foreach ($schools as $key => $school) {
            $assignments = Assignment::where('company_id', $school->id)
                ->whereMonth('created_at', request()->month)
                ->whereYear('created_at', request()->year)
                ->get();

            $topSchool[$key]['name'] = $school->name;
            $topSchool[$key]['score'] = sizeof($assignments);
        }
        usort($topSchool, function ($a, $b) {
            return $b['score'] - $a['score'];
        });

        $topSchool = array_slice($topSchool, 0, 10);

        $data = [
            'top_schools'  =>  $topSchool,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }

    public function ClasscodeWiseOverview()
    {
        if (request()->company_id) {
            $company     = Company::find(request()->company_id);
            $teachers    = $company->allUsers()->where('is_deleted', false);
            $students    = $company->allUsers()->where('is_deleted', false);
            $classes     = $company->classcodes()->where('is_deleted', false);
            $assignments = $company->assignments();
        }
        if (request()->type_id) {
            // Classcode
            $teachers = $teachers->whereHas('user_classcodes', function ($uc) {
                $uc->where('classcode_id', '=', request()->type_id);
            });
            $students = $students->whereHas('user_classcodes', function ($uc) {
                $uc->where('classcode_id', '=', request()->type_id);
            });
            $classes = $classes->where('id', request()->type_id);
            $assignments = $assignments->wherehas('assignment_classcodes', function ($uc) {
                $uc->where('classcode_id', '=', request()->type_id);
            });
        }

        $teachers = $teachers->whereHas('roles', function ($q) {
            $q->where('name', '=', 'TEACHER');
        })->get();

        $students = $students->whereHas('roles', function ($q) {
            $q->where('name', '=', 'STUDENT');
        })->get();

        $classes = $classes->get();

        $assignments = $assignments->get();
        /******** Top Teachers */
        $top_teachers = [];
        $total_teacher_read_count = 0;
        foreach ($teachers as $key => $teacher) {
            $teacher['assignment_count'] = 0;
            if ($teacher->assignments) {
                $teacher['assignment_count'] = sizeof($teacher->assignments);
            }

            $teacher['read_count'] = 0;
            if ($teacher->content_reads) {
                $teacher['read_count'] = sizeof($teacher->content_reads);
            }
            $total_teacher_read_count += $teacher['read_count'];
            // Top Teachers Based on Number of Assignment posted
            $top_teachers[] = $teacher;
        }
        // Sorting Descending by Average
        usort($top_teachers, function ($a, $b) {
            return $b['assignment_count'] - $a['assignment_count'];
        });

        /******** Top Student */
        $top_students_count = 0;
        $avg_students_count = 0;
        $below_avg_students_count = 0;
        $weak_students_count = 0;
        $total_maximum_marks = 0;
        $top_students = [];
        $total_student_read_count = 0;

        foreach ($students as $key => $student) {
            $student['average'] = 0;
            $total_scored = 0;
            $average = 0;
            $user_assignments = $student->user_assignments;
            $assignment_submitted = sizeof($user_assignments);
            foreach ($user_assignments as $key => $ua) {
                $total_maximum_marks += $ua->assignment->maximum_marks;
                $score = $ua->score;
                $total_scored += $score;
            }
            if ($assignment_submitted != 0) {
                $average = $total_scored / $assignment_submitted;
                $student['average'] = $average;
            }
            switch (true) {
                case ($average >= 76):
                    $grade = 'A';
                    $top_students_count++;
                    break;
                case ($average >= 60 && $average < 76):
                    $grade = 'B';
                    $avg_students_count++;
                    break;
                case ($average >= 59 && $average < 36):
                    $grade = 'C';
                    $below_avg_students_count++;
                    break;
                case ($average < 36):
                    $grade = 'D';
                    $weak_students_count++;
                    break;
            }
            // Top Students Based on Average
            $top_students[] = $student;
            //  Total Student Content read Count
            $student['read_count'] = 0;
            if ($student->content_reads) {
                $student['read_count'] = sizeof($student->content_reads);
            }
            $total_student_read_count += $student['read_count'];
        }
        // Sorting Descending by Average
        usort($top_students, function ($a, $b) {
            return $b['average'] - $a['average'];
        });

        /******** Top Classcodes */
        $top_classes = [];
        foreach ($classes as $key => $class) {
            $class['assignment_count'] = 0;
            if ($class->assignment_classcodes) {
                $class['assignment_count'] = sizeof($class->assignment_classcodes);
            }
            // Top Classcodes Based on Number of Assignment posted
            $top_classes[] = $class;
        }
        // Sorting Descending by Average
        usort($top_classes, function ($a, $b) {
            return $b['assignment_count'] - $a['assignment_count'];
        });

        // Assignment Type Overview
        $subjective_assignment_count = 0;
        $objective_assignment_count = 0;
        $document_assignment_count = 0;
        foreach ($assignments as $key => $assignment) {
            switch ($assignment->assignment_type) {
                case 'SUBJECTIVE':
                    $subjective_assignment_count++;
                    break;

                case 'OBJECTIVE':
                    $objective_assignment_count++;
                    break;

                case 'DOCUMENT':
                    $document_assignment_count++;
                    break;

                default:
                    # code...
                    break;
            }
        }

        $data = [
            'teachers'      =>  $teachers,
            'teachersCount' =>  $teachers->count(),
            'students'      =>  $students,
            'studentsCount' => $students->count(),
            'classes'       =>  $classes,
            'classesCount'  =>  $classes->count(),
            'top_10_teachers'  =>  array_slice($top_teachers, 0, 10),  //  Top 10 Teachers
            'top_10_students'  =>  array_slice($top_students, 0, 10),  //  Top 10 Students
            'top_10_classes'   =>  array_slice($top_classes, 0, 10),   //  Top 10 Classes
            //Student Wise Performance 
            'top_students_count'            => $top_students_count,
            'avg_students_count'            => $avg_students_count,
            'below_avg_students_count'      => $below_avg_students_count,
            'weak_students_count'           => $weak_students_count,
            // Assignment Type Overview
            'subjective_assignment_count'      => $subjective_assignment_count,
            'objective_assignment_count'       => $objective_assignment_count,
            'document_assignment_count'        => $document_assignment_count,
            // Total Content Read
            'total_teacher_read_count'      => $total_teacher_read_count,
            'total_student_read_count'      => $total_student_read_count,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }

    public function TeacherWiseOverview()
    {
        $teacher_id = request()->type_id;
        if (request()->company_id) {
            $company     = Company::find(request()->company_id);
            $teachers    = $company->allUsers()->where('is_deleted', false);
            $classes     = $company->classcodes()->where('is_deleted', false);
            $assignments = $company->assignments();
        }
        if ($teacher_id) {
            // Teacher
            $teachers = $teachers->where('users.id', $teacher_id);
            $classes = $classes->whereHas('user_classcodes', function ($uc) use ($teacher_id) {
                $uc->where('user_id', '=', $teacher_id);
            });
            $assignments = $assignments->where('created_by_id', $teacher_id);
        }

        $teachers = $teachers->whereHas('roles', function ($q) {
            $q->where('name', '=', 'TEACHER');
        })->get();
        $classes = $classes->get();

        $assignments = $assignments->get();
        /******** Top Teachers */
        $top_teachers = [];
        $total_teacher_read_count = 0;
        foreach ($teachers as $key => $teacher) {
            $teacher['assignment_count'] = 0;
            if ($teacher->assignments) {
                $teacher['assignment_count'] = sizeof($teacher->assignments);
            }
            // Top Teachers Based on Number of Assignment posted
            $top_teachers[] = $teacher;
            // Total Teacher Content Read COunt 
            $teacher['read_count'] = 0;
            if ($teacher->content_reads) {
                $teacher['read_count'] = sizeof($teacher->content_reads);
            }
            $total_teacher_read_count += $teacher['read_count'];
        }
        // Sorting Descending by Average
        usort($top_teachers, function ($a, $b) {
            return $b['assignment_count'] - $a['assignment_count'];
        });

        /******** Top Classcodes */
        $final_student_performance = [];
        $top_classes = [];
        $total_students = [];
        $total_student_read_count = 0;

        foreach ($classes as $key => $class) {
            $class_tsc = 'top_students_count_' . $class->id;
            $class_asc = 'avg_students_count_' . $class->id;
            $class_basc = 'below_avg_students_count_' . $class->id;
            $class_wsc = 'weak_students_count_' . $class->id;
            $class_tmm = 'total_maximum_marks_' . $class->id;
            $class_ts = 'top_students_' . $class->id;
            $class['assignment_count'] = 0;
            if ($class->assignment_classcodes) {
                $class['assignment_count'] = sizeof($class->assignment_classcodes);
            }
            // Top Classcodes Based on Number of Assignment posted
            $top_classes[] = $class;

            /******** Top Student */
            $students = $class->students;
            $$class_tsc = 0;            // $top_students_count = 0;
            $$class_asc = 0;            // $avg_students_count = 0;
            $$class_basc = 0;           // $below_avg_students_count = 0;
            $$class_wsc = 0;            // $weak_students_count = 0;
            $$class_tmm = 0;            // $total_maximum_marks = 0;
            $$class_ts = [];            // $top_students = [];
            foreach ($students as $key => $student) {
                $student['average'] = 0;
                $total_scored = 0;
                $average = 0;
                $user_assignments = $student->user_assignments;
                $assignment_submitted = sizeof($user_assignments);
                foreach ($user_assignments as $key => $ua) {
                    $$class_tmm += $ua->assignment->maximum_marks;
                    $score = $ua->score;
                    $total_scored += $score;
                }
                if ($assignment_submitted != 0) {
                    $average = $total_scored / $assignment_submitted;
                    $student['average'] = $average;
                }
                switch (true) {
                    case ($average >= 76):
                        $grade = 'A';
                        $$class_tsc++;
                        break;
                    case ($average >= 60 && $average < 76):
                        $grade = 'B';
                        $$class_asc++;
                        break;
                    case ($average >= 59 && $average < 36):
                        $grade = 'C';
                        $$class_basc++;
                        break;
                    case ($average < 36):
                        $grade = 'D';
                        $$class_wsc++;
                        break;
                }
                // Top Students Based on Average
                $$class_ts[] = $student;
                $total_students[] = $student;
                //  Total Student Content read Count
                $student['read_count'] = 0;
                if ($student->content_reads) {
                    $student['read_count'] = sizeof($student->content_reads);
                }
                $total_student_read_count += $student['read_count'];
            }
            // Sorting Descending by Average
            usort($$class_ts, function ($a, $b) {
                return $b['average'] - $a['average'];
            });

            // Top 10 Students of that Class
            $top_students = array_slice($$class_ts, 0, 10);
            $final_top_student[] = [
                "classcode"  =>   $class->classcode,
                "students" =>  $top_students
            ];
            //Student Wise Performance 
            $student_performance = [
                'id'                       => $class->id,
                'classcode'                =>  $class->classcode,
                'top_students_count'       => $$class_tsc,
                'avg_students_count'       => $$class_asc,
                'below_avg_students_count' => $$class_basc,
                'weak_students_count'      => $$class_wsc,
            ];
            $final_student_performance[] = $student_performance;
        }
        // Sorting Descending by Average
        usort($top_classes, function ($a, $b) {
            return $b['assignment_count'] - $a['assignment_count'];
        });

        // Assignment Type Overview
        $subjective_assignment_count = 0;
        $objective_assignment_count = 0;
        $document_assignment_count = 0;
        foreach ($assignments as $key => $assignment) {
            switch ($assignment->assignment_type) {
                case 'SUBJECTIVE':
                    $subjective_assignment_count++;
                    break;

                case 'OBJECTIVE':
                    $objective_assignment_count++;
                    break;

                case 'DOCUMENT':
                    $document_assignment_count++;
                    break;

                default:
                    # code...
                    break;
            }
        }
        $data = [
            'teachers'      =>  $teachers,
            'teachersCount' =>  $teachers->count(),
            'students'      =>  $total_students,
            'studentsCount' => sizeOf($total_students),
            'classes'       =>  $classes,
            'classesCount'  =>  $classes->count(),
            'top_10_teachers'  =>  array_slice($top_teachers, 0, 10),  //  Top 10 Teachers
            'top_10_students'  =>  $final_top_student,  //  Top 10 Students Array
            'top_10_classes'   =>  array_slice($top_classes, 0, 10),   //  Top 10 Classes
            //Student Wise Performance Array
            'final_student_performance' => $final_student_performance,
            // Assignment Type Overview
            'subjective_assignment_count'      => $subjective_assignment_count,
            'objective_assignment_count'       => $objective_assignment_count,
            'document_assignment_count'        => $document_assignment_count,
            // Total Content Read
            'total_teacher_read_count'      => $total_teacher_read_count,
            'total_student_read_count'      => $total_student_read_count,

        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }

    public function StudentWiseOverview()
    {
        $student_id = request()->type_id;
        if (request()->company_id) {
            $company     = Company::find(request()->company_id);
            $students    = $company->allUsers()->where('is_deleted', false);
            $classes     = $company->classcodes()->where('is_deleted', false);
            $assignments = $company->assignments();
        }
        if ($student_id) {
            // Student 
            $students = $students->where('users.id', $student_id);
            $classes = $classes->whereHas('user_classcodes', function ($uc) use ($student_id) {
                $uc->where('user_id', '=', $student_id);
            });
            $assignments = $assignments->wherehas('user_assignments', function ($uc) use ($student_id) {
                $uc->where('user_id', '=', $student_id);
            });
        }

        $students = $students->whereHas('roles', function ($q) {
            $q->where('name', '=', 'STUDENT');
        })->get();

        $classes = $classes->get();
        $assignments = $assignments->get();

        /******** Top Student */
        $top_students_count = 0;
        $avg_students_count = 0;
        $below_avg_students_count = 0;
        $weak_students_count = 0;
        $total_maximum_marks = 0;
        $top_students = [];
        $total_student_read_count = 0;

        foreach ($students as $key => $student) {
            $student['average'] = 0;
            $total_scored = 0;
            $average = 0;
            $user_assignments = $student->user_assignments;
            $assignment_submitted = sizeof($user_assignments);
            foreach ($user_assignments as $key => $ua) {
                $total_maximum_marks += $ua->assignment->maximum_marks;
                $score = $ua->score;
                $total_scored += $score;
            }
            if ($assignment_submitted != 0) {
                $average = $total_scored / $assignment_submitted;
                $student['average'] = $average;
            }
            switch (true) {
                case ($average >= 76):
                    $grade = 'A';
                    $top_students_count++;
                    break;
                case ($average >= 60 && $average < 76):
                    $grade = 'B';
                    $avg_students_count++;
                    break;
                case ($average >= 59 && $average < 36):
                    $grade = 'C';
                    $below_avg_students_count++;
                    break;
                case ($average < 36):
                    $grade = 'D';
                    $weak_students_count++;
                    break;
            }
            // Top Students Based on Average
            $top_students[] = $student;
            //  Total Student Content read Count
            $student['read_count'] = 0;
            if ($student->content_reads) {
                $student['read_count'] = sizeof($student->content_reads);
            }
            $total_student_read_count += $student['read_count'];
        }
        // Sorting Descending by Average
        usort($top_students, function ($a, $b) {
            return $b['average'] - $a['average'];
        });

        /******** Top Classcodes */
        $top_classes = [];
        $top_teachers = [];
        $total_teachers = [];
        $total_teacher_read_count = 0;
        $upcoming_assignments = [];
        foreach ($classes as $key => $class) {
            $class['assignment_count'] = 0;
            if ($class->assignment_classcodes) {
                $class['assignment_count'] = sizeof($class->assignment_classcodes);
                $total_assignment_posted_for_classcode = $class['assignment_count'];
                $total_assignment_submitted_for_classcode = 0;
                $total_maximum_marks = 0;
                $total_scored = 0;
                // Assignment Type Overview
                $subjective_assignment_count = 0;
                $objective_assignment_count = 0;
                $document_assignment_count = 0;
                foreach ($class->assignment_classcodes as $key => $ac) {
                    $assignment = $ac->assignment;
                    $start_date = $ac->start_date;
                    $date1 = date_create(date('Y-m-d'));
                    $date2 = date_create($start_date);
                    $diff = date_diff($date1, $date2);
                    if ($diff->format("%a") > 0) {
                        $upcoming_assignments[] = $assignment;
                    }
                    switch ($assignment->assignment_type) {
                        case 'SUBJECTIVE':
                            $subjective_assignment_count++;
                            break;

                        case 'OBJECTIVE':
                            $objective_assignment_count++;
                            break;

                        case 'DOCUMENT':
                            $document_assignment_count++;
                            break;

                        default:
                            # code...
                            break;
                    }
                    $total_maximum_marks += $assignment->maximum_marks;
                    if (sizeOf($assignment->user_assignments)) {
                        foreach ($assignment->user_assignments as $key => $ua) {
                            $score = 0;
                            if ($ua->user_id == $student_id) {
                                $total_assignment_submitted_for_classcode++;
                                $score = $ua->score;
                                $total_scored += $score;
                            }
                        }
                    }
                }
            }
            $average = 0;
            if ($total_scored != 0 &&  $total_assignment_submitted_for_classcode != 0) {
                $average = $total_scored / $total_assignment_submitted_for_classcode;
            }
            $class_details = [
                'class_id' => $class['id'],
                'classcode' => $class['classcode'],
                'total_assignment_posted_for_classcode' => $total_assignment_posted_for_classcode,
                'total_assignment_submitted_for_classcode' => $total_assignment_submitted_for_classcode,
                'total_maximum_marks' => $total_maximum_marks,
                'total_scored' => $total_scored,
                'average' => $average,
                // Assignment Type Overview
                'subjective_assignment_count'      => $subjective_assignment_count,
                'objective_assignment_count'       => $objective_assignment_count,
                'document_assignment_count'        => $document_assignment_count,
            ];
            // Total of All Class Details
            $total_classes[] = $class_details;
            // Top Classcodes Based on Number of Assignment posted
            $top_classes[] = $class;

            /******** Top Teachers */
            $teachers = $class->teachers;
            foreach ($teachers as $key => $teacher) {
                $teacher['assignment_count'] = 0;
                if ($teacher->assignments) {
                    $teacher['assignment_count'] = sizeof($teacher->assignments);
                }

                $teacher['read_count'] = 0;
                if ($teacher->content_reads) {
                    $teacher['read_count'] = sizeof($teacher->content_reads);
                }
                $total_teacher_read_count += $teacher['read_count'];
                // Top Teachers Based on Number of Assignment posted
                $top_teachers[] = $teacher;
                $total_teachers[] = $teacher;
            }
        }

        // Sorting Descending by Average
        usort($top_teachers, function ($a, $b) {
            return $b['assignment_count'] - $a['assignment_count'];
        });

        // Sorting Descending by Average
        usort($total_classes, function ($a, $b) {
            return $b['average'] - $a['average'];
        });

        $top_classes = array_slice($total_classes, 0, 10);
        // Assignment Type Overview
        $subjective_assignment_count = 0;
        $objective_assignment_count = 0;
        $document_assignment_count = 0;
        foreach ($assignments as $key => $assignment) {
            switch ($assignment->assignment_type) {
                case 'SUBJECTIVE':
                    $subjective_assignment_count++;
                    break;

                case 'OBJECTIVE':
                    $objective_assignment_count++;
                    break;

                case 'DOCUMENT':
                    $document_assignment_count++;
                    break;

                default:
                    # code...
                    break;
            }
        }


        $data = [
            'teachers'      =>  $total_teachers,
            'teachersCount' =>  sizeOf($total_teachers),
            'students'      =>  $students,
            'studentsCount' => $students->count(),
            'classes'       =>  $classes,
            'classesCount'  =>  $classes->count(),
            'top_10_teachers'  =>  array_slice($top_teachers, 0, 10),  //  Top 10 Teachers
            'top_10_students'  =>  array_slice($top_students, 0, 10),  //  Top 10 Students
            'top_10_classes'   =>  $top_classes,   //  Top 10 Classes
            //Student Wise Performance 
            'final_classes' => $total_classes,
            // 'top_students_count'            => $top_students_count,
            // 'avg_students_count'            => $avg_students_count,
            // 'below_avg_students_count'      => $below_avg_students_count,
            // 'weak_students_count'           => $weak_students_count,
            // Assignment Type Overview
            'subjective_assignment_count'      => $subjective_assignment_count,
            'objective_assignment_count'       => $objective_assignment_count,
            'document_assignment_count'        => $document_assignment_count,
            // Total Content Read
            'total_teacher_read_count'      => $total_teacher_read_count,
            'total_student_read_count'      => $total_student_read_count,
            // Counts
            'total_assignment_completed' => sizeOf($assignments),
            'article_read' => $total_student_read_count,
            'video_watched' => 0,
            'assignment_pending' => 0,
            'upcoming_assignments' => $upcoming_assignments,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }

    public function contentMetadataWise(Request $request)
    {
        $request->validate([
            'company_id'    =>  'required',
            'type_id'       =>  'required'
        ]);

        $contentMetadatas = ContentMetadata::
            // where('metadata_type', '=', 'ANNOTATION')
            // ->where('company_id', '=', $request->company_id)
            // ->
            get();



        return response()->json([
            'annotationsCount'  =>  $contentMetadatas->where('metadata_type', '=', 'ANNOTATION')->count(),
            'highlightsCount'  =>  $contentMetadatas->where('metadata_type', '=', 'HIGHLIGHT')->count(),
            'dictionariesCount'  =>  $contentMetadatas->where('metadata_type', '=', 'DICTIONARY')->count()
        ]);
    }
}
