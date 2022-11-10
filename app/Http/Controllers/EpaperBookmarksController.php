<?php

namespace App\Http\Controllers;

use App\EpaperBookmark;
use App\EpaperEpaperBookmark;
use Illuminate\Http\Request;

class EpaperBookmarksController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        if ($request->user_id) {
            $epaper_bookmarks = request()->company->epaper_bookmarks()
                ->where('user_id', '=', $request->user_id)
                ->get();
        } else {

            $epaper_bookmarks = request()->company->epaper_bookmarks;
            $count = $epaper_bookmarks->count();
        }
        return response()->json([
            'data'     =>  $epaper_bookmarks,
            'count'    =>   $count,
            'success'  => true
        ], 200);
    }

    /*
     * To store a new user_section
     *
     *@
     */
    public function toi_store(Request $request)
    {
        $request->validate([
            'user_id'        =>  'required',
        ]);
        $epaper_bookmark = [];
        $msg = '';
        // TOI Paper
        if (request()->toi_article_id) {
            $toi_existing_epaper_bookmark = request()->company->epaper_bookmarks()
                ->where(['user_id' => request()->toi_article_id, 'toi_article_id' => request()->content_id])->first();
            $epaper_bookmark = [];
            if (!$toi_existing_epaper_bookmark) {
                $epaper_bookmark = new EpaperBookmark(request()->all());
                $request->company->epaper_bookmarks()->save($epaper_bookmark);
            } else {
                $epaper_bookmark = [];
                $msg = 'TOI Epaper Bookmark already exist.';
            }
        }

        return response()->json([
            'data'    =>  $epaper_bookmark,
            'msg' => $msg
        ], 201);
    }

    public function et_store(Request $request)
    {
        $request->validate([
            'user_id'        =>  'required',
        ]);
        $epaper_bookmark = [];
        $msg = '';

        // ET Paper
        $epaper_bookmark = [];
        if (request()->et_article_id) {
            $et_existing_epaper_bookmark = request()->company->epaper_bookmarks()
                ->where(['user_id' => request()->user_id, 'et_article_id' => request()->et_article_id])->first();
            if (!isset($et_existing_epaper_bookmark)) {
                $epaper_bookmark = new EpaperBookmark(request()->all());
                $request->company->epaper_bookmarks()->save($epaper_bookmark);
            } else {
                $msg = 'ET Epaper Bookmark already exist.';
            }
        }

        return response()->json([
            'data'    =>  $epaper_bookmark,
            'msg' => $msg
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(EpaperBookmark $epaper_bookmark)
    {
        return response()->json([
            'data'   =>  $epaper_bookmark,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, EpaperBookmark $epaper_bookmark)
    {
        $epaper_bookmark->update($request->all());

        return response()->json([
            'data'  =>  $epaper_bookmark
        ], 200);
    }

    public function destroy($id)
    {
        $epaper_bookmark = EpaperBookmark::find($id);
        $epaper_bookmark->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
