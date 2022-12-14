<?php

namespace App\Http\Controllers;

use App\Collection;
use App\CollectionContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionContentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api, company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $collection_contents = CollectionContent::all();
        $count = $collection_contents->count();

        return response()->json([
            'data'     =>  $collection_contents,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new user_section
     *
     *@
     */
    public function store(Request $request, Collection  $collection)
    {
        // return request()->all();
        $request->validate([
            'collection_id'        =>  'required',
        ]);
        $collection_contents  = [];
        $msg = '';
        $existing_collection_content = CollectionContent::where(['collection_id' => request()->collection_id, 'content_id' => request()->content_id])->first();
        if (!$existing_collection_content) {
            $collection_contents = new CollectionContent(request()->all());
            $collection_contents->save();
            $user = Auth::user();
            $user_role = $user->roles[0]->name;
            if ($user_role == "INFAKT TEACHER") {
                // If role is INFAKT TEACHER, Then All Collection are in pending
                $collection = Collection::find(request()->collection_id)->update(['status' => false]);
            }
        } else {
            $msg = 'Content already exist';
        }


        return response()->json([
            'data'    =>  $collection_contents,
            'msg' => $msg
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(CollectionContent $collection_content)
    {
        return response()->json([
            'data'   =>  $collection_content,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, CollectionContent $collection_content)
    {
        $collection_content->update($request->all());

        return response()->json([
            'data'  =>  $collection_content
        ], 200);
    }

    public function destroy($id)
    {
        $collection_contents = CollectionContent::find($id);
        $collection_contents->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
