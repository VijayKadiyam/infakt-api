<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;

class StudentDashboardsController extends Controller
{
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
        $annotations = [];
        $highlights = [];
        $dictionaries = [];
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
                $student_content_read = $student->content_reads;
                $article_contents = [];
                $infographic_contents = [];
                $video_contents = [];
                foreach ($student_content_read as $key => $scr) {
                    $content = $scr->content;
                    switch ($content->content_type) {
                        case 'ARTICLE':
                            $article_contents[] = $content;
                            break;
                        case 'INFOGRAPHIC':
                            $infographic_contents[] = $content;
                            break;
                        case 'VIDEO':
                            $video_contents[] = $content;
                            break;

                        default:
                            break;
                    }
                }
                $student['read_count'] = sizeof($student->content_reads);
            }
            $content_types = [
                [
                    'name' => "ARTICLE",
                    'count' => sizeof($article_contents),
                    'values' => $article_contents
                ],
                [
                    'name' => "INFOGRAPHIC",
                    'count' => sizeof($infographic_contents),
                    'values' => $infographic_contents
                ],
                [
                    'name' => "VIDEO",
                    'count' => sizeof($video_contents),
                    'values' => $video_contents
                ]
            ];
            $student['content_types'] = $content_types;
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
        $overdued_assignments = [];
        $pending_assignments = [];
        $completed_assignments = [];
        $total_assigments = [];
        foreach ($classes as $key => $class) {
            $classcode_id = $class->id;
            $class_total_assigments = [];
            $class_upcoming_assignments = [];
            $class_overdued_assignments = [];
            $class_pending_assignments = [];
            $class_completed_assignments = [];
            $class['assignment_count'] = 0;
            if ($class->assignment_classcodes) {
                $class['assignment_count'] = sizeof($class->assignment_classcodes);
                $total_assignment_submitted_for_classcode = 0;
                $class_total_assignment_submitted = 0;
                $total_maximum_marks = 0;
                $total_scored = 0;
                $class_total_scored = 0;
                $class_subjective_assignments = [];
                $class_objective_assignments = [];
                $class_document_assignments = [];
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
                        $upcoming_assignments[] = $assignment;
                        $class_upcoming_assignments[] = $assignment;
                    }

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
                    $total_maximum_marks += $assignment->maximum_marks;
                    if (sizeOf($assignment->user_assignments)) {
                        $is_submitted = false;
                        foreach ($assignment->user_assignments as $key => $ua) {
                            $score = 0;
                            if ($ua->user_id == $student_id) {
                                // Student Submitted
                                $is_submitted = true;
                                $total_assignment_submitted_for_classcode++;
                                $score = $ua->score;
                                $total_scored += $score;
                                $assignment['my_submission']    =   $ua;
                                $completed_assignments[] = $assignment;
                                $class_completed_assignments[] = $assignment;
                            }
                            // else {
                            $class_total_scored += $ua->score;
                            $class_total_assignment_submitted++;
                            // }
                        }
                        if ($is_ongoing == true && $is_submitted == false) {
                            // Student Hadn't submitted the Ongoing Assignment hence Add to Pending Assignment
                            $pending_assignments[] = $assignment;
                            $class_pending_assignments[] = $assignment;
                        }
                        if ($is_Due == true && $is_submitted == false) {
                            // Student Hadn't submitted the Assignment And end date has been past hence Add to Overdue Assignment
                            $overdued_assignments[] = $assignment;
                            $class_overdued_assignments[] = $assignment;
                        }
                    }
                }
                $class_total_assigments[] = $assignment;
                $total_assigments[] = $assignment;
            }
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
            $totalAverage = 0;
            if ($total_maximum_marks != 0 && $class_total_assignment_submitted != 0) {
                $totalAverage = $total_maximum_marks / $class_total_assignment_submitted;
            }

            $average = 0;
            if ($total_scored != 0 &&  $total_assignment_submitted_for_classcode != 0) {
                $average = $total_scored / $total_assignment_submitted_for_classcode;
            }
            $class_average = 0;
            if ($class_total_scored != 0 &&  $class_total_assignment_submitted != 0) {
                $class_average = $class_total_scored / $class_total_assignment_submitted;
            }

            /******** Class Meta data type Overview */
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

            $student_assignment_overview = [
                [
                    'name' => "UPCOMING",
                    'count' => sizeof($class_upcoming_assignments),
                    'values' => $class_upcoming_assignments,
                ],
                [
                    'name' => "OVERDUE",
                    'count' => sizeof($class_overdued_assignments),
                    'values' => $class_overdued_assignments,
                ],
                [
                    'name' => "PENDING",
                    'count' => sizeof($class_pending_assignments),
                    'values' => $class_pending_assignments,
                ],
                [
                    'name' => "COMPLETED",
                    'count' => sizeof($class_completed_assignments),
                    'values' => $class_completed_assignments,
                ],
            ];
            $class_details = [
                'class_id'                                 => $class['id'],
                'classcode'                                => $class['classcode'],
                'total_assignment_posted_for_classcode'    => sizeof($class_total_assigments),
                'total_assignment_submitted_for_classcode' => $total_assignment_submitted_for_classcode,
                'total_maximum_marks'                      => $total_maximum_marks,
                'total_scored'                             => $total_scored,
                'average'                                  => $average,
                'class_average'                            => $class_average,
                'total_average'                             =>  $totalAverage,
                'assignments'                              => $class_total_assigments,
                // Student Wise_
                'student_assignment_overview'              => $student_assignment_overview,
                'class_metadata_type_overview'             => $class_metadata_type_overview,
                // Assignment Type Overview
                'class_assignment_type_overview'           => $class_assignment_type_overview
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

        // Assignment Overview
        $assignment_overview = [
            [
                'name' => "UPCOMING",
                'count' => sizeof($upcoming_assignments),
                'values' => $upcoming_assignments,
            ],
            [
                'name' => "OVERDUE",
                'count' => sizeof($overdued_assignments),
                'values' => $overdued_assignments,
            ],
            [
                'name' => "PENDING",
                'count' => sizeof($pending_assignments),
                'values' => $pending_assignments,
            ],
            [
                'name' => "COMPLETED",
                'count' => sizeof($completed_assignments),
                'values' => $completed_assignments,
            ],
        ];

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
            // Assignment Type Overview
            'assignment_type_overview' => $assignment_type_overview,
            // Total Content Read
            'total_teacher_read_count'      => $total_teacher_read_count,
            'total_student_read_count'      => $total_student_read_count,
            // Counts
            'total_assignment_completed' => sizeOf($assignments),
            'article_read' => $total_student_read_count,
            'video_watched' => 0,
            'assignment_pending' => 0,
            'assignment_overview' => $assignment_overview,
            'metadata_type_overview' => $metadata_type_overview,

        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }
}
