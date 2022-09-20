<?php

namespace App\Http\Controllers;

use App\CollectionClasscode;
use App\UserClasscode;
use Illuminate\Http\Request;

class CollectionClasscodesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        $collection_classcodes = $request->company->collection_classcodes;
        if (request()->collection_id) {
            $collection_classcodes = $collection_classcodes->where('collection_id', request()->collection_id);
        }
        if (request()->user_role == 'STUDENT') {
            $user_classcodes = UserClasscode::where('user_id', request()->user_id)->get();
            foreach ($user_classcodes as $key => $classcode) {
                $collection_classcodes = $collection_classcodes->where('classcode_id', $classcode->classcode_id);
                $count = $collection_classcodes->count();
                return response()->json([
                    'data'     =>  $collection_classcodes,
                    'count'    =>   $count
                ], 200);
            }
        }
        $count = $collection_classcodes->count();

        return response()->json([
            'data'     =>  $collection_classcodes,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new collection_classcode
     *
     *@
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'collection_id'        =>  'required',
        // ]);

        // $collection_classcodes = new CollectionClasscode(request()->all());
        // $request->company->collection_classcodes()->save($collection_classcodes);

        if (isset(request()->collection_classcodes)) {
            foreach ($request->collection_classcodes as $collection_classcode) {
                $collection_classcodes = new CollectionClasscode($collection_classcode);
                $request->company->collection_classcodes()->save($collection_classcodes);
            }
        }

        return response()->json([
            'data'    =>  $collection_classcodes
        ], 201);
    }

    /*
     * To view a single collection_classcode
     *
     *@
     */
    public function show(CollectionClasscode $collection_classcode)
    {
        return response()->json([
            'data'   =>  $collection_classcode,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a collection_classcode
     *
     *@
     */
    public function update(Request $request, CollectionClasscode $collection_classcode)
    {
        $collection_classcode->update($request->all());

        return response()->json([
            'data'  =>  $collection_classcode
        ], 200);
    }

    public function destroy($id)
    {
        $collection_classcodes = CollectionClasscode::find($id);
        $collection_classcodes->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
