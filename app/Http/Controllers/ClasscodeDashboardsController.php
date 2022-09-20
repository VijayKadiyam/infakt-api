<?php

namespace App\Http\Controllers;

use App\Company;
use App\ContentMetadata;
use App\UserClasscode;
use Illuminate\Http\Request;

class ClasscodeDashboardsController extends Controller
{
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
        $subjective_assignments = [];
        $objective_assignments = [];
        $document_assignments = [];
        foreach ($assignments as $key => $assignment) {
            switch ($assignment->assignment_type) {
                case 'SUBJECTIVE':
                    $subjective_assignments[] = $assignment;
                    break;

                case 'OBJECTIVE':
                    $objective_assignments[] = $assignment;
                    break;

                case 'DOCUMENT':
                    $document_assignments[] = $assignment;
                    break;

                default:
                    # code...
                    break;
            }
        }

        $assignment_type_overview = [
            [
                'name' => 'SUBJECTIVE',
                'count' => sizeof($subjective_assignments),
                'values' => $subjective_assignments,
            ],
            [
                'name' => 'OBJECTIVE',
                'count' => sizeof($objective_assignments),
                'values' => $objective_assignments,
            ],
            [
                'name' => 'DOCUMENT',
                'count' => sizeof($document_assignments),
                'values' => $document_assignments,
            ],
            [
                'name' => 'TOTAL',
                'count' => sizeof($assignments),
                'values' => $assignments,
            ],
        ];


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
            'assignment_type_overview'      => $assignment_type_overview,
            // Total Content Read
            'total_teacher_read_count'      => $total_teacher_read_count,
            'total_student_read_count'      => $total_student_read_count,
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
        $contentMetadatas = [];
        $annotation_content_metadatas = [];
        $highlight_content_metadatas = [];
        $dictionary_content_metadatas = [];

        $user_classcodes = UserClasscode::where('user_id', request()->user_id)->get();
        foreach ($user_classcodes as $key => $uc) {
            $classcode_id = $uc->classcode_id;

            $annotation = ContentMetadata::where('metadata_type', '=', 'ANNOTATION')
                ->with('content')
                ->whereHas('content_metadata_classcodes', function ($q) use ($classcode_id) {
                    $q->where('classcode_id', '=', $classcode_id);
                })
                ->get();
            if (sizeOf($annotation)) {
                $annotation_content_metadatas[] = $annotation;
            }

            $highlight = ContentMetadata::where('metadata_type', '=', 'HIGHLIGHT')
                ->with('content')
                ->whereHas('content_metadata_classcodes', function ($q) use ($classcode_id) {
                    $q->where('classcode_id', '=', $classcode_id);
                })
                ->get();
            if (sizeOf($highlight)) {
                $highlight_content_metadatas[] = $highlight;
            }

            $dictionary = ContentMetadata::where('metadata_type', '=', 'DICTIONARY')
                ->with('content')
                ->whereHas('content_metadata_classcodes', function ($q) use ($classcode_id) {
                    $q->where('classcode_id', '=', $classcode_id);
                })
                ->get();
            if (sizeOf($dictionary)) {
                $dictionary_content_metadatas[] = $dictionary;
            }
        }
        $total_content_metadatas = [
            [
                'name' => 'ANNOTATION',
                'count' => sizeOF($annotation_content_metadatas),
                'values' => $annotation_content_metadatas,
            ],
            [
                'name' => 'HIGHLIGHT',
                'count' => sizeOF($highlight_content_metadatas),
                'values' => $highlight_content_metadatas,
            ],
            [
                'name' => 'DICTIONARY',
                'count' => sizeOF($dictionary_content_metadatas),
                'values' => $dictionary_content_metadatas,
            ],
        ];

        return response()->json([
            'total_content_metadatas'  => $total_content_metadatas
        ]);
    }
}
