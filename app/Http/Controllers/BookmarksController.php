<?php

namespace App\Http\Controllers;

use App\Bookmark;
use Illuminate\Http\Request;

class BookmarksController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        if ($request->user_id) {
            $bookmarks = request()->company->bookmarks()
                ->where('user_id', '=', $request->user_id)
                ->get();
        } else {

            $bookmarks = request()->company->bookmarks;
            $count = $bookmarks->count();
        }

        return response()->json([
            'data'     =>  $bookmarks,
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

        $bookmark = new Bookmark(request()->all());
        $request->company->bookmarks()->save($bookmark);

        return response()->json([
            'data'    =>  $bookmark
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(Bookmark $bookmark)
    {
        return response()->json([
            'data'   =>  $bookmark,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, Bookmark $bookmark)
    {
        $bookmark->update($request->all());

        return response()->json([
            'data'  =>  $bookmark
        ], 200);
    }

    public function destroy($id)
    {
        $bookmark = Bookmark::find($id);
        $bookmark->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
