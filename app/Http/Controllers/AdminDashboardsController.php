<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Classcode;
use App\Company;
use App\ContentMetadata;
use App\User;
use App\UserAssignment;
use Illuminate\Http\Request;

class AdminDashboardsController extends Controller
{
    public function adminDashboard(Request $request)
    {
        if ($request->company_id) {
            $company     = Company::find(request()->company_id);
            $students =  $company->students()->get();
            $teachers =  $company->teachers()->get();
            $classes = $company->classcodes()->get();
            $assignments = $company->assignments()->where('is_deleted', false)->get();
            $annotations = $company->annotations()->get();
            $highlights = $company->highlights()->get();
            $dictionaries = $company->dictionaries()->get();
            $assignmentPosts = $company->user_assignments()->get();
        } else {
            $teachers =  User::where('is_deleted', false)
                ->whereHas('roles', function ($q) {
                    $q->where('name', '=', 'TEACHER');
                })->get();
            $students =  User::where('is_deleted', false)
                ->whereHas('roles', function ($q) {
                    $q->where('name', '=', 'STUDENT');
                })->get();
            $classes = Classcode::where('is_deleted', false)->get();
            $assignments = Assignment::where('is_deleted', false)->get();
            $annotations = ContentMetadata::where('metadata_type', 'ANNOTATION')->get();
            $highlights = ContentMetadata::where('metadata_type', 'HIGHLIGHT')->get();
            $dictionaries = ContentMetadata::where('metadata_type', 'DICTIONARY')->get();
            $assignmentPosts = UserAssignment::where('is_deleted', false)->get();
        }

        // total Content Read By Teacher 
        $total_teacher_read_count = 0;
        $total_teacher_reads = [];
        $total_contents = [];
        foreach ($teachers as $key => $teacher) {
            $teacher['read_count'] = 0;
            if ($teacher->content_reads) {
                $teacher['read_count'] = sizeof($teacher->content_reads);
                foreach ($teacher->content_reads as $key => $cr) {
                    $content_id = $cr->content_id;
                    $content = $cr->content;
                    $count = 1;
                    $content_key = array_search($content_id, array_column($total_teacher_reads, 'id'));
                    if ($content_key != null || $content_key !== false) {
                        // Increase read Count 
                        $total_teacher_reads[$content_key]['count']++;
                    } else {
                        // Read Not Added
                        $read_detail = [
                            'id' => $content_id,
                            'content' => $content,
                            'count' => $count,
                        ];
                        $total_teacher_reads[] = $read_detail;
                    }
                    // Total Content
                    $c_key = array_search($content_id, array_column($total_contents, 'id'));
                    if ($c_key != null || $c_key !== false) {
                        // Increase Read Count
                        $total_contents[$c_key]['count']++;
                    } else {
                        $Content_read_detail = [
                            'id' => $content_id,
                            'content' => $content,
                            'count' => $count,
                        ];
                        $total_contents[] = $Content_read_detail;
                    }
                }
            }
            $total_teacher_read_count += $teacher['read_count'];
        }
        // total Content Read By Student 
        $total_student_read_count = 0;
        $total_student_reads = [];
        foreach ($students as $key => $student) {
            $student['read_count'] = 0;
            if ($student->content_reads) {
                $student['read_count'] = sizeof($student->content_reads);
                foreach ($student->content_reads as $key => $cr) {
                    $content_id = $cr->content_id;
                    $content = $cr->content;
                    $count = 1;
                    $content_key = array_search($content_id, array_column($total_student_reads, 'id'));
                    if ($content_key != null || $content_key !== false) {
                        // Increase read Count 
                        $total_student_reads[$content_key]['count']++;
                    } else {
                        // Read Not Added
                        $read_detail = [
                            'id' => $content_id,
                            'content' => $content,
                            'count' => $count,
                        ];
                        $total_student_reads[] = $read_detail;
                    }
                    // Total Content
                    $c_key = array_search($content_id, array_column($total_contents, 'id'));
                    if ($c_key != null || $c_key !== false) {
                        // Increase Read Count
                        $total_contents[$c_key]['count']++;
                    } else {
                        $Content_read_detail = [
                            'id' => $content_id,
                            'content' => $content,
                            'count' => $count,
                        ];
                        $total_contents[] = $Content_read_detail;
                    }
                }
            }
            $total_student_read_count += $student['read_count'];
        }

        // Sorting Descending by Count
        usort($total_contents, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        $top_10_content_read = array_slice($total_contents, 0, 10);

        $data = [
            'total_studentsCount'            =>  sizeof($students),
            'students'                       =>  $students,
            'teachersCount'                  =>  sizeof($teachers),
            'total_teachers'                 =>  $teachers,
            'classesCount'                   =>  sizeof($classes),
            'total_classes'                  =>  $classes,
            'assignmentsCount'               =>  sizeof($assignments),
            'total_assignments'              =>  $assignments,
            'annotationsCount'               =>  sizeof($annotations),
            'total_annotations'              =>  $annotations,
            'highlightsCount'                =>  sizeof($highlights),
            'total_highlights'               =>  $highlights,
            'dictionariesCount'              =>  sizeof($dictionaries),
            'total_dictionaries'             =>  $dictionaries,
            'total_teacher_read_count'       =>  $total_teacher_read_count,
            'teacher_article_read_overview'  =>  $total_teacher_reads,
            'total_student_read_count'       =>  $total_student_read_count,
            'student_article_read_overview'  =>  $total_student_reads,
            'overall_top_10_content_read'    =>  $top_10_content_read,
            'assignmentPostsCount'           =>  sizeof($assignmentPosts),
            'total_assignment_posts'         =>  $assignmentPosts,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }
}
