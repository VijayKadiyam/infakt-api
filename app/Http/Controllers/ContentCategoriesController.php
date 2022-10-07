<?php

namespace App\Http\Controllers;

use App\ContentCategory;
use Illuminate\Http\Request;

class ContentCategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $Content_categories = ContentCategory::all();
        $count = $Content_categories->count();

        return response()->json([
            'data'     =>  $Content_categories,
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
            'content_id'        =>  'required',
        ]);

        $Content_categories= new ContentCategory(request()->all());
        $Content_categories->save();

        return response()->json([
            'data'    =>  $Content_categories
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(ContentCategory $content_category)
    {
        return response()->json([
            'data'   =>  $content_category,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, ContentCategory $content_category)
    {
        $content_category->update($request->all());

        return response()->json([
            'data'  =>  $content_category
        ], 200);
    }

    public function destroy($id)
    {
        $Content_categories= ContentCategory::find($id);
        $Content_categories->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
