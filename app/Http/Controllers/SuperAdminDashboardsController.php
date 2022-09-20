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
        // Board Wise School Count
        $boards = Board::where('is_active', TRUE)->get();
        $BoardSchoolCount = [];
        foreach ($boards as $key => $board) {
            $board_detail = [];
            $schools = $board->schools;
            $board_name = $board->name;
            $board_detail = [
                'name' => $board_name,
                'count' => sizeof($schools),
                'values' => $schools,
            ];
            $BoardSchoolCount[] = $board_detail;
        }
        $teachersCount =  User::where('is_deleted', false)
            ->whereHas('roles', function ($q) {
                $q->where('name', '=', 'TEACHER');
            })->count();
        $studentsCount =  User::where('is_deleted', false)
            ->whereHas('roles', function ($q) {
                $q->where('name', '=', 'STUDENT');
            })->count();
        $contentsCount = Content::all()->count();
        $toi_papersCount = ToiArticle::all()->count();
        $et_papersCount = EtArticle::all()->count();
        // Contact Request Count Section
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

        // Career Request Count Section
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

        $total_requests = [
            'name' => 'TOTAL',
            'count' => sizeof($all_requests),
            'request' => $all_requests
        ];
        $RequestsCount = [
            'contact_request' => $contact_requests,
            'career_request' => $career_requests,
            'total_request' => $total_requests,
        ];
        $data = [
            'BoardSchoolCount'  =>  $BoardSchoolCount,
            'studentsCount'     =>  $studentsCount,
            'paidStudentsCount' =>  $studentsCount,
            'freeStudentsCount' =>  0,
            'teachersCount'     =>  $teachersCount,
            'contentsCount'     =>  $contentsCount,
            'toi_papersCount'   =>  $toi_papersCount,
            'et_papersCount'    =>  $et_papersCount,
            'RequestsCount'     =>  $RequestsCount,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }

    public function contentBasedCount()
    {
        $content_based_count = [];
        // For Grade
        if (request()->type == 1) {
            return request()->type;
        }
        // For Board
        if (request()->type == 2) {
            return request()->type;
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

    public function SchoolWiseOverview()
    {
        $month = request()->month;
        $year = request()->year;
        $end_date = Carbon::parse("$year-$month")->endOfMonth()->format('Y-n-d');
        $start_date = Carbon::parse("$year-$month")->subMonths(3)->startOfMonth()->format('Y-n-d');

        if (request()->company_id) {
            $company = Company::find(request()->company_id);
            $L3M_Assignment_contents_count = $company->assignments()->where('is_deleted', false);
            $article_read_count = $company->content_reads();
            $searched_categories = $company->searched_categories();
            $searched_subjects = $company->searched_subjects();
            $searched_keywords = $company->searched_keywords();
            $visitors = $company->visitors();
            $assignments_count = $company->assignments()->where('is_deleted', false);
        } else {
            $L3M_Assignment_contents_count = Assignment::where('is_deleted', false);
            $article_read_count = ContentRead::where('content_id', '!=', null);
            $searched_categories = Search::where('search_type', 'CATEGORY');
            $searched_subjects = Search::where('search_type', 'SUBJECT');
            $searched_keywords = Search::where('search_type', 'KEYWORD');
            $visitors = UserTimestamp::where('user_id', '!=', null);
            $assignments_count = Assignment::where('is_deleted', false);
        }

        $L3M_Assignment_contents_count = $L3M_Assignment_contents_count
            ->whereBetween("created_at", [$start_date, $end_date])
            ->count();

        $article_read_count = $article_read_count
            ->whereMonth("created_at", $month)
            ->count();
        $assignments_count = $assignments_count
            ->whereMonth("created_at", $month)
            ->count();

        $visitors = $visitors->whereMonth("created_at", $month)
            ->get();
        $searched_categories = $searched_categories
            ->whereMonth("created_at", $month)
            ->get();
        $searched_subjects = $searched_subjects
            ->whereMonth("created_at", $month)
            ->get();
        $searched_keywords = $searched_keywords
            ->whereMonth("created_at", $month)
            ->get();

        // Most Looked Categories
        $most_looked_categories = [];
        foreach ($searched_categories as $key => $category) {
            $category_name = $category->search;
            $count = 1;
            $category_key = array_search($category_name, array_column($most_looked_categories, 'name'));
            if ($category_key != null || $category_key !== false) {
                // Increase Category Looked Count 
                $most_looked_categories[$category_key]['count']++;
            } else {
                // Category Not Added
                $category_details = [
                    'name' => $category_name,
                    'count' => $count,
                ];
                $most_looked_categories[] = $category_details;
            }
        }
        // Sorting Descending by Count
        usort($most_looked_categories, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        // Most Looked Subjects
        $most_looked_subjects = [];
        foreach ($searched_subjects as $key => $subject) {
            $subject_name = $subject->search;
            $count = 1;
            $subject_key = array_search($subject_name, array_column($most_looked_subjects, 'name'));
            if ($subject_key != null || $subject_key !== false) {
                // Increase Category Looked Count 
                $most_looked_subjects[$subject_key]['count']++;
            } else {
                // Category Not Added
                $subject_details = [
                    'name' => $subject_name,
                    'count' => $count,
                ];
                $most_looked_subjects[] = $subject_details;
            }
        }
        // Sorting Descending by Count
        usort($most_looked_subjects, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        // Most Looked Keywords
        $most_looked_keywords = [];
        foreach ($searched_keywords as $key => $keyword) {
            $keyword_name = $keyword->search;
            $count = 1;
            $keyword_key = array_search($keyword_name, array_column($most_looked_keywords, 'name'));
            if ($keyword_key != null || $keyword_key !== false) {
                // Increase Category Looked Count 
                $most_looked_keywords[$keyword_key]['count']++;
            } else {
                // Category Not Added
                $keyword_details = [
                    'name' => $keyword_name,
                    'count' => $count,
                ];
                $most_looked_keywords[] = $keyword_details;
            }
        }
        // Sorting Descending by Count
        usort($most_looked_keywords, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        // Most Frequent Visitors
        $most_frequent_visitors = [];
        foreach ($visitors as $key => $visitor) {
            $visitor_id = $visitor->user_id;
            $visitor_name = $visitor->user->name;
            $count = 1;
            $visitor_key = array_search($visitor_id, array_column($most_frequent_visitors, 'user_id'));
            if ($visitor_key != null || $visitor_key !== false) {
                // Increase Category Looked Count 
                $most_frequent_visitors[$visitor_key]['count']++;
            } else {
                // Category Not Added
                $visitor_details = [
                    'user_id' => $visitor_id,
                    'name' => $visitor_name,
                    'count' => $count,
                ];
                $most_frequent_visitors[] = $visitor_details;
            }
        }
        // Sorting Descending by Count
        usort($most_frequent_visitors, function ($a, $b) {
            return $b['count'] - $a['count'];
        });
        $data = [
            'avg_time_spent_by_student'     =>  0,
            'avg_time_spent_by_teacher'     =>  0,
            'L3M_Assignment_contents_count' =>  $L3M_Assignment_contents_count,
            'article_read_count'            =>  $article_read_count,
            'most_looked_categories'        =>  $most_looked_categories,
            'most_looked_subjects'          =>  $most_looked_subjects,
            'most_looked_keywords'          =>  $most_looked_keywords,
            'most_frequent_visitors'        =>  $most_frequent_visitors,
            'assignments_count'             =>  $assignments_count,
        ];
        return response()->json([
            'data'  =>  $data
        ], 200);
    }
}
