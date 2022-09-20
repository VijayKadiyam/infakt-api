<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;

class TeacherDashboardsController extends Controller
{
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
        $total_assigments = [];
        $annotations = [];
        $highlights = [];
        $dictionaries = [];
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

            /******* Class wise Assignment Overview */
            $class_upcoming_assignments = [];
            $class_overdued_assignments = [];
            $class_ongoing_assignments = [];
            $class_subjective_assignments = [];
            $class_objective_assignments = [];
            $class_document_assignments = [];
            $class_total_assigments = [];
            if ($class->assignment_classcodes) {
                foreach ($class->assignment_classcodes as $key => $ac) {
                    $assignment = $ac->assignment;
                    $start_date = $ac->start_date;
                    $end_date = $ac->end_date;
                    $date1 = date_create(date('Y-m-d'));
                    // Comparing Current Date with Starting Date
                    $date2 = date_create($start_date);
                    $start_diff = date_diff($date1, $date2)->format("%R%a");
                    $is_Upcoming = $start_diff > 0 ? true : false;
                    // Comparing Current Date with Ending Date
                    $date2 = date_create($end_date);
                    $end_diff = date_diff($date1, $date2)->format("%R%a");
                    $is_Due = $end_diff < 0 ? true : false;
                    // return "AC id" . $ac->id . " Current Date=" . date('Y-m-d') . ' Start Date=' . $start_date . ' End Date= ' . $end_date . ' Start Diff=' . $start_diff . ' End Diff=' . $end_diff;
                    $is_ongoing = $start_diff < 0 && $end_diff > 0 ? true : false;
                    if ($is_Upcoming == true) {
                        // Assignment not Started Yet
                        $class_upcoming_assignments[] = $assignment;
                    }
                    if ($is_ongoing == true) {
                        // Assignment Already Started
                        $class_ongoing_assignments[] = $assignment;
                    }
                    if ($is_Due == true) {
                        // Assignment Already Dued
                        $class_overdued_assignments[] = $assignment;
                    }

                    // Assignment Type Overview

                    switch ($assignment->assignment_type) {
                        case 'SUBJECTIVE':
                            $class_subjective_assignments[] = $assignment;
                            break;

                        case 'OBJECTIVE':
                            $class_objective_assignments[] = $assignment;
                            break;

                        case 'DOCUMENT':
                            $class_document_assignments[] = $assignment;
                            break;

                        default:
                            # code...
                            break;
                    }
                    $class_total_assigments[] = $assignment;
                    $total_assigments[] = $assignment;
                }
            }
            $class_assignment_overview = [
                [
                    'name' => "UPCOMING",
                    'count' => sizeof($class_upcoming_assignments),
                    'values' => $class_upcoming_assignments,
                ],
                [
                    'name' => "ONGOING",
                    'count' => sizeof($class_ongoing_assignments),
                    'values' => $class_ongoing_assignments,
                ],
                [
                    'name' => "OVERDUE",
                    'count' => sizeof($class_overdued_assignments),
                    'values' => $class_overdued_assignments,
                ],

            ];

            $class_assignment_type_overview = [
                [
                    'name' => 'SUBJECTIVE',
                    'count' => sizeof($class_subjective_assignments),
                    'values' => $class_subjective_assignments,
                ],
                [
                    'name' => 'OBJECTIVE',
                    'count' => sizeof($class_objective_assignments),
                    'values' => $class_objective_assignments,
                ],
                [
                    'name' => 'DOCUMENT',
                    'count' => sizeof($class_document_assignments),
                    'values' => $class_document_assignments,
                ],
                [
                    'name' => 'TOTAL',
                    'count' => sizeof($class_total_assigments),
                    'values' => $class_total_assigments,
                ],
            ];
            $class['class_assignment_overview'] = $class_assignment_overview;
            $class['class_assignment_type_overview'] = $class_assignment_type_overview;

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


            $class_annotations = $class->annotations;
            $class_highlights = $class->highlights;
            $class_dictionaries = $class->dictionaries;

            $class_metadata_type_overview = [
                [
                    'name' => 'ANNOTATION',
                    'count' => sizeOf($class_annotations),
                    'values' => $class_annotations,
                ],
                [
                    'name' => 'HIGHLIGHT',
                    'count' => sizeOf($class_highlights),
                    'values' => $class_highlights,
                ],
                [
                    'name' => 'DICTIONARY',
                    'count' => sizeOf($class_dictionaries),
                    'values' => $class_dictionaries,
                ],
            ];

            array_push($annotations, ...$class_annotations);
            array_push($highlights, ...$class_highlights);
            array_push($dictionaries, ...$class_dictionaries);

            $class['class_metadata_type_overview'] = $class_metadata_type_overview;
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

        // Metadata Type Overview
        $metadata_type_overview = [
            [
                'name' => 'ANNOTATION',
                'count' => sizeOf($annotations),
                'values' => $annotations,
            ],
            [
                'name' => 'HIGHLIGHT',
                'count' => sizeOf($highlights),
                'values' => $highlights,
            ],
            [
                'name' => 'DICTIONARY',
                'count' => sizeOf($dictionaries),
                'values' => $dictionaries,
            ],
        ];

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
            'class_assignment_type_overview'      => $class_assignment_type_overview,
            // Total Content Read
            'total_teacher_read_count'      => $total_teacher_read_count,
            'total_student_read_count'      => $total_student_read_count,
            'class_assignment_overview'     => $class_assignment_overview,
            'metadata_type_overview'        => $metadata_type_overview

        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }
}
