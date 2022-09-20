<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;

class StudentDashboardsController extends Controller
{
    public function StudentWiseOverview(Request $request)
    {
        $request->validate([
            'companyId'    =>  'required',
            'studentId'    =>  'required',
        ]);

        $studentId = request()->studentId;
        $company     = Company::find(request()->companyId);
        $student    = $company->allUsers()
            ->where('is_deleted', false)
            ->with('classcodes')
            ->where('users.id', $studentId)
            ->whereHas('roles', function ($q) {
                $q->where('name', '=', 'STUDENT');
            })->first();
        $classcodes = $student->classcodes;
        $myAssignments = [];
        foreach ($classcodes as $classcode) {
            $assignments = $classcode->assignments;
            foreach ($assignments as $assignment) {
                $classAssignments = $assignment->user_assignments;
                // Class Average
                $classTotalMarks = 0;
                $totalStudentsSubmitted = $classAssignments->count();
                $myAssignment = [];
                $isSubmitted = false;
                foreach ($classAssignments as $userAssignment) {
                    $classTotalMarks += $userAssignment->score;
                    if ($userAssignment->user_id == $studentId) {
                        $isSubmitted = true;
                        $myAssignment = $userAssignment;
                    }
                }
                $classAverage = $totalStudentsSubmitted == 0 ? 0 : $classTotalMarks / $totalStudentsSubmitted;
                $myAssignment['classAverage'] = $classAverage;
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
                $inProgress = $startDiff < 0 && $endDiff > 0 ? true : false;
                if ($isUpcoming) $myAssignment['status'] = 'UPCOMING';
                else if ($isDue && !$isSubmitted) $myAssignment['status'] = 'OVERDUE';
                else if ($inProgress && !$isSubmitted) $myAssignment['status'] = 'IN PROGRESS';
                else $myAssignment['status'] = 'COMPLETED';
                // End Assignmet Status
                $myAssignment['classcode'] = $classcode->classcode;
                $myAssignment['assignment_type'] = $assignment->assignment_type;
                $myAssignment['maximum_marks'] = $assignment->maximum_marks;
                $myAssignment['assignment_title'] = $assignment->assignment_title;
                $myAssignment['assignment_created_date'] = '';

                $myAssignments[] = $myAssignment;
            }
        }

        $assignmentOverview = [
            'totalAssignmentsCount' =>  sizeof($myAssignments),
            "statusWiseAssignments"  =>  [],
            "typeWiseAssignments"   =>  [],
            "classcodeWiseAssignments"  =>  [],
        ];
        $upcoming = [
            'name'  =>  'UPCOMING',
            'count' =>  0,
            'values'    =>  []
        ];
        $overdue = [
            'name'  =>  'OVERDUE',
            'count' =>  0,
            'values'    =>  []
        ];
        $inprogress = [
            'name'  =>  'IN PROGRESS',
            'count' =>  0,
            'values'    =>  []
        ];
        $completed = [
            'name'  =>  'COMPLETED',
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
        foreach ($myAssignments as $myAssignment) {
            // Status Wise Bifurcation
            switch ($myAssignment['status']) {
                case 'UPCOMING':
                    $upcoming['count']++;
                    $upcoming['values'][]   =   $myAssignment;
                    break;
                case 'OVERDUE':
                    $overdue['count']++;
                    $overdue['values'][]   =   $myAssignment;
                    break;
                case 'IN PROGRESS':
                    $inprogress['count']++;
                    $inprogress['values'][]   =   $myAssignment;
                    break;
                case 'COMPLETED':
                    $completed['count']++;
                    $completed['values'][]   =   $myAssignment;
                    break;
                default:
                    break;
            }
            // End Status Wise Bifurcation/
            // Type Wise Bifurcation
            switch ($myAssignment['assignment_type']) {
                case 'SUBJECTIVE':
                    $subjective['count']++;
                    $subjective['values'][]   =   $myAssignment;
                    break;
                case 'OBJECTIVE':
                    $objective['count']++;
                    $objective['values'][]   =   $myAssignment;
                    break;
                case 'DOCUMENT':
                    $document['count']++;
                    $document['values'][]   =   $myAssignment;
                    break;
                default:
                    break;
            }
            // End Type Wise Bifurcation/

            // Classcode Wise Bifurcation
            $contentKey = array_search($myAssignment['classcode'], array_column($classcodeWise, 'name'));
            if ($contentKey != null || $contentKey !== false) {
                $classcodeWise[$contentKey]['count']++;
                $classcodeWise[$contentKey]['values'][] = $myAssignment;
            } else {
                $classcodeWise[] = [
                    'name'  =>  $myAssignment['classcode'],
                    'count' =>  1,
                    'values' => [$myAssignment],
                ];
            }
            // End Classcode Wise Bifurcation
        }
        $assignmentOverview['statusWiseAssignments'] = [$upcoming, $overdue, $inprogress, $completed];
        $assignmentOverview['typeWiseAssignments'] = [$subjective, $objective, $document];
        $assignmentOverview['classcodeWiseAssignments'] = $classcodeWise;

        // Metadata Overwiew
        $metadataOverview = [];
        $annotations = [
            'name'  =>  'ANNOTATION',
            'count' =>  0,
            'values'    =>  []
        ];
        $highlights = [
            'name'  =>  'HIGHLIGHT',
            'count' =>  0,
            'values'    =>  []
        ];
        $dictionaries = [
            'name'  =>  'DICTIONARY',
            'count' =>  0,
            'values'    =>  []
        ];
        foreach ($classcodes as $classcode) {
            $classContentMetadatas = $classcode->content_metadatas;

            foreach ($classContentMetadatas as $contentMetadata) {
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
        }

        $metadataOverview = [$annotations, $highlights, $dictionaries];

        return response()->json([
            'assignmentOverview' =>  $assignmentOverview,
            'metadataOverview' =>  $metadataOverview,
        ]);
    }
}
