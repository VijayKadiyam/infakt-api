<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Classcode;
use App\Company;
use App\ContentMetadata;
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
}
