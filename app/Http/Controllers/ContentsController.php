<?php

namespace App\Http\Controllers;

use App\Content;
use App\ContentAssignToRead;
use App\ContentDescription;
use App\ContentHiddenClasscode;
use App\ContentLockClasscode;
use App\ContentMedia;
use App\ContentSubject;
use Illuminate\Http\Request;

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
        $contents = Content::with('content_subjects', 'content_medias', 'content_reads');
        if (request()->subject_id) {
            $contents = $contents->whereHas('content_subjects', function ($c) {
                $c->where('subject_id', '=', request()->subject_id);
            });
        }
        if (request()->search_keyword) {
            $contents = $contents
                ->where('content_type', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('content_name', 'LIKE', '%' . request()->search_keyword . '%')
                ->orWhere('created_at', 'LIKE', '%' . request()->search_keyword . '%');
        }
        if (request()->date_filter) {
            $contents = $contents
                ->Where('created_at', 'LIKE', '%' . request()->date_filter . '%');
        }
        $contents = $contents->get();
        return response()->json([
            'data'  =>  $contents,
            'count' =>   sizeof($contents),
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
        }

        $content->content_subjects = $content->content_subjects;
        $content->content_medias = $content->content_medias;
        $content->content_descriptions = $content->content_descriptions;
        $content->content_hidden_classcodes = $content->content_hidden_classcodes;
        $content->content_lock_classcodes = $content->content_lock_classcodes;
        $content->content_assign_to_reads = $content->content_assign_to_reads;
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
        $content->content_subjects = $content->content_subjects;
        $content->content_medias = $content->content_medias;
        $content->content_metadatas = $content->content_metadatas;
        $content->content_descriptions = $content->content_descriptions;

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
