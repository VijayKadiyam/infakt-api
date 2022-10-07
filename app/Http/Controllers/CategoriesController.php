<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $categories = Category::all();
        $count = $categories->count();

        return response()->json([
            'data'     =>  $categories,
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
            'name'        =>  'required',
        ]);

        $categories = new Category(request()->all());
        $categories->save();

        return response()->json([
            'data'    =>  $categories
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(Category $category)
    {
        return response()->json([
            'data'   =>  $category,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, Category $category)
    {
        $category->update($request->all());

        return response()->json([
            'data'  =>  $category
        ], 200);
    }

    public function destroy($id)
    {
        $categories = Category::find($id);
        $categories->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
