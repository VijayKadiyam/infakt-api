<?php

namespace App\Http\Controllers;

use App\Category;
use App\Company;
use App\Content;
use App\ContentAssignToRead;
use App\ContentBoard;
use App\ContentCategory;
use App\ContentDescription;
use App\ContentGrade;
use App\ContentHiddenClasscode;
use App\ContentInfoBoard;
use App\ContentLockClasscode;
use App\ContentMedia;
use App\ContentSchool;
use App\ContentSubject;
use App\EtArticle;
use App\Search;
use App\Subject;
use App\ToiArticle;
use App\User;
use App\UserClasscode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    public function masters(Request $request)
    {
        $user = Auth::user();
        $user_role = $user->roles[0]->name;
        if ($user_role == "INFAKT TEACHER") {
            $subjects = $user->subjects;
        } else {
            $subjectsController = new SubjectsController();
            $subjectsResponse = $subjectsController->index($request);
            $subjects = $subjectsResponse->getData()->data;
        }
        // $collectionsController = new CollectionsController();
        // $request->request->add(['user_id' => $request->user_id]);
        // $collectionsResponse = $collectionsController->index($request);
        $usersController = new UsersController();
        $request->request->add(['role_id' => 4]);
        $usersResponse = $usersController->index($request);

        $categoriesController = new CategoriesController();
        $categoriesResponse = $categoriesController->index($request);


        $gradesController = new GradesController();
        $gradesResponse = $gradesController->index($request);

        $boardsController = new BoardsController();
        $boardsResponse = $boardsController->index($request);

        $schoolsController = new CompaniesController();
        $schoolsResponse = $schoolsController->index($request);

        return response()->json([
            // 'collections'           =>  $collectionsResponse->getData()->data,
            'users'      =>  $usersResponse->getData()->data,
            'categories' =>  $categoriesResponse->getData()->data,
            'subjects'   =>  $subjects,
            'grades'     =>  $gradesResponse->getData()->data,
            'boards'     =>  $boardsResponse->getData()->data,
            'schools'    =>  $schoolsResponse->getData()->data,
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $content_limit_4 = request()->content_limit_4 ? request()->content_limit_4 : false;
    //     $category_wise_limit_4 = request()->category_wise_limit_4 ? request()->category_wise_limit_4 : false;
    //     $Assigned_to_read_articles = [];

    //     $user = Auth::user();
    //     $my_assignments = $user->assignments;
    //     $contents = Content::with('content_subjects', 'content_medias', 'content_reads', 'content_descriptions', 'content_hidden_classcodes', 'content_grades', 'content_boards', 'created_by');
    //     if (request()->subject_id) {
    //         $subject = Subject::find(request()->subject_id);
    //         $contents = $contents->whereHas('content_subjects', function ($c) {
    //             $c->where('subject_id', '=', request()->subject_id);
    //         });
    //         Search::create([
    //             'company_id' =>  Auth::user()->companies[0]->id,
    //             'user_id'   =>      Auth::user()->id,
    //             'search_type'   =>  'SUBJECT',
    //             'search'        =>  $subject->name
    //         ]);
    //     }
    //     if (request()->search_keyword) {
    //         $contents = $contents
    //             ->where('content_type', 'LIKE', '%' . request()->search_keyword . '%')
    //             ->orWhere('content_name', 'LIKE', '%' . request()->search_keyword . '%')
    //             ->orWhere('created_at', 'LIKE', '%' . request()->search_keyword . '%');

    //         if (isset(Auth::user()->roles[0]->name) != 'ACADEMIC TEAM') {
    //             if (isset(Auth::user()->companies))
    //                 Search::create([
    //                     'company_id' =>  Auth::user()->companies[0]->id,
    //                     'user_id'   =>      Auth::user()->id,
    //                     'search_type'   =>  'KEYWORD',
    //                     'search'        =>  request()->search_keyword
    //                 ]);
    //         }
    //     }
    //     if (request()->date_filter) {
    //         $contents = $contents
    //             ->Where('created_at', 'LIKE', '%' . request()->date_filter . '%');
    //     }
    //     if (request()->academic_team) {
    //         $contents = $contents
    //             ->Where('is_draft', false);
    //     }
    //     if (request()->academic_team_approval) {
    //         $contents = $contents
    //             ->Where('is_approved', false);
    //     }
    //     if (request()->type) {
    //         $contents = $contents
    //             ->Where('content_type', request()->type);
    //     }
    //     if (request()->user_id) {
    //         $contents = $contents
    //             ->Where('created_by_id', request()->user_id);
    //     }
    //     if (request()->approved_id == 'APPROVED') {
    //         $contents = $contents
    //             ->Where('is_approved', true);
    //     }
    //     if (request()->approved_id == 'PENDING') {
    //         $contents = $contents
    //             ->Where('is_approved', false);
    //     }
    //     if (request()->active_id == "ACTIVE") {
    //         $contents = $contents
    //             ->Where('is_active', true);
    //     }
    //     if (request()->active_id == "INACTIVE") {
    //         $contents = $contents
    //             ->Where('is_active', false);
    //     }
    //     if (request()->category_id) {
    //         $category = Category::find(request()->category_id);
    //         $contents = $contents->whereHas('content_categories', function ($c) {
    //             $c->where('category_id', '=', request()->category_id);
    //         });
    //         Search::create([
    //             'company_id' =>  Auth::user()->companies[0]->id,
    //             'user_id'   =>      Auth::user()->id,
    //             'search_type'   =>  'CATEGORY',
    //             'search'        =>  $category->name
    //         ]);
    //     }
    //     if ($content_limit_4) {
    //         $contents = $contents->limit(4);
    //     }
    //     $contents = $contents->latest()->get();
    //     $user_role = request()->roleName;
    //     $user_id = request()->user_id;
    //     if ($user_role == 'STUDENT') {
    //         // If Role is Student// Show Filtered Content
    //         $user_classcodes =  UserClasscode::where('user_id', $user_id)->get();
    //         $user_classcode_array = array_column($user_classcodes->toArray(), 'classcode_id');
    //         $filtered_contents = [];
    //         $currentDate = date_create(date('Y-m-d'));
    //         foreach ($contents as $key => $content) {
    //             $content_assign_to_read = $content->content_assign_to_reads;
    //             $endDiff = 0;
    //             if (sizeof($content_assign_to_read) > 0) {
    //                 $endDate = date_create($content_assign_to_read[0]['due_date']);
    //                 $endDiff = date_diff($currentDate, $endDate)->format("%R%a");
    //             }
    //             $isDue = $endDiff < 0 ? true : false;
    //             $assigned_to_read_array = array_column($content_assign_to_read->toArray(), 'classcode_id');
    //             if (array_intersect($user_classcode_array, $assigned_to_read_array) && $isDue == false) {
    //                 $content['assign_to_read'] = true;
    //                 $Assigned_to_read_articles[] = $content;
    //             } else {
    //                 $content['assign_to_read'] = false;
    //             }
    //             $content_hidden_classcodes = $content->content_hidden_classcodes;
    //             $hidden_classcode_array = array_column($content_hidden_classcodes->toArray(), 'classcode_id');
    //             if (!array_intersect($user_classcode_array, $hidden_classcode_array)) {
    //                 $filtered_contents[] = $content;
    //             }
    //         }
    //         $contents = $filtered_contents;
    //     }
    //     $article_contents = [];
    //     $infographic_contents = [];
    //     $video_contents = [];
    //     $CategoryWiseContent = [];
    //     foreach ($contents as $key => $content) {
    //         // Random Subject Image 
    //         $image_Array = [];
    //         $content->subject_image = "";
    //         if (sizeof($content->content_subjects)) {
    //             for ($i = 1; $i < 6; $i++) {
    //                 $name = "imagepath_" . $i;
    //                 if ($content->content_subjects[0]->subject->$name) {
    //                     $image_Array[] = $content->content_subjects[0]->subject->$name;
    //                 }
    //             }
    //             $rand_subject_image = array_rand(
    //                 $image_Array,
    //                 1
    //             );
    //             $content->subject_image = $image_Array[$rand_subject_image];
    //         }
    //         // Content type Wise
    //         switch ($content->content_type) {
    //             case 'ARTICLE':
    //                 $article_contents[] = $content;
    //                 break;
    //             case 'INFOGRAPHIC':
    //                 $infographic_contents[] = $content;
    //                 break;
    //             case 'VIDEO':
    //                 $video_contents[] = $content;
    //                 break;

    //             default:
    //                 # code...
    //                 break;
    //         }
    //         // Category Wise  
    //         if (sizeOf($content->content_categories)) {
    //             // Select First Category 
    //             $category = $content->content_categories[0]->category;
    //             $category_key = array_search($category->id, array_column($CategoryWiseContent, 'id'));
    //             if (($category_key != null || $category_key !== false)) {
    //                 // Increase Content Count 
    //                 $CategoryWiseContent[$category_key]['count']++;
    //                 if ($category_wise_limit_4 != false) {
    //                     // If Limit is set to 4
    //                     if ($CategoryWiseContent[$category_key]['count'] <= 4) {
    //                         // Check if Count is not Exceeding than 4 and And Content
    //                         $CategoryWiseContent[$category_key]['values'][] = $content;
    //                     }
    //                 } else {
    //                     // Add Content in array
    //                     $CategoryWiseContent[$category_key]['values'][] = $content;
    //                 }
    //             } else {
    //                 // Content Added
    //                 $content_details = [
    //                     'id' => $category->id,
    //                     'category' => $category->name,
    //                     'values' => [$content],
    //                     'count' => 1,
    //                 ];
    //                 $CategoryWiseContent[] = $content_details;
    //             }
    //         }
    //     }
    //     $content_types = [
    //         [
    //             'name' => "ARTICLE",
    //             'icon' => "mdi-script-text",
    //             'count' => sizeof($article_contents),
    //             'values' => $article_contents
    //         ],
    //         [
    //             'name' => "INFOGRAPHIC",
    //             'icon' => 'mdi-chart-bar',
    //             'count' => sizeof($infographic_contents),
    //             'values' => $infographic_contents
    //         ],
    //         [
    //             'name' => "VIDEO",
    //             'icon' => 'mdi-video-vintage',
    //             'count' => sizeof($video_contents),
    //             'values' => $video_contents
    //         ]
    //     ];
    //     return response()->json([
    //         'data'  =>  $contents,
    //         'count' =>   sizeof($contents),
    //         'content_types' => $content_types,
    //         'CategoryWiseContent' => $CategoryWiseContent,
    //         'Assign_to_read_articles' => $Assigned_to_read_articles,
    //         'assignments' => $my_assignments,
    //         'success' =>  true,
    //     ], 200);
    // }
    public function index()
    {
        $content_limit_4 = request()->content_limit_4 ? request()->content_limit_4 : false;
        $category_wise_limit_4 = request()->category_wise_limit_4 ? request()->category_wise_limit_4 : false;
        $Assigned_to_read_articles = [];

        $user = Auth::user();
        $my_assignments = $user->assignments;
        $contents = Content::with('content_subjects', 'content_medias', 'content_reads', 'content_descriptions', 'content_hidden_classcodes', 'content_grades', 'content_boards', 'created_by');
        if (request()->subject_id) {
            $subject = Subject::find(request()->subject_id);
            $contents = $contents->whereHas('content_subjects', function ($c) {
                $c->where('subject_id', '=', request()->subject_id);
            });
            Search::create([
                'company_id' =>  Auth::user()->companies[0]->id,
                'user_id'   =>      Auth::user()->id,
                'search_type'   =>  'SUBJECT',
                'search'        =>  $subject->name
            ]);
        }
        if (request()->search_keyword) {
            $contents = $contents
                ->where('content_type', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('content_name', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('created_at', 'LIKE', '%' . request()->search_keyword . '%');

            if (isset(Auth::user()->roles[0]->name) != 'ACADEMIC TEAM') {
                if (isset(Auth::user()->companies))
                    Search::create([
                        'company_id' =>  Auth::user()->companies[0]->id,
                        'user_id'   =>      Auth::user()->id,
                        'search_type'   =>  'KEYWORD',
                        'search'        =>  request()->search_keyword
                    ]);
            }
        }
        if (request()->date_filter) {
            $contents = $contents
                ->Where('created_at', 'LIKE', '%' . request()->date_filter . '%');
        }
        if (request()->type) {
            $contents = $contents
                ->Where('content_type', request()->type);
        }
        if (request()->category_id) {
            $category = Category::find(request()->category_id);
            $contents = $contents->whereHas('content_categories', function ($c) {
                $c->where('category_id', '=', request()->category_id);
            });
            Search::create([
                'company_id' =>  Auth::user()->companies[0]->id,
                'user_id'   =>      Auth::user()->id,
                'search_type'   =>  'CATEGORY',
                'search'        =>  $category->name
            ]);
        }
        if ($content_limit_4) {
            $contents = $contents->limit(4);
        }
        $contents = $contents->latest()->get();
        $user_role = request()->roleName;
        $user_id = request()->user_id;
        if ($user_role == 'STUDENT') {
            // If Role is Student// Show Filtered Content
            $user_classcodes =  UserClasscode::where('user_id', $user_id)->get();
            $user_classcode_array = array_column($user_classcodes->toArray(), 'classcode_id');
            $filtered_contents = [];
            $currentDate = date_create(date('Y-m-d'));
            foreach ($contents as $key => $content) {
                $content_assign_to_read = $content->content_assign_to_reads;
                $endDiff = 0;
                if (sizeof($content_assign_to_read) > 0) {
                    $endDate = date_create($content_assign_to_read[0]['due_date']);
                    $endDiff = date_diff($currentDate, $endDate)->format("%R%a");
                }
                $isDue = $endDiff < 0 ? true : false;
                $assigned_to_read_array = array_column($content_assign_to_read->toArray(), 'classcode_id');
                if (array_intersect($user_classcode_array, $assigned_to_read_array) && $isDue == false) {
                    $content['assign_to_read'] = true;
                    $Assigned_to_read_articles[] = $content;
                } else {
                    $content['assign_to_read'] = false;
                }
                $content_hidden_classcodes = $content->content_hidden_classcodes;
                $hidden_classcode_array = array_column($content_hidden_classcodes->toArray(), 'classcode_id');
                if (!array_intersect($user_classcode_array, $hidden_classcode_array)) {
                    $filtered_contents[] = $content;
                }
            }
            $contents = $filtered_contents;
        }
        $article_contents = [];
        $infographic_contents = [];
        $video_contents = [];
        $CategoryWiseContent = [];
        foreach ($contents as $key => $content) {
            // Random Subject Image 
            $image_Array = [];
            $content->subject_image = "";
            if (sizeof($content->content_subjects)) {
                for ($i = 1; $i < 6; $i++) {
                    $name = "imagepath_" . $i;
                    if ($content->content_subjects[0]->subject->$name) {
                        $image_Array[] = $content->content_subjects[0]->subject->$name;
                    }
                }
                $rand_subject_image = array_rand(
                    $image_Array,
                    1
                );
                $content->subject_image = $image_Array[$rand_subject_image];
            }
            // Content type Wise
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
                    # code...
                    break;
            }
            // Category Wise  
            if (sizeOf($content->content_categories)) {
                // Select First Category 
                $category = $content->content_categories[0]->category;
                $category_key = array_search($category->id, array_column($CategoryWiseContent, 'id'));
                if (($category_key != null || $category_key !== false)) {
                    // Increase Content Count 
                    $CategoryWiseContent[$category_key]['count']++;
                    if ($category_wise_limit_4 != false) {
                        // If Limit is set to 4
                        if ($CategoryWiseContent[$category_key]['count'] <= 4) {
                            // Check if Count is not Exceeding than 4 and And Content
                            $CategoryWiseContent[$category_key]['values'][] = $content;
                        }
                    } else {
                        // Add Content in array
                        $CategoryWiseContent[$category_key]['values'][] = $content;
                    }
                } else {
                    // Content Added
                    $content_details = [
                        'id' => $category->id,
                        'category' => $category->name,
                        'values' => [$content],
                        'count' => 1,
                    ];
                    $CategoryWiseContent[] = $content_details;
                }
            }
        }
        $content_types = [
            [
                'name' => "ARTICLE",
                'icon' => "mdi-script-text",
                'count' => sizeof($article_contents),
                'values' => $article_contents
            ],
            [
                'name' => "INFOGRAPHIC",
                'icon' => 'mdi-chart-bar',
                'count' => sizeof($infographic_contents),
                'values' => $infographic_contents
            ],
            [
                'name' => "VIDEO",
                'icon' => 'mdi-video-vintage',
                'count' => sizeof($video_contents),
                'values' => $video_contents
            ]
        ];
        return response()->json([
            'data'  =>  $contents,
            'count' =>   sizeof($contents),
            'content_types' => $content_types,
            'CategoryWiseContent' => $CategoryWiseContent,
            'Assign_to_read_articles' => $Assigned_to_read_articles,
            'assignments' => $my_assignments,
            'success' =>  true,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'content_name'  =>  'required',
            'content_categories'  =>  'required',
            'content_assign_to_reads.*.due_date'    =>  'required',
        ]);
        if ($request->id == null || $request->id == '') {
            // Save Content
            $content = new Content(request()->all());
            $content->save();
            // Save Content Categories
            if (isset($request->content_categories))
                foreach ($request->content_categories as $category) {
                    $category = new ContentCategory($category);
                    $content->content_categories()->save($category);
                }
            // ---------------------------------------------------
            // Save Content Subjects
            if (isset($request->content_subjects))
                foreach ($request->content_subjects as $subject) {
                    $subject = new ContentSubject($subject);
                    $content->content_subjects()->save($subject);
                }
            // ---------------------------------------------------
            // Save Content Medias
            if (isset($request->content_medias))
                foreach ($request->content_medias as $media) {
                    $media = new ContentMedia($media);
                    $content->content_medias()->save($media);
                }
            // ---------------------------------------------------
            // Save Content Descriptions
            if (isset($request->content_descriptions))
                foreach ($request->content_descriptions as $cd) {
                    $cd = new ContentDescription($cd);
                    $content->content_descriptions()->save($cd);
                }
            // ---------------------------------------------------
            // Save Content Hidden Classcode
            if (isset($request->content_hidden_classcodes))
                foreach ($request->content_hidden_classcodes as $hidden_classcode) {
                    $content_hidden_classcode = new ContentHiddenClasscode($hidden_classcode);
                    $content->content_hidden_classcodes()->save($content_hidden_classcode);
                }
            // ---------------------------------------------------
            // Save Content Lock Classcode
            if (isset($request->content_lock_classcodes))
                foreach ($request->content_lock_classcodes as $lock_classcode) {
                    $content_lock_classcode = new ContentLockClasscode($lock_classcode);
                    $content->content_lock_classcodes()->save($content_lock_classcode);
                }
            // ---------------------------------------------------
            // Save Content Assign To Reads
            if (isset($request->content_assign_to_reads))
                foreach ($request->content_assign_to_reads as $assign_to_read) {

                    $content_assign_to_read = new ContentAssignToRead($assign_to_read);
                    $content->content_assign_to_reads()->save($content_assign_to_read);
                }
            // ---------------------------------------------------
            // Save Content Grades
            if (isset($request->content_grades))
                foreach ($request->content_grades as $grade) {
                    $content_grade = new ContentGrade($grade);
                    $content->content_grades()->save($content_grade);
                }
            // ---------------------------------------------------
            // Save Content Boards
            if (isset($request->content_boards))
                foreach ($request->content_boards as $board) {
                    $content_board = new ContentBoard($board);
                    $content->content_boards()->save($content_board);
                }
            // ---------------------------------------------------
            // Save Content Info Boards
            if (isset($request->content_info_boards))
                foreach ($request->content_info_boards as $info_board) {
                    $content_info_board = new ContentInfoBoard($info_board);
                    $content->content_info_boards()->save($content_info_board);
                }
            // ---------------------------------------------------
            // Save Content Schools
            if (isset($request->content_schools))
                foreach ($request->content_schools as $school) {
                    $content_school = new ContentSchool($school);
                    $content->content_schools()->save($content_school);
                }
            // ---------------------------------------------------
        } else {
            // Update Content
            $content = Content::find($request->id);
            $content->update($request->all());

            // Check if Content Category deleted
            if (isset($request->content_categories)) {
                $contentCategoryIdResponseArray = array_pluck($request->content_categories, 'id');
            } else
                $contentCategoryIdResponseArray = [];
            $contentId = $content->id;
            $contentCategoryIdArray = array_pluck(ContentCategory::where('content_id', '=', $contentId)->get(), 'id');
            $differenceContentCategoryIds = array_diff($contentCategoryIdArray, $contentCategoryIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceContentCategoryIds)
                foreach ($differenceContentCategoryIds as $differenceContentCategoryId) {
                    $contentCategory = ContentCategory::find($differenceContentCategoryId);
                    $contentCategory->delete();
                }

            // Update Content Category
            if (isset($request->content_categories))
                foreach ($request->content_categories as $category) {
                    if (!isset($category['id'])) {
                        $content_category = new ContentCategory($category);
                        $content->content_categories()->save($content_category);
                    } else {
                        $content_category = ContentCategory::find($category['id']);
                        $content_category->update($category);
                    }
                }

            // ---------------------------------------------------
            // Check if Content Subject deleted
            if (isset($request->content_subjects)) {
                $contentSubjectIdResponseArray = array_pluck($request->content_subjects, 'id');
            } else
                $contentSubjectIdResponseArray = [];
            $contentId = $content->id;
            $contentSubjectIdArray = array_pluck(ContentSubject::where('content_id', '=', $contentId)->get(), 'id');
            $differenceContentSubjectIds = array_diff($contentSubjectIdArray, $contentSubjectIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceContentSubjectIds)
                foreach ($differenceContentSubjectIds as $differenceContentSubjectId) {
                    $contentSubject = ContentSubject::find($differenceContentSubjectId);
                    $contentSubject->delete();
                }

            // Update Content Subject
            if (isset($request->content_subjects))
                foreach ($request->content_subjects as $subject) {
                    if (!isset($subject['id'])) {
                        $content_subject = new ContentSubject($subject);
                        $content->content_subjects()->save($content_subject);
                    } else {
                        $content_subject = ContentSubject::find($subject['id']);
                        $content_subject->update($subject);
                    }
                }

            // ---------------------------------------------------
            // Check if Content Media deleted
            if (isset($request->content_medias)) {
                $contentMediaIdResponseArray = array_pluck($request->content_medias, 'id');
            } else
                $contentMediaIdResponseArray = [];
            $contentId = $content->id;
            $contentMediaIdArray = array_pluck(ContentMedia::where('content_id', '=', $contentId)->get(), 'id');
            $differenceContentMediaIds = array_diff($contentMediaIdArray, $contentMediaIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceContentMediaIds)
                foreach ($differenceContentMediaIds as $differenceContentMediaId) {
                    $contentMedia = ContentMedia::find($differenceContentMediaId);
                    $contentMedia->delete();
                }

            // Update Content Media
            if (isset($request->content_medias))
                foreach ($request->content_medias as $media) {
                    if (!isset($media['id'])) {
                        $content_media = new ContentMedia($media);
                        $content->content_medias()->save($content_media);
                    } else {
                        $content_media = ContentMedia::find($media['id']);
                        $content_media->update($media);
                    }
                }

            // ---------------------------------------------------
            // Check if Content Description deleted
            if (isset($request->content_descriptions)) {
                $contentDescriptionIdResponseArray = array_pluck($request->content_descriptions, 'id');
            } else
                $contentDescriptionIdResponseArray = [];
            $contentId = $content->id;
            $contentDescriptionIdArray = array_pluck(ContentDescription::where('content_id', '=', $contentId)->get(), 'id');
            $differenceContentDescriptionIds = array_diff($contentDescriptionIdArray, $contentDescriptionIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceContentDescriptionIds)
                foreach ($differenceContentDescriptionIds as $differenceContentDescriptionId) {
                    $contentDescription = ContentDescription::find($differenceContentDescriptionId);
                    $contentDescription->delete();
                }

            // Update Content Description
            if (isset($request->content_descriptions))
                foreach ($request->content_descriptions as $description) {
                    if (!isset($description['id'])) {
                        $content_description = new ContentDescription($description);
                        $content->content_descriptions()->save($content_description);
                    } else {
                        $content_description = ContentDescription::find($description['id']);
                        $content_description->update($description);
                    }
                }

            // ---------------------------------------------------

            // Check if Content Hidden classcode deleted
            if (isset($request->content_hidden_classcodes)) {
                $contentHiddenClasscodeIdResponseArray = array_pluck($request->content_hidden_classcodes, 'id');
            } else
                $contentHiddenClasscodeIdResponseArray = [];
            $contentId = $content->id;
            $contentHiddenClasscodeIdArray = array_pluck(ContentHiddenClasscode::where('content_id', '=', $contentId)->get(), 'id');
            $differenceContentHddenClasscodeIds = array_diff($contentHiddenClasscodeIdArray, $contentHiddenClasscodeIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceContentHddenClasscodeIds)
                foreach ($differenceContentHddenClasscodeIds as $differenceContentHddenClasscodeId) {
                    $contentHddenClasscode = ContentHiddenClasscode::find($differenceContentHddenClasscodeId);
                    $contentHddenClasscode->delete();
                }

            // Update Content Hdden
            if (isset($request->content_hidden_classcodes))
                foreach ($request->content_hidden_classcodes as $hidden_classcode) {
                    if (!isset($hidden_classcode['id'])) {
                        $content_hidden_classcode = new ContentHiddenClasscode($hidden_classcode);
                        $content->content_hidden_classcodes()->save($content_hidden_classcode);
                    } else {
                        $content_hidden_classcode = ContentHiddenClasscode::find($hidden_classcode['id']);
                        $content_hidden_classcode->update($hidden_classcode);
                    }
                }

            // ---------------------------------------------------
            // Check if Content Lock classcode deleted
            if (isset($request->content_lock_classcodes)) {
                $contentLockClasscodeIdResponseArray = array_pluck($request->content_lock_classcodes, 'id');
            } else
                $contentLockClasscodeIdResponseArray = [];
            $contentId = $content->id;
            $contentLockClasscodeIdArray = array_pluck(ContentLockClasscode::where('content_id', '=', $contentId)->get(), 'id');
            $differenceContentLockClasscodeIds = array_diff($contentLockClasscodeIdArray, $contentLockClasscodeIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceContentLockClasscodeIds)
                foreach ($differenceContentLockClasscodeIds as $differenceContentLockClasscodeId) {
                    $contentLockClasscode = ContentLockClasscode::find($differenceContentLockClasscodeId);
                    $contentLockClasscode->delete();
                }

            // Update Content Description
            if (isset($request->content_lock_classcodes))
                foreach ($request->content_lock_classcodes as $lock_classcode) {
                    if (!isset($lock_classcode['id'])) {
                        $content_lock_classcode = new ContentLockClasscode($lock_classcode);
                        $content->content_lock_classcodes()->save($content_lock_classcode);
                    } else {
                        $content_lock_classcode = ContentLockClasscode::find($lock_classcode['id']);
                        $content_lock_classcode->update($lock_classcode);
                    }
                }

            // ---------------------------------------------------
            // Check if Content Assign to Read deleted
            if (isset($request->content_assign_to_reads)) {
                $contentAssignToReadIdResponseArray = array_pluck($request->content_assign_to_reads, 'id');
            } else
                $contentAssignToReadIdResponseArray = [];
            $contentId = $content->id;
            $contentAssignToReadIdArray = array_pluck(ContentAssignToRead::where('content_id', '=', $contentId)->get(), 'id');
            $differenceContentAssignToReadIds = array_diff($contentAssignToReadIdArray, $contentAssignToReadIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceContentAssignToReadIds)
                foreach ($differenceContentAssignToReadIds as $differenceContentAssignToReadId) {
                    $contentAssignToRead = ContentAssignToRead::find($differenceContentAssignToReadId);
                    $contentAssignToRead->delete();
                }

            // Update Content Description
            if (isset($request->content_assign_to_reads))
                foreach ($request->content_assign_to_reads as $assign_to_read) {
                    if (!isset($assign_to_read['id'])) {
                        $content_assign_to_read = new ContentAssignToRead($assign_to_read);
                        $content->content_assign_to_reads()->save($content_assign_to_read);
                    } else {
                        $content_assign_to_read = ContentAssignToRead::find($assign_to_read['id']);
                        $content_assign_to_read->update($assign_to_read);
                    }
                }

            // ---------------------------------------------------
            // Check if Content Grade deleted
            if (isset($request->content_grades)) {
                $contentGradeIdResponseArray = array_pluck($request->content_grades, 'id');
            } else
                $contentGradeIdResponseArray = [];
            $contentId = $content->id;
            $contentGradeIdArray = array_pluck(ContentGrade::where('content_id', '=', $contentId)->get(), 'id');
            $differenceContentGradeIds = array_diff($contentGradeIdArray, $contentGradeIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceContentGradeIds)
                foreach ($differenceContentGradeIds as $differenceContentGradeId) {
                    $contentGrade = ContentGrade::find($differenceContentGradeId);
                    $contentGrade->delete();
                }

            // Update Content Grade
            if (isset($request->content_grades))
                foreach ($request->content_grades as $grade) {
                    if (!isset($grade['id'])) {
                        $content_grade = new ContentGrade($grade);
                        $content->content_grades()->save($content_grade);
                    } else {
                        $content_grade = ContentGrade::find($grade['id']);
                        $content_grade->update($grade);
                    }
                }
            // ---------------------------------------------------
            // Check if Content Board deleted
            if (isset($request->content_boards)) {
                $contentBoardIdResponseArray = array_pluck($request->content_boards, 'id');
            } else
                $contentBoardIdResponseArray = [];
            $contentId = $content->id;
            $contentBoardIdArray = array_pluck(ContentBoard::where('content_id', '=', $contentId)->get(), 'id');
            $differenceContentBoardIds = array_diff($contentBoardIdArray, $contentBoardIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceContentBoardIds)
                foreach ($differenceContentBoardIds as $differenceContentBoardId) {
                    $contentBoard = ContentBoard::find($differenceContentBoardId);
                    $contentBoard->delete();
                }

            // Update Content Board
            if (isset($request->content_boards))
                foreach ($request->content_boards as $board) {
                    if (!isset($board['id'])) {
                        $content_board = new ContentBoard($board);
                        $content->content_boards()->save($content_board);
                    } else {
                        $content_board = ContentBoard::find($board['id']);
                        $content_board->update($board);
                    }
                }

            // ---------------------------------------------------
            // Check if Content InfoBoard deleted
            if (isset($request->content_info_boards)) {
                $contentInfoBoardIdResponseArray = array_pluck($request->content_info_boards, 'id');
            } else
                $contentInfoBoardIdResponseArray = [];
            $contentId = $content->id;
            $contentInfoBoardIdArray = array_pluck(ContentInfoBoard::where('content_id', '=', $contentId)->get(), 'id');
            $differenceContentInfoBoardIds = array_diff($contentInfoBoardIdArray, $contentInfoBoardIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceContentInfoBoardIds)
                foreach ($differenceContentInfoBoardIds as $differenceContentInfoBoardId) {
                    $contentInfoBoard = ContentInfoBoard::find($differenceContentInfoBoardId);
                    $contentInfoBoard->delete();
                }

            // Update Content Info Board
            if (isset($request->content_info_boards))
                foreach ($request->content_info_boards as $info_board) {
                    if (!isset($info_board['id'])) {
                        $content_info_board = new ContentInfoBoard($info_board);
                        $content->content_info_boards()->save($content_info_board);
                    } else {
                        $content_info_board = ContentInfoBoard::find($info_board['id']);
                        $content_info_board->update($info_board);
                    }
                }

            // ---------------------------------------------------
            // Check if Content School deleted
            if (isset($request->content_schools)) {
                $contentSchoolIdResponseArray = array_pluck($request->content_schools, 'id');
            } else
                $contentSchoolIdResponseArray = [];
            $contentId = $content->id;
            $contentSchoolIdArray = array_pluck(ContentSchool::where('content_id', '=', $contentId)->get(), 'id');
            $differenceContentSchoolIds = array_diff($contentSchoolIdArray, $contentSchoolIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceContentSchoolIds)
                foreach ($differenceContentSchoolIds as $differenceContentSchoolId) {
                    $contentSchool = ContentSchool::find($differenceContentSchoolId);
                    $contentSchool->delete();
                }

            // Update Content School
            if (isset($request->content_schools))
                foreach ($request->content_schools as $school) {
                    if (!isset($school['id'])) {
                        $content_school = new ContentSchool($school);
                        $content->content_schools()->save($content_school);
                    } else {
                        $content_school = ContentSchool::find($school['id']);
                        $content_school->update($school);
                    }
                }

            // ---------------------------------------------------
        }

        $content->content_categories = $content->content_categories;
        $content->content_subjects = $content->content_subjects;
        $content->content_medias = $content->content_medias;
        $content->content_descriptions = $content->content_descriptions;
        $content->content_hidden_classcodes = $content->content_hidden_classcodes;
        $content->content_lock_classcodes = $content->content_lock_classcodes;
        $content->content_assign_to_reads = $content->content_assign_to_reads;
        $content->content_grades = $content->content_grades;
        $content->content_boards = $content->content_boards;
        $content->content_info_boards = $content->content_info_boards;
        $content->content_schools = $content->content_schools;
        return response()->json([
            'data'  =>  $content
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Content $content)
    {
        $user = Auth::user();
        $user_role = $user->roles[0]->name;
        $user_id = $user->id;
        if ($user_role == 'STUDENT') {
            $user_classcodes =  UserClasscode::where('user_id', $user_id)->get();
            $content_locks = [];

            foreach ($user_classcodes as $key => $classcode) {
                $content_locks = $content->content_lock_classcodes()->where('content_lock_classcodes.classcode_id', $classcode->classcode_id)->get();
                foreach ($content_locks as $key => $content_lock) {
                    $content_description = $content->content_descriptions()->where('content_descriptions.level', $content_lock->level)->get();
                    $content->content_descriptions = $content_description;
                    $content->content_subjects = $content->content_subjects;
                    $content->content_medias = $content->content_medias;
                    $content->content_metadatas = $content->content_metadatas;
                    $content->content_hidden_classcodes = $content->content_hidden_classcodes;
                    $content->content_lock_classcodes = $content->content_lock_classcodes;
                    $content->content_assign_to_reads = $content->content_assign_to_reads;
                }
            }
        }
        $content->content_categories = $content->content_categories;
        $content->content_subjects = $content->content_subjects;
        $content->content_medias = $content->content_medias;
        $content->content_metadatas = $content->content_metadatas;
        // Sorting Descending by level
        // $descriptions = $content->content_descriptions->toArray();
        // usort($descriptions, function ($a, $b) {
        //     return $b['level'] - $a['level'];
        // });
        // $content['content_descriptionsad'] = $descriptions;
        $content->content_descriptions = $content->content_descriptions;
        $content->content_hidden_classcodes = $content->content_hidden_classcodes;
        $content->content_lock_classcodes = $content->content_lock_classcodes;
        $content->content_assign_to_reads = $content->content_assign_to_reads;
        $content->content_grades = $content->content_grades;
        $content->content_boards = $content->content_boards;
        $content->content_info_boards = $content->content_info_boards;
        $content->content_schools = $content->content_schools;
        return response()->json([
            'data'  =>  $content
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Content $content)
    {
        $request->validate([
            'content_name'  =>  'required',
        ]);

        $content->update($request->all());

        return response()->json([
            'data'  =>  $content
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $content = Content::find($id);
        $content->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }

    public function assigned_to_read_articles()
    {
        $user = Auth::user();
        $user_role = $user->roles[0]->name;
        $user_classcodes =  UserClasscode::where('user_id', $user->id)->get();
        $user_classcode_array = array_column($user_classcodes->toArray(), 'classcode_id');
        $currentDate = date_create(date('Y-m-d'));
        $contents = Content::with('content_subjects', 'content_medias', 'content_reads', 'content_descriptions', 'content_hidden_classcodes', 'content_grades', 'content_boards');

        $contents = $contents->whereHas('content_assign_to_reads', function ($c) use ($user_classcode_array) {
            $c->whereIn('classcode_id', $user_classcode_array);
        });
        if (request()->subject_id) {
            $subject = Subject::find(request()->subject_id);
            $contents = $contents->whereHas('content_subjects', function ($c) {
                $c->where('subject_id', '=', request()->subject_id);
            });
            Search::create([
                'company_id' =>  Auth::user()->companies[0]->id,
                'user_id'   =>      Auth::user()->id,
                'search_type'   =>  'SUBJECT',
                'search'        =>  $subject->name
            ]);
        }
        if (request()->search_keyword) {
            $contents = $contents
                ->where('content_type', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('content_name', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('created_at', 'LIKE', '%' . request()->search_keyword . '%');

            Search::create([
                'company_id' =>  Auth::user()->companies[0]->id,
                'user_id'   =>      Auth::user()->id,
                'search_type'   =>  'KEYWORD',
                'search'        =>  request()->search_keyword
            ]);
        }
        if (request()->date_filter) {
            $contents = $contents
                ->Where('created_at', 'LIKE', '%' . request()->date_filter . '%');
        }
        if (request()->type) {
            $contents = $contents
                ->Where('content_type', request()->type);
        }
        if (request()->category_id) {
            $category = Category::find(request()->category_id);
            $contents = $contents->whereHas('content_categories', function ($c) {
                $c->where('category_id', '=', request()->category_id);
            });
            Search::create([
                'company_id' =>  Auth::user()->companies[0]->id,
                'user_id'   =>      Auth::user()->id,
                'search_type'   =>  'CATEGORY',
                'search'        =>  $category->name
            ]);
        }
        $contents = $contents->latest()->get();
        if ($user_role == 'STUDENT') {
            // If Role is Student// Show Filtered Content
            $filtered_contents = [];
            foreach ($contents as $key => $content) {
                $content_assign_to_read = $content->content_assign_to_reads;
                $endDate = 0;
                if (sizeof($content_assign_to_read) > 0) {
                    $endDate = date_create($content_assign_to_read[0]->due_date);
                    $endDiff = date_diff($currentDate, $endDate)->format("%R%a");
                }
                $isDue = $endDiff < 0 ? true : false;
                if ($isDue == false) {
                    $content_hidden_classcodes = $content->content_hidden_classcodes;
                    $hidden_classcode_array = array_column($content_hidden_classcodes->toArray(), 'classcode_id');
                    if (!array_intersect($user_classcode_array, $hidden_classcode_array)) {
                        $content['assign_to_read'] = true;
                        $filtered_contents[] = $content;
                    }
                }
            }
            $contents = $filtered_contents;
        }
        $article_contents = [];
        $infographic_contents = [];
        $video_contents = [];
        $CategoryWiseContent = [];
        foreach ($contents as $key => $content) {
            // Random Subject Image 
            $image_Array = [];
            $content->subject_image = "";
            if (sizeof($content->content_subjects)) {
                for ($i = 1; $i < 6; $i++) {
                    $name = "imagepath_" . $i;
                    if ($content->content_subjects[0]->subject->$name) {
                        $image_Array[] = $content->content_subjects[0]->subject->$name;
                    }
                }
                $rand_subject_image = array_rand(
                    $image_Array,
                    1
                );
                $content->subject_image = $image_Array[$rand_subject_image];
            }
            // Content type Wise
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
                    # code...
                    break;
            }
            // Category Wise  
            if (sizeOf($content->content_categories)) {
                // Select First Category 
                $category = $content->content_categories[0]->category;
                $category_key = array_search($category->id, array_column($CategoryWiseContent, 'id'));
                if (($category_key != null || $category_key !== false)) {
                    // Increase Content Count 
                    $CategoryWiseContent[$category_key]['count']++;
                    // Add Content in array
                    $CategoryWiseContent[$category_key]['values'][] = $content;
                } else {
                    // Content Added
                    $content_details = [
                        'id' => $category->id,
                        'category' => $category->name,
                        'values' => [$content],
                        'count' => 1,
                    ];
                    $CategoryWiseContent[] = $content_details;
                }
            }
        }
        $content_types = [
            [
                'name' => "ARTICLE",
                'icon' => "mdi-script-text",
                'count' => sizeof($article_contents),
                'values' => $article_contents
            ],
            [
                'name' => "INFOGRAPHIC",
                'icon' => 'mdi-chart-bar',
                'count' => sizeof($infographic_contents),
                'values' => $infographic_contents
            ],
            [
                'name' => "VIDEO",
                'icon' => 'mdi-video-vintage',
                'count' => sizeof($video_contents),
                'values' => $video_contents
            ]
        ];
        return response()->json([
            'data'  =>  $contents,
            'count' =>   sizeof($contents),
            'content_types' => $content_types,
            'CategoryWiseContent' => $CategoryWiseContent,
            'success' =>  true,
        ], 200);
    }

    public function mostContentRead(Request $request)
    {
        $currentDate = Carbon::now();
        $to = date('Y-m-d', strtotime($currentDate));
        $last_week = $currentDate->subDays($currentDate->dayOfWeek + 1);
        $from = date('Y-m-d', strtotime($last_week));
        $user = Auth::user();
        $company = Company::find($user->companies[0]->id);

        $content_reads = $company->content_reads()
            // ->whereBetween('created_at', [$from, $to])
            ->whereDate('created_at', ">=", $from)
            ->whereDate('created_at', "<=", $to)
            ->get();
        $most_popular_articles = [];
        foreach ($content_reads as $key => $content_read) {
            $content = $content_read->content;
            $content->content_reads = $content->content_reads;
            $content_id = $content->id;
            $content_key = array_search($content_id, array_column($most_popular_articles, 'id'));
            if ($content_key != null || $content_key !== false) {
                // Increase Category Looked Count 
                $count = $most_popular_articles[$content_key]['count'];
                $count++;
                $most_popular_articles[$content_key]['count'] = $count;
            } else {
                // Category Not Added
                $content['count'] = 1;
                $most_popular_articles[] = $content;
            }
        }
        // Sorting Descending by Count
        usort($most_popular_articles, function ($a, $b) {
            return $b['count'] - $a['count'];
        });
        $top_5_content_read = array_slice($most_popular_articles, 0, 4);

        return response()->json([
            'data'    => $top_5_content_read,
            'count'   => sizeof($top_5_content_read),
        ]);
    }

    public function search_mother_articles(Request $request)
    {
        // Search TOI ARTICLE
        $toi_articles = new ToiArticle();
        $toi_articles = $toi_articles->with('contents')->where('word_count', '>', 100)
            ->orderBy('story_date', 'DESC');
        if (request()->search_keyword) {
            $toi_articles = $toi_articles->where('edition_name', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('id', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('story_date', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('headline', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('byline', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('drophead', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('category', 'LIKE', '%' . request()->search_keyword . '%');
        }
        $toi_articles = $toi_articles->get();

        // Search ET ARTICLE
        $et_articles = new EtArticle();
        $et_articles = $et_articles->where('word_count', '>', 100)
            ->orderBy('story_date', 'DESC');
        if (request()->search_keyword) {
            $et_articles = $et_articles->where('edition_name', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('id', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('story_date', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('headline', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('byline', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('drophead', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('category', 'LIKE', '%' . request()->search_keyword . '%');
        }
        $et_articles = $et_articles->get();
        $articles = [...$et_articles, ...$toi_articles];
        return response()->json([
            'data'     =>  $articles,
            'count'    =>   sizeof($articles),
            'success'   =>  true,
        ], 200);
    }
}
