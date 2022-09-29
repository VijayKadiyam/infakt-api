<?php

namespace App\Http\Controllers;

use App\Collection;
use Illuminate\Http\Request;

class CollectionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        if ($request->user_id) {
            $collections =  request()->company->collections()
                ->where('user_id', '=', $request->user_id)
                ->where('is_deleted', false)
                ->get();
        } else {

            $collections =  $request->company->collections()
                ->where('is_deleted', false)->get();
            $count = $collections->count();
        }

        return response()->json([
            'data'     =>  $collections,
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
            'collection_name'        =>  'required',
        ]);
        $collection = [];
        $msg = '';
        $existing_collection = request()->company->collections()
            ->where(['user_id' => $request->user_id, 'collection_name' => request()->collection_name])
            ->first();
        if (!$existing_collection) {
            $collection = new Collection(request()->all());
            $request->company->collections()->save($collection);
        } else {
            $msg = request()->collection_name . ' already exist.';
        }



        return response()->json([
            'data'    =>  $collection,
            'msg' => $msg,
            'success' => true,
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(Collection $collection)
    {
        $collection->collection_contents = $collection->collection_contents;

        return response()->json([
            'data'   =>  $collection,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, Collection $collection)
    {
        $collection->update($request->all());

        return response()->json([
            'data'  =>  $collection
        ], 200);
    }

    public function destroy($id)
    {
        $collection = Collection::find($id);
        $collection->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
