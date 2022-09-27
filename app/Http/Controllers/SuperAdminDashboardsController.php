<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Board;
use App\CareerRequest;
use App\Company;
use App\ContactRequest;
use App\Content;
use App\ContentRead;
use App\ContentSubject;
use App\EtArticle;
use App\Grade;
use App\Search;
use App\Subject;
use App\ToiArticle;
use App\User;
use App\UserTimestamp;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SuperAdminDashboardsController extends Controller
{
    public function superadminDashboard(Request $request)
    {
        $counts = [];

        $counts['schools'] = Company::count();
        $counts['teachers'] = User::where('is_deleted', false)
            ->whereHas('roles', function ($q) {
                $q->where('name', '=', 'TEACHER');
            })->count();
        $counts['students'] = User::where('is_deleted', false)
            ->whereHas('roles', function ($q) {
                $q->where('name', '=', 'STUDENT');
            })->count();
        $counts['contents']  = Content::all()->count();
        $counts['toi']   = ToiArticle::all()->count();
        $counts['et']   = EtArticle::all()->count();


        $contactRequests = ContactRequest::where('is_deleted', false)->get();
        $settled_contact_request = [];
        $pending_contact_request = [];
        foreach ($contactRequests as $key => $request) {
            if ($request->status == "SETTLED") {
                $settled_contact_request[] = $request;
            } else {
                $pending_contact_request[] = $request;
            }
            $all_requests[] = $request;
        }
        $contact_requests = [
            [
                'name' => "PENDING",
                'count' => sizeOf($pending_contact_request),
                'request' => $pending_contact_request
            ],
            [
                'name' => "SETTLED",
                'count' => sizeOf($settled_contact_request),
                'request' => $settled_contact_request
            ],
            [
                'name' => "TOTAL",
                'count' => sizeof($contactRequests),
                'request' => $contactRequests
            ]
        ];
        $counts['contactRequests'] = $contact_requests;

        $careerRequests = CareerRequest::where('is_deleted', false)->get();
        $settled_career_request = [];
        $pending_career_request = [];
        foreach ($careerRequests as $key => $request) {
            if ($request->status == "SETTLED") {
                $settled_career_request[] = $request;
            } else {
                $pending_career_request[] = $request;
            }
            $all_requests[] = $request;
        }
        $career_requests = [
            [
                'name' => "PENDING",
                'count' => sizeOf($pending_career_request),
                'request' => $pending_career_request
            ],
            [
                'name' => "SETTLED",
                'count' => sizeOf($settled_career_request),
                'request' => $settled_career_request
            ],
            [
                'name' => "TOTAL",
                'count' => sizeof($careerRequests),
                'request' => $careerRequests
            ]
        ];
        $counts['careerRequests'] = $career_requests;

        return response()->json([
            'counts'    =>  $counts,
        ]);
    }

    public function contentBasedCount()
    {
        $content_based_count = [];
        // For Grade
        if (request()->type == 1) {
            $grades = Grade::get();
            foreach ($grades as $key => $grade) {
                $contents =  Content::whereHas('content_grades', function ($q) use ($grade) {
                    $q->where('grade_id', '=', $grade->id);
                })->get();
                $content_based_count[$key]['name'] = $grade->name;
                $content_based_count[$key]['count'] = sizeof($contents);
            }
        }
        // For Board
        if (request()->type == 2) {
            $boards = Board::get();
            foreach ($boards as $key => $board) {
                $contents =  Content::whereHas('content_boards', function ($q) use ($board) {
                    $q->where('board_id', '=', $board->id);
                })->get();
                $content_based_count[$key]['name'] = $board->name;
                $content_based_count[$key]['count'] = sizeof($contents);
            }
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
}
