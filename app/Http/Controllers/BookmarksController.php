<?php

namespace App\Http\Controllers;

use App\Bookmark;
use Illuminate\Http\Request;

class BookmarksController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        if ($request->user_id) {
            $bookmarks = Bookmark::where('user_id', '=', $request->user_id)
                ->get();
        } else {
            $bookmarks = request()->company->bookmarks;
            $count = $bookmarks->count();
        }
        return response()->json([
            'data'     =>  $bookmarks,
            'count'    =>   $count,
            'success'  => true
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
        $bookmark = [];
        $msg = '';
        $existing_bookmark = Bookmark::where(['user_id' => request()->user_id, 'content_id' => request()->content_id])->first();
        $bookmark = [];
        if (!$existing_bookmark) {
            if ($request->company_id) {
                $bookmark = new Bookmark(request()->all());
                $request->company->bookmarks()->save($bookmark);
            } else {
                $bookmark = new Bookmark(request()->all());
                $bookmark->save();
            }
        } else {
            $msg = 'Bookmark already exist.';
        }
        return response()->json([
            'data'    =>  $bookmark,
            'msg' => $msg
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
        $bookmark = Bookmark::find($id)->update(['is_deleted' => true]);
        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }

    // public function clear()
    // {
    //     $id = request()->notification_id;
    //     $notifications = Notification::find($id)->update(['is_deleted' => true]);
    //     return response()->json([
    //         'message' =>  'Cleared All Messages'
    //     ], 200);
    // }
}
