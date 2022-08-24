<?php

namespace App\Http\Controllers;

use App\ContentClasscode;
use Illuminate\Http\Request;

class ContentClasscodesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        $content_classcodes = $request->company->content_classcodes;
        $count = $content_classcodes->count();

        return response()->json([
            'data'     =>  $content_classcodes,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new content_classcode
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'content_id'        =>  'required',
        ]);

        $content_classcodes = new ContentClasscode(request()->all());
        $request->company->content_classcodes()->save($content_classcodes);

        return response()->json([
            'data'    =>  $content_classcodes
        ], 201);
    }

    /*
     * To view a single content_classcode
     *
     *@
     */
    public function show(ContentClasscode $content_classcode)
    {
        return response()->json([
            'data'   =>  $content_classcode,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a content_classcode
     *
     *@
     */
    public function update(Request $request, ContentClasscode $content_classcode)
    {
        $content_classcode->update($request->all());

        return response()->json([
            'data'  =>  $content_classcode
        ], 200);
    }

    public function destroy($id)
    {
        $content_classcodes = ContentClasscode::find($id);
        $content_classcodes->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
