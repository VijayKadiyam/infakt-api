<?php

namespace App\Http\Controllers;

use App\Company;
use App\User;
use Illuminate\Http\Request;

class TeacherDashboardsController extends Controller
{
    public function TeacherWiseOverview(Request $request)
    {
        $request->validate([
            'companyId'    =>  'required',
            'teacherId'    =>  'required',
        ]);

        $teacherId = $request->teacherId;
        $teacher = User::where('users.id', '=', $teacherId)
            ->first();
        $teacherClasscodes = $teacher->classcodes;

        // ---------------------------------------------------------------------------------------------------------
        // Controller Logic
        $students = [];
        foreach ($teacherClasscodes as $teacherClasscode) {
            $classcodeStudents = $teacherClasscode->students;
            foreach ($classcodeStudents as $student) {
                $contentKey = array_search($student['id'], array_column($students, 'id'));
                if ($contentKey != null || $contentKey !== false) {
                    $students[$contentKey]['teacherClassCodes'][] =  $teacherClasscode->toArray();
                } else {
                    $student['teacherClassCodes'] = [$teacherClasscode->toArray()];
                    $students[] = $student->toArray();
                }
            }
        }

        // Assignments of all students
        $allAssignments = [];
        foreach ($students as $student) {
            $classcodes = $student['teacherClassCodes'];
            $studentDashboarsController = new StudentDashboardsController();
            $myAssignments = $studentDashboarsController->assignmentOverview($classcodes, $student);

            $allAssignments = [...$allAssignments, ...$myAssignments];
        }

        // ---------------------------------------------------------------------------------------------------------
        // View Logic

        $assignmentOverview = [
            "assignmentWiseStudents"  =>  [],
            "classcodeWiseStudents"  =>  [],
        ];
        $assignmentWise = [];
        $classcodeWise = [];
        foreach ($allAssignments as $singleAssignment) {

            // Assignment Wise Bifurcation
            $contentKey = array_search($singleAssignment['assignment_id'], array_column($assignmentWise, 'assignment_id'));
            if ($contentKey != null || $contentKey !== false) {
                $assignmentWise[$contentKey]['count']++;
                $assignmentWise[$contentKey]['values'][] = $singleAssignment;
            } else {
                $assignmentWise[] = [
                    'assignment_id' =>  $singleAssignment['assignment_id'],
                    'assignment_title' =>  $singleAssignment['assignment_title'],
                    'classcode'  =>  $singleAssignment['classcode'],
                    'count' =>  1,
                    'teachers' =>  $singleAssignment['teachers'],
                    'myAverage' =>  $singleAssignment['score'],
                    'classcodeAverage' =>  $singleAssignment['classAverage'],
                    'values'    =>  [$singleAssignment]
                ];
            }
            // End Assignment Wise Bifurcation

            // Classcode Wise Bifurcation
            $contentKey = array_search($singleAssignment['classcode'], array_column($classcodeWise, 'name'));
            if ($contentKey != null || $contentKey !== false) {
                $classcodeWise[$contentKey]['count']++;
                $count  = $classcodeWise[$contentKey]['count'];
                // Classcode Average
                $classcodeAverage = $classcodeWise[$contentKey]['classcodeAverage'];
                $classcodeAverage = (($classcodeAverage * ($count - 1)) + $singleAssignment['classAverage'])  / $count;
                $classcodeWise[$contentKey]['classcodeAverage'] =  $classcodeAverage;
                // Students and their values
                $studentContentKey = array_search($singleAssignment['student']['id'], array_column($classcodeWise[$contentKey]['students'], 'id'));
                if ($studentContentKey != null || $studentContentKey !== false) {
                    // My Average
                    $myAverage = $classcodeWise[$contentKey]['students'][$studentContentKey]['myAverage'];
                    $myAverage = (($myAverage * ($count - 1)) + $singleAssignment['score'])  / $count;
                    $classcodeWise[$contentKey]['students'][$studentContentKey]['myAverage'] =  $myAverage;
                    $classcodeWise[$contentKey]['students'][$studentContentKey]['values'][] = $singleAssignment;
                } else {
                    $classcodeWise[$contentKey]['students'][] = [
                        'id'    =>  $singleAssignment['student']['id'],
                        'name'  =>  $singleAssignment['student']['name'],
                        'myAverage'  =>  $singleAssignment['score'],
                        'values'    =>  [$singleAssignment]
                    ];
                }
            } else {
                $classcodeWise[] = [
                    'name'  =>  $singleAssignment['classcode'],
                    'count' =>  1,
                    'teachers' =>  $singleAssignment['teachers'],
                    'classcodeAverage' =>  $singleAssignment['classAverage'],
                    'students' => [
                        0   =>  [
                            'id'    =>  $singleAssignment['student']['id'],
                            'name'  =>  $singleAssignment['student']['name'],
                            'myAverage' =>  $singleAssignment['score'],
                            'values'    =>  [$singleAssignment]
                        ]
                    ],
                ];
            }
            // End Classcode Wise Bifurcation
        }

        $assignmentOverview['assignmentWiseStudents'] = $assignmentWise;
        $assignmentOverview['classcodeWiseStudents'] = $classcodeWise;

        return response()->json([
            'students'              =>   sizeof($students),
            'classcodes'            =>  sizeof($teacherClasscodes),
            'assignments'           =>  sizeof($assignmentWise),
            'assignmentOverview'    =>  $assignmentOverview,
        ]);
    }
}
