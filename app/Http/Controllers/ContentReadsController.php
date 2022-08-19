<?php

namespace App\Http\Controllers;

use App\ContentRead;
use Illuminate\Http\Request;

class ContentReadsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        if ($request->user_id) {
            $content_reads = request()->company->content_reads()
                ->where('user_id', '=', $request->user_id)
                ->get();
        } else {

            $content_reads = request()->company->content_reads;
            $count = $content_reads->count();
        }

        return response()->json([
            'data'     =>  $content_reads,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new user_section
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        =>  'required',
        ]);

        $content_read = new ContentRead(request()->all());
        $request->company->content_reads()->save($content_read);

        return response()->json([
            'data'    =>  $content_read
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(ContentRead $content_read)
    {
        return response()->json([
            'data'   =>  $content_read,

        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, ContentRead $content_read)
    {
        $content_read->update($request->all());

        return response()->json([
            'data'  =>  $content_read
        ], 200);
    }

    public function destroy($id)
    {
        $content_read = ContentRead::find($id);
        $content_read->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
