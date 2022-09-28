<?php

namespace App\Http\Controllers;

use App\Content;
use App\ContentAssignToRead;
use App\ContentBoard;
use App\ContentDescription;
use App\ContentGrade;
use App\ContentHiddenClasscode;
use App\ContentInfoBoard;
use App\ContentLockClasscode;
use App\ContentMedia;
use App\ContentSchool;
use App\ContentSubject;
use App\Search;
use App\Subject;
use App\UserClasscode;
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
        // $collectionsController = new CollectionsController();
        // $request->request->add(['user_id' => $request->user_id]);
        // $collectionsResponse = $collectionsController->index($request);
        $usersController = new UsersController();
        $request->request->add(['role_id' => 4]);
        $usersResponse = $usersController->index($request);

        $subjectsController = new SubjectsController();
        $subjectsResponse = $subjectsController->index($request);

        $gradesController = new GradesController();
        $gradesResponse = $gradesController->index($request);

        $boardsController = new BoardsController();
        $boardsResponse = $boardsController->index($request);

        $schoolsController = new CompaniesController();
        $schoolsResponse = $schoolsController->index($request);

        return response()->json([
            // 'collections'           =>  $collectionsResponse->getData()->data,
            'users'     =>  $usersResponse->getData()->data,
            'subjects'  =>  $subjectsResponse->getData()->data,
            'grades'    =>  $gradesResponse->getData()->data,
            'boards'    =>  $boardsResponse->getData()->data,
            'schools'   =>  $schoolsResponse->getData()->data,
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contents = Content::with('content_subjects', 'content_medias', 'content_reads', 'content_descriptions', 'content_hidden_classcodes');
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
        $contents = $contents->get();

        $user_role = request()->roleName;
        $user_id = request()->user_id;

        if ($user_role == 'STUDENT') {
            // If Role is Student// Show Filtered Content
            $user_classcodes =  UserClasscode::where('user_id', $user_id)->get();
            $user_classcode_array = array_column($user_classcodes->toArray(), 'classcode_id');
            $filtered_contents = [];
            foreach ($contents as $key => $content) {
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
        foreach ($contents as $key => $content) {
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
            'content_name'  =>  'required'
        ]);
        if ($request->id == null || $request->id == '') {
            // Save Content
            $content = new Content(request()->all());
            $content->save();
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
                foreach ($request->content_descriptions as $description) {
                    $description = new ContentDescription($description);
                    $content->content_descriptions()->save($description);
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
        $content->content_subjects = $content->content_subjects;
        $content->content_medias = $content->content_medias;
        $content->content_metadatas = $content->content_metadatas;
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
}
