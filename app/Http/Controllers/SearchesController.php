<?php

namespace App\Http\Controllers;

use App\Search;
use Illuminate\Http\Request;

class SearchesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        $searches = $request->company->searches;
        $count = $searches->count();

        return response()->json([
            'data'     =>  $searches,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new search
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        =>  'required',
        ]);

        $searches = new Search(request()->all());
        $request->company->searches()->save($searches);

        return response()->json([
            'data'    =>  $searches
        ], 201);
    }

    /*
     * To view a single search
     *
     *@
     */
    public function show(Search $search)
    {
        return response()->json([
            'data'   =>  $search,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a search
     *
     *@
     */
    public function update(Request $request, Search $search)
    {
        $search->update($request->all());

        return response()->json([
            'data'  =>  $search
        ], 200);
    }

    public function destroy($id)
    {
        $searches = Search::find($id);
        $searches->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
