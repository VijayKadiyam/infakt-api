<?php

namespace App\Http\Controllers;

use App\CollectionContent;
use Illuminate\Http\Request;

class CollectionContentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $collection_contents = CollectionContent::all();
        $count = $collection_contents->count();

        return response()->json([
            'data'     =>  $collection_contents,
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
            'collection_id'        =>  'required',
        ]);

        $collection_contents = new CollectionContent(request()->all());
        $collection_contents->save();

        return response()->json([
            'data'    =>  $collection_contents
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(CollectionContent $collection_content)
    {
        return response()->json([
            'data'   =>  $collection_content,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, CollectionContent $collection_content)
    {
        $collection_content->update($request->all());

        return response()->json([
            'data'  =>  $collection_content
        ], 200);
    }

    public function destroy($id)
    {
        $collection_contents = CollectionContent::find($id);
        $collection_contents->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
