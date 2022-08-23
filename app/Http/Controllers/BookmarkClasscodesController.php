<?php

namespace App\Http\Controllers;

use App\BookmarkClasscode;
use Illuminate\Http\Request;

class BookmarkClasscodesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        $bookmark_classcodes = $request->company->bookmark_classcodes;
        $count = $bookmark_classcodes->count();

        return response()->json([
            'data'     =>  $bookmark_classcodes,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new bookmark_classcode
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'bookmark_id'        =>  'required',
        ]);

        $bookmark_classcodes = new BookmarkClasscode(request()->all());
        $request->company->bookmark_classcodes()->save($bookmark_classcodes);

        return response()->json([
            'data'    =>  $bookmark_classcodes
        ], 201);
    }

    /*
     * To view a single bookmark_classcode
     *
     *@
     */
    public function show(BookmarkClasscode $bookmark_classcode)
    {
        return response()->json([
            'data'   =>  $bookmark_classcode,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a bookmark_classcode
     *
     *@
     */
    public function update(Request $request, BookmarkClasscode $bookmark_classcode)
    {
        $bookmark_classcode->update($request->all());

        return response()->json([
            'data'  =>  $bookmark_classcode
        ], 200);
    }

    public function destroy($id)
    {
        $bookmark_classcodes = BookmarkClasscode::find($id);
        $bookmark_classcodes->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
