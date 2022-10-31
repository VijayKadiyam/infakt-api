<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Classcode;
use App\Company;
use App\ContentMetadata;
use App\Role;
use App\Search;
use App\User;
use App\UserAssignment;
use Illuminate\Http\Request;

class AdminDashboardsController extends Controller
{
    public function SchoolWiseOverview(Request $request)
    {
        $request->validate([
            'companyId'    =>  'required',
        ]);

        $company = Company::find($request->companyId);

        $counts = [];
        $counts['avgTimeSpentByTeacher'] = '23 min';
        $counts['avgTimeSpentByStudent'] = '42 min';
        $counts['contentReads'] = $company->content_reads()->count();
        $counts['assignmentsPosted'] = $company->assignments()->where('is_deleted', false)->count();
        $counts['students'] =  $company->students()->count();
        $counts['teachers'] = $company->teachers()->count();
        $counts['classcodes'] = $company->classcodes()->count();

        $searches = $company->searches;

        $mostLookedSubjects = [];
        $mostLookedKeywords = [];


        foreach ($searches as $search) {
            if ($search->search_type == 'SUBJECT') {
                $subjectKey = array_search($search->search, array_column($mostLookedSubjects, 'name'));
                if ($subjectKey != null || $subjectKey !== false) {
                    $mostLookedSubjects[$subjectKey]['count']++;
                } else {
                    $mostLookedSubjects[] = [
                        'name' => $search->search,
                        'count' => 1,
                    ];
                }
            }

            if ($search->search_type == 'KEYWORD') {
                $keywordKey = array_search($search->search, array_column($mostLookedKeywords, 'name'));
                if ($keywordKey != null || $keywordKey !== false) {
                    $mostLookedKeywords[$keywordKey]['count']++;
                } else {
                    $mostLookedKeywords[] = [
                        'name' => $search->search,
                        'count' => 1,
                    ];
                }
            }
        }

        return response()->json([
            'counts'  =>  $counts,
            'mostLookedSubjects'    =>  $mostLookedSubjects,
            'mostLookedKeywords'    =>  $mostLookedKeywords
        ]);
    }

    public function TaskWiseOverview(Request $request)
    {
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', '1000M');
        set_time_limit(0);
        if (request()->company)
            $users = request()->company->users()->where('is_deleted', false)->with('roles', 'user_classcodes');
        else
            $users = User::where('is_deleted', false)->with('roles', 'user_classcodes');
        if ($request->role_id) {
            $role = Role::find($request->role_id);
            if (request()->company)
                $users = request()->company->users()->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', '=', $role->name);
                });
            else
                $users = User::with('roles', 'classcodes', 'board', 'user_classcodes')->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', '=', $role->name);
                });
        }

        if ($request->standard_id) {
            $users = $users->whereHas('user_classcodes', function ($uc) {
                $uc->where('standard_id', '=', request()->standard_id);
            });
        }
        if ($request->section_id) {
            $users = $users->whereHas('user_classcodes', function ($uc) {
                $uc->where('section_id', '=', request()->section_id);
            });
        }
        $users = $users->get();
        foreach ($users as $key => $student) {
            $classcodes = $student->classcodes()
                ->where('user_classcodes.standard_id', $request->standard_id)
                ->where('user_classcodes.section_id', $request->section_id)
                ->get();
            $studentDashboarsController = new StudentDashboardsController();
            $myAssignments = $studentDashboarsController->assignmentOverview($classcodes, $student);
            $student['totalAssignmentsCount'] = sizeof($myAssignments);
            $inprogress_count = 0;
            $completed_count = 0;
            foreach ($myAssignments as $singleAssignment) {
                // Status Wise Bifurcation
                switch ($singleAssignment['status']) {
                    case 'IN PROGRESS':
                        $inprogress_count++;
                        break;
                    case 'COMPLETED':
                        $completed_count++;
                        break;
                    default:
                        break;
                }
                // End Status Wise Bifurcation/
            }

            $student['inprogress_count'] = $inprogress_count;
            $student['completed_count'] = $completed_count;
        }

        return response()->json([
            'data'  =>  $users,
            'count' =>   sizeof($users),
            'success' =>  true,
        ], 200);
    }
    public function subjectWiseOverview(Request $request)
    {
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', '1000M');
        set_time_limit(0);
        if (request()->company)
            $users = request()->company->users()->where('is_deleted', false)->with('roles', 'user_classcodes');
        else
            $users = User::where('is_deleted', false)->with('roles', 'user_classcodes');
        if ($request->role_id) {
            $role = Role::find($request->role_id);
            if (request()->company)
                $users = request()->company->users()->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', '=', $role->name);
                });
            else
                $users = User::with('roles', 'classcodes', 'board', 'user_classcodes')->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', '=', $role->name);
                });
        }

        $classcodes = Classcode::where('is_deleted', false);
        if ($request->standard_id) {
            $classcodes = $classcodes->where('standard_id', request()->standard_id);
            $users = $users->whereHas('user_classcodes', function ($uc) {
                $uc->where('standard_id', '=', request()->standard_id);
            });
        }
        if ($request->section_id) {
            $classcodes = $classcodes->where('section_id', request()->section_id);
            $users = $users->whereHas('user_classcodes', function ($uc) {
                $uc->where('section_id', '=', request()->section_id);
            });
        }
        $users = $users->get();
        $classcodes = $classcodes->get();
        foreach ($users as $key => $student) {
            $classcodeWise = [];
            $studentDashboarsController = new StudentDashboardsController();
            $myAssignments = $studentDashboarsController->assignmentOverview($classcodes, $student);
            $student['totalAssignmentsCount'] = sizeof($myAssignments);
            foreach ($myAssignments as $singleAssignment) {
                // Classcode Wise Bifurcation
                $classcode_Key = array_search($singleAssignment['classcode'], array_column($classcodeWise, 'name'));
                if ($classcode_Key != null || $classcode_Key !== false) {
                    $classcodeWise[$classcode_Key]['count']++;
                    // Status Wise Bifurcation
                    switch ($singleAssignment['status']) {
                        case 'IN PROGRESS':
                            $classcodeWise[$classcode_Key]['inprogress_count']++;
                            break;
                        case 'COMPLETED':
                            $classcodeWise[$classcode_Key]['completed_task']++;
                            break;
                        default:
                            break;
                    }
                    // End Status Wise Bifurcation/

                    $count = $classcodeWise[$classcode_Key]['count'];
                    $completed_count = $classcodeWise[$classcode_Key]['completed_task'];
                    $completed_average = $completed_count / $count * 100;
                    $classcodeWise[$classcode_Key]['completed_average'] = $completed_average;
                } else {
                    $classcodeWise[] = [
                        'name'  =>  $singleAssignment['classcode'],
                        'count' =>  1,
                        'completed_task' => $singleAssignment['status'] == 'COMPLETED' ? 1 : 0,
                        'inprogress_count' => $singleAssignment['status'] == 'IN PROGRESS' ? 1 : 0,
                        'completed_average' =>  $singleAssignment['status'] == 'COMPLETED' ? 100 : 0,
                    ];
                }
                // End Classcode Wise Bifurcation
            }
            // $completed_average=$c
            $student['classcodeWiseOverview'] = $classcodeWise;
        }

        return response()->json([
            'data'  =>  $users,
            'count' =>   sizeof($users),
            'success' =>  true,
        ], 200);
    }
}
