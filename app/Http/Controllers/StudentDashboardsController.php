<?php

namespace App\Http\Controllers;

use App\Classcode;
use App\Company;
use App\ContentRead;
use App\User;
use Illuminate\Http\Request;

class StudentDashboardsController extends Controller
{
    public function assignmentOverview($classcodes, $student)
    {
        // ---------------------------------------------------------------------------------------------------------
        // Controller Logic

        $myAssignments = [];
        foreach ($classcodes as $classcode) {
            if (is_array($classcode))
                $classcode = new Classcode($classcode);
            $assignments = $classcode->assignments;
            foreach ($assignments as $assignment) {
                $classAssignments = $assignment->user_assignments;
                // Class Average
                $classTotalMarks = 0;
                $totalStudentsSubmitted = $classAssignments->count();
                $singleAssignment = [];
                $isSubmitted = false;
                foreach ($classAssignments as $userAssignment) {
                    $classTotalMarks += $userAssignment->score;
                    if ($userAssignment->user_id == $student['id']) {
                        $isSubmitted = true;
                        $singleAssignment = $userAssignment;
                        $singleAssignment['percent'] = $assignment->maximum_marks  == 0 ?  0 : $singleAssignment['score'] * 100 / $assignment->maximum_marks;
                    }
                }
                $classAverage = $totalStudentsSubmitted == 0 ? 0 : $classTotalMarks / $totalStudentsSubmitted;
                $singleAssignment['isSubmitted'] = $isSubmitted;
                $singleAssignment['classAverage'] = $classAverage;
                $singleAssignment['classAveragePercent'] = $assignment->maximum_marks  == 0 ?  0 : $classAverage * 100 / $assignment->maximum_marks;
                if (!$isSubmitted) {
                    $singleAssignment['score'] = 0;
                    $singleAssignment['percent'] = 0;
                }
                // End Class Average
                // Assignment Status
                $currentDate = date_create(date('Y-m-d'));
                $assignmentClasscode = $assignment->my_assignment_classcodes()
                    ->where('classcode_id', '=', $classcode->id)
                    ->first();
                $startDate = date_create($assignmentClasscode->start_date);
                $endDate = date_create($assignmentClasscode->end_date);
                $startDiff = date_diff($currentDate, $startDate)->format("%R%a");
                $endDiff = date_diff($currentDate, $endDate)->format("%R%a");
                $isUpcoming = $startDiff > 0 ? true : false;
                $isDue = $endDiff < 0 ? true : false;
                $inProgress = $startDiff <= 0 && $endDiff >= 0 ? true : false;
                if ($isUpcoming) $singleAssignment['status'] = 'UPCOMING';
                else if ($isDue && !$isSubmitted) $singleAssignment['status'] = 'OVERDUE';
                else if ($inProgress && !$isSubmitted) $singleAssignment['status'] = 'IN PROGRESS';
                else $singleAssignment['status'] = 'COMPLETED';
                // End Assignmet Status
                $singleAssignment['classcode'] = $classcode->classcode;
                $singleAssignment['assignment_type'] = $assignment->assignment_type;
                $singleAssignment['maximum_marks'] = $assignment->maximum_marks;
                $singleAssignment['assignment_title'] = $assignment->assignment_title;
                $singleAssignment['my_results'] = $assignment->my_results($student['id'])->get();
                $singleAssignment['assignment_id'] = $assignment->id;
                $singleAssignment['assignment_created_date'] = '';
                $singleAssignment['teachers'] = $classcode->teachers;
                $singleAssignment['student'] = $student;
                $singleAssignment['created_at'] = $assignment->toArray()['created_at'];

                // Assignment Classcode End Date
                $assignmentClasscode = $assignment->assignment_classcodes()
                    ->where('classcode_id', '=', $classcode->id)
                    ->first();
                if ($assignmentClasscode)
                    $singleAssignment['end_date'] = $assignmentClasscode->end_date;
                // Assignment Classcode End Date
                $myAssignments[] = $singleAssignment;
            }
        }

        return $myAssignments;
    }

    public function StudentWiseOverview(Request $request)
    {
        $request->validate([
            'companyId'    =>  'required',
            'studentId'    =>  'required',
        ]);

        $studentId  = request()->studentId;
        $student    = User::where('users.id', '=', $studentId)
            ->first();
        $classcodes = $student->classcodes;

        // ---------------------------------------------------------------------------------------------------------
        // Assignment Overwiew
        $myAssignments = $this->assignmentOverview($classcodes, $student->toArray());

        // ---------------------------------------------------------------------------------------------------------
        // View Logic
        $assignmentOverview = [
            'totalAssignmentsCount' =>  sizeof($myAssignments),
            "statusWiseAssignments"  =>  [],
            "typeWiseAssignments"   =>  [],
            "classcodeWiseAssignments"  =>  [],
        ];
        $inprogress = [
            'name'  =>  'IN PROGRESS',
            'count' =>  0,
            'values'    =>  []
        ];
        
        $overdue = [
            'name'  =>  'OVERDUE',
            'count' =>  0,
            'values'    =>  []
        ];
        $completed = [
            'name'  =>  'COMPLETED',
            'count' =>  0,
            'values'    =>  []
        ];
        $upcoming = [
            'name'  =>  'UPCOMING',
            'count' =>  0,
            'values'    =>  []
        ];
        $subjective = [
            'name'  =>  'SUBJECTIVE',
            'count' =>  0,
            'values'    =>  []
        ];
        $objective = [
            'name'  =>  'OBJECTIVE',
            'count' =>  0,
            'values'    =>  []
        ];
        $document = [
            'name'  =>  'DOCUMENT',
            'count' =>  0,
            'values'    =>  []
        ];
        $classcodeWise = [];
        foreach ($myAssignments as $singleAssignment) {
            // Status Wise Bifurcation
            switch ($singleAssignment['status']) {
                case 'UPCOMING':
                    $upcoming['count']++;
                    $upcoming['values'][]   =   $singleAssignment;
                    break;
                case 'OVERDUE':
                    $overdue['count']++;
                    $overdue['values'][]   =   $singleAssignment;
                    break;
                case 'IN PROGRESS':
                    $inprogress['count']++;
                    $inprogress['values'][]   =   $singleAssignment;
                    break;
                case 'COMPLETED':
                    $completed['count']++;
                    $completed['values'][]   =   $singleAssignment;
                    break;
                default:
                    break;
            }
            // End Status Wise Bifurcation/

            // Type Wise Bifurcation
            switch ($singleAssignment['assignment_type']) {
                case 'SUBJECTIVE':
                    $subjective['count']++;
                    $subjective['values'][]   =   $singleAssignment;
                    break;
                case 'OBJECTIVE':
                    $objective['count']++;
                    $objective['values'][]   =   $singleAssignment;
                    break;
                case 'DOCUMENT':
                    $document['count']++;
                    $document['values'][]   =   $singleAssignment;
                    break;
                default:
                    break;
            }
            // End Type Wise Bifurcation/

            // Classcode Wise Bifurcation
            $contentKey = array_search($singleAssignment['classcode'], array_column($classcodeWise, 'name'));
            if ($contentKey != null || $contentKey !== false) {
                $classcodeWise[$contentKey]['count']++;
                $count  = $classcodeWise[$contentKey]['count'];
                // My Average
                $myAverage = $classcodeWise[$contentKey]['myAverage'];
                $myAverage = (($myAverage * ($count - 1)) + $singleAssignment['score'])  / $count;
                $classcodeWise[$contentKey]['myAverage'] =  $myAverage;
                // Classcode Average
                $classcodeAverage = $classcodeWise[$contentKey]['classcodeAverage'];
                $classcodeAverage = (($classcodeAverage * ($count - 1)) + $singleAssignment['classAverage'])  / $count;
                $classcodeWise[$contentKey]['classcodeAverage'] =  $classcodeAverage;
                $classcodeWise[$contentKey]['values'][] = $singleAssignment;
            } else {
                $classcodeWise[] = [
                    'name'  =>  $singleAssignment['classcode'],
                    'count' =>  1,
                    'teachers' =>  $singleAssignment['teachers'],
                    'myAverage' =>  $singleAssignment['score'],
                    // 'myAveragePercent' =>  $singleAssignment['score'] * 100 / $singleAssignment['maximum_marks'],
                    'classcodeAverage' =>  $singleAssignment['classAverage'],
                    // 'classcodeAveragePercent' =>  $singleAssignment['classAverage'] * 100 / $singleAssignment['maximum_marks'],
                    'values' => [$singleAssignment],
                ];
            }
            // End Classcode Wise Bifurcation
        }
        $assignmentOverview['statusWiseAssignments'] = [$upcoming, $overdue, $inprogress, $completed];
        $assignmentOverview['typeWiseAssignments'] = [$subjective, $objective, $document];
        $assignmentOverview['classcodeWiseAssignments'] = $classcodeWise;

        // ---------------------------------------------------------------------------------------------------------
        // Metadata Overwiew
        $metadataOverview = [];
        $annotations = [
            'name'  =>  'ANNOTATION',
            'count' =>  0,
            'values' =>  []
        ];
        $highlights = [
            'name'  =>  'HIGHLIGHT',
            'count' =>  0,
            'values' =>  []
        ];
        $dictionaries = [
            'name'  =>  'DICTIONARY',
            'count' =>  0,
            'values' =>  []
        ];

        // Metadatas created by the student
        $studentMetadatas = $student->content_metadatas;
        $contentMetadatas = $studentMetadatas;

        foreach ($classcodes as $classcode) {
            $classContentMetadatas = $classcode->content_metadatas;
            $contentMetadatas = [...$contentMetadatas, ...$classContentMetadatas];

            foreach ($contentMetadatas as $contentMetadata) {
                if ($contentMetadata->user_id == $studentId || $contentMetadata->user->roles[0]->id == 3) {
                    $contentMetadata['postedBy'] = $contentMetadata->user_id == $studentId ? 'Student' : 'Teacher';
                    switch ($contentMetadata->metadata_type) {
                        case 'ANNOTATION':
                            $annotations['count']++;
                            $annotations['values'][]  =   $contentMetadata;
                            break;
                        case 'HIGHLIGHT':
                            $highlights['count']++;
                            $highlights['values'][]  =   $contentMetadata;
                            break;
                        case 'DICTIONARY':
                            $dictionaries['count']++;
                            $dictionaries['values'][]  =   $contentMetadata;
                            break;
                        default:
                            break;
                    }
                }
            }
            $contentMetadatas  = [];
        }
        $metadataOverview = [$annotations, $highlights, $dictionaries];

        // ---------------------------------------------------------------------------------------------------------
        // Content Wise Overwiew

        $contentWiseOverview = [];
        $articles = [
            'name'  =>  'ARTICLE',
            'count' =>  0,
            'values'    =>  []
        ];
        $infographics = [
            'name'  =>  'INFOGRAPHIC',
            'count' =>  0,
            'values'    =>  []
        ];
        $videos = [
            'name'  =>  'VIDEO',
            'count' =>  0,
            'values'    =>  []
        ];

        $contentReads = $student->content_reads;
        foreach ($contentReads as $contentRead) {
            $contentRead->content['content_metadatas'] = $contentRead->content->content_metadatas()
                // ->where('user_id', '=', $student['id'])
                ->get();
            switch ($contentRead->content->content_type) {
                case 'ARTICLE':
                    $articles['count']++;
                    $articles['values'][] = $contentRead;
                    break;
                case 'INFOGRAPHIC':
                    $infographics['count']++;
                    $infographics['values'][] = $contentRead;
                    break;
                case 'VIDEO':
                    $videos['count']++;
                    $videos['values'][] = $contentRead;
                    break;
                default:
                    break;
            }
        }
        $contentWiseOverview = [$articles, $infographics, $videos];

        return response()->json([
            'assignmentOverview' =>  $assignmentOverview,
            'metadataOverview' =>  $metadataOverview,
            'contentWiseOverview'   =>  $contentWiseOverview,
        ]);
    }

    public function singleAssignmentOverview($classcode, $student, $assignment)
    {
        // ---------------------------------------------------------------------------------------------------------
        // Controller Logic

        $myAssignments = [];
        if (is_array($classcode))
            $classcode = new Classcode($classcode);
        $classAssignments = $assignment->user_assignments;
        // Class Average
        $classTotalMarks = 0;
        $totalStudentsSubmitted = $classAssignments->count();
        $singleAssignment = [];
        $isSubmitted = false;
        foreach ($classAssignments as $userAssignment) {
            $classTotalMarks += $userAssignment->score;
            if ($userAssignment->user_id == $student['id']) {
                $isSubmitted = true;
                $singleAssignment = $userAssignment;
                $singleAssignment['percent'] =  $singleAssignment['score'] * 100 / $assignment->maximum_marks;
            }
        }
        $classAverage = $totalStudentsSubmitted == 0 ? 0 : $classTotalMarks / $totalStudentsSubmitted;
        $singleAssignment['isSubmitted'] = $isSubmitted;
        $singleAssignment['classAverage'] = $classAverage;
        $singleAssignment['classAveragePercent'] = $classAverage * 100 / $assignment->maximum_marks;
        if (!$isSubmitted) {
            $singleAssignment['score'] = 0;
            $singleAssignment['percent'] = 0;
        }
        // End Class Average
        // Assignment Status
        $currentDate = date_create(date('Y-m-d'));
        $assignmentClasscode = $assignment->my_assignment_classcodes()
            ->where('classcode_id', '=', $classcode->id)
            ->first();
        $startDate = date_create($assignmentClasscode->start_date);
        $endDate = date_create($assignmentClasscode->end_date);
        $startDiff = date_diff($currentDate, $startDate)->format("%R%a");
        $endDiff = date_diff($currentDate, $endDate)->format("%R%a");
        $isUpcoming = $startDiff > 0 ? true : false;
        $isDue = $endDiff < 0 ? true : false;
        $inProgress = $startDiff < 0 && $endDiff >= 0 ? true : false;
        if ($isUpcoming) $singleAssignment['status'] = 'UPCOMING';
        else if ($isDue && !$isSubmitted) $singleAssignment['status'] = 'OVERDUE';
        else if ($inProgress && !$isSubmitted) $singleAssignment['status'] = 'IN PROGRESS';
        else $singleAssignment['status'] = 'COMPLETED';
        // End Assignmet Status
        $singleAssignment['classcode'] = $classcode->classcode;
        $singleAssignment['assignment_type'] = $assignment->assignment_type;
        $singleAssignment['maximum_marks'] = $assignment->maximum_marks;
        $singleAssignment['assignment_title'] = $assignment->assignment_title;
        $singleAssignment['my_results'] = $assignment->my_results($student['id'])->get();
        $singleAssignment['assignment_id'] = $assignment->id;
        $singleAssignment['assignment_created_date'] = '';
        $singleAssignment['teachers'] = $classcode->teachers;
        $singleAssignment['student'] = $student;
        $singleAssignment['created_at'] = $assignment->toArray()['created_at'];

        // Assignment Classcode End Date
        $assignmentClasscode = $assignment->assignment_classcodes()
            ->where('classcode_id', '=', $classcode->id)
            ->first();
        if ($assignmentClasscode)
            $singleAssignment['end_date'] = $assignmentClasscode->end_date;
        // Assignment Classcode End Date
        $myAssignments[] = $singleAssignment;

        return $myAssignments;
    }
}
