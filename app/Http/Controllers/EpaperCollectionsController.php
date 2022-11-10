<?php

namespace App\Http\Controllers;

use App\EpaperCollection;
use Illuminate\Http\Request;

class EpaperCollectionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        if ($request->user_id) {
            $epaper_collections =  EpaperCollection::
                // $epaper_collections =  request()->company->epaper_collections()
                where('user_id', '=', $request->user_id)
                ->where('is_deleted', false)
                ->get();
        } else {

            $epaper_collections =  EpaperCollection::where('is_deleted', false)->get();
            $count = $epaper_collections->count();
        }
        return response()->json([
            'data'     =>  $epaper_collections,
            'success'     =>  true,
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
        // return 1;
        $request->validate([
            'user_id'        =>  'required',
            'collection_name'        =>  'required',
        ]);
        $epaper_collection = [];
        $msg = '';
        $existing_epaper_collection = EpaperCollection::where(['user_id' => $request->user_id, 'collection_name' => request()->collection_name])
            ->first();
        if (!$existing_epaper_collection) {
            $epaper_collection = new EpaperCollection(request()->all());
            $epaper_collection->save();
        } else {
            $msg = request()->collection_name . ' is already exist. Please try other epaper collection name.';
        }



        return response()->json([
            'data'    =>  $epaper_collection,
            'msg' => $msg,
            'success' => true,
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(EpaperCollection $epaper_collection)
    {

        return response()->json([
            'data'   =>  $epaper_collection,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, EpaperCollection $epaper_collection)
    {
        $epaper_collection->update($request->all());

        return response()->json([
            'data'  =>  $epaper_collection
        ], 200);
    }

    public function destroy($id)
    {
        $epaper_collection = EpaperCollection::find($id);
        $epaper_collection->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
