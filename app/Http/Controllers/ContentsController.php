<?php

namespace App\Http\Controllers;

use App\Content;
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

        return response()->json([
            // 'collections'           =>  $collectionsResponse->getData()->data,
            'users'                 =>  $usersResponse->getData()->data,
            'subjects'              =>  $subjectsResponse->getData()->data,
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contents = Content::with('written_by', 'content_subjects', 'content_medias','content_reads');
        if (request()->subject_id) {
            $contents = $contents->whereHas('content_subjects', function ($c) {
                $c->where('subject_id', '=', request()->subject_id);
            });
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
        }
        
        $content->content_subjects = $content->content_subjects;
        $content->content_medias = $content->content_medias;
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
