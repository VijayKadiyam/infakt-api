<?php

namespace App\Http\Controllers;

use App\Classcode;
use App\ContentMetadata;
use App\ContentMetadataClasscode;
use App\Notification;
use Illuminate\Http\Request;

class ContentMetadatasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $content_metadatas = ContentMetadata::all();
        return response()->json([
            'data'  =>  $content_metadatas,
            'count' =>   sizeof($content_metadatas),
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
            'content_id'  =>  'required'
        ]);

        // $content_metadata = new ContentMetadata(request()->all());
        // $content_metadata->save();

        if ($request->id == null || $request->id == '') {
            // Save Content
            $content_metadata = new ContentMetadata(request()->all());
            $content_metadata->save();
            // Save Content Subjects
            if (isset($request->content_metadata_classcodes))
                foreach ($request->content_metadata_classcodes as $metadata_classcodes) {
                    $content_metadata_classcodes = new ContentMetadataClasscode($metadata_classcodes);
                    $content_metadata->content_metadata_classcodes()->save($content_metadata_classcodes);
                    // Create Notification Log
                    // $c = Classcode::find($content_metadata_classcodes->classcode_id);
                    // $students = $c->students;
                    // foreach ($students as $key => $user) {
                    //     $description = "A new $content_metadata->metadata_type has been posted for Classcode[ $c->classcode ].";
                    //     $notification_data = [
                    //         'user_id' => $user->id,
                    //         'description' => $description
                    //     ];
                    //     $notifications = new Notification($notification_data);
                    //     $request->company->notifications()->save($notifications);
                    // }
                }
            // ---------------------------------------------------

        } else {
            // Update Content
            $content_metadata = ContentMetadata::find($request->id);
            $content_metadata->update($request->all());

            // Check if Content Subject deleted
            if (isset($request->content_metadata_classcodes)) {
                $contentMetadataClasscodeIdResponseArray = array_pluck($request->content_metadata_classcodes, 'id');
            } else
                $contentMetadataClasscodeIdResponseArray = [];
            $content_metadata_id = $content_metadata->id;
            $contentMetadataClasscodeIdArray = array_pluck(ContentMetadataClasscode::where('content_id', '=', $content_metadata_id)->get(), 'id');
            $differenceContentMetadataClasscodeIds = array_diff($contentMetadataClasscodeIdArray, $contentMetadataClasscodeIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceContentMetadataClasscodeIds)
                foreach ($differenceContentMetadataClasscodeIds as $differenceContentMetadataClasscodeId) {
                    $contentMetadataClasscode = ContentMetadataClasscode::find($differenceContentMetadataClasscodeId);
                    // $c = Classcode::find($contentMetadataClasscode['classcode_id']);
                    $contentMetadataClasscode->delete();
                    // Create Notification Log
                    // $students = $c->students;
                    // foreach ($students as $key => $user) {
                    //     $description = "An Existing $content_metadata->metadata_type has been removed for Classcode[ $c->classcode ].";
                    //     $notification_data = [
                    //         'user_id' => $user->id,
                    //         'description' => $description
                    //     ];
                    //     $notifications = new Notification($notification_data);
                    //     $request->company->notifications()->save($notifications);
                    // }
                }

            // Update Content Subject
            if (isset($request->content_metadata_classcodes))
                foreach ($request->content_metadata_classcodes as $metadata_classcode) {
                    if (!isset($metadata_classcode['id'])) {
                        $content_metadata_classcode = new ContentMetadataClasscode($metadata_classcode);
                        $content_metadata->content_metadata_classcodes()->save($content_metadata_classcode);
                        // Create Notification Log
                        // $c = Classcode::find($content_metadata_classcode['classcode_id']);
                        // $students = $c->students;
                        // foreach ($students as $key => $user) {
                        //     $description = "A new $content_metadata->metadata_type has been posted for Classcode[ $c->classcode ].";
                        //     $notification_data = [
                        //         'user_id' => $user->id,
                        //         'description' => $description
                        //     ];
                        //     $notifications = new Notification($notification_data);
                        //     $request->company->notifications()->save($notifications);
                        // }
                    } else {
                        $content_metadata_classcode = ContentMetadataClasscode::find($metadata_classcode['id']);
                        $content_metadata->update($metadata_classcode);
                    }
                }

            // ---------------------------------------------------

        }

        $content_metadata->content_metadata_classcodes = $content_metadata->content_metadata_classcodes;

        return response()->json([
            'data'  =>  $content_metadata
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ContentMetadata $content_metadata)
    {
        return response()->json([
            'data'  =>  $content_metadata
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContentMetadata $content_metadata)
    {
        $request->validate([
            'content_id'  =>  'required',
        ]);

        $content_metadata->update($request->all());

        return response()->json([
            'data'  =>  $content_metadata
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
        $content_metadata = ContentMetadata::find($id);
        $content_metadata->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
