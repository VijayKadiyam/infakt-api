<?php

namespace App\Http\Controllers;

use App\Classcode;
use App\Collection;
use App\CollectionClasscode;
use App\Notification;
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
        if ($request->company_id) {
            $collection_classcodes = $request->company->collection_classcodes();
        } else {
            $collection_classcodes = new CollectionClasscode;
        }
        // return 1;
        if (request()->collection_id) {
            $collection_classcodes = $collection_classcodes->where('collection_id', request()->collection_id);
        }
        $collection_classcodes = $collection_classcodes->get();
        $count = $collection_classcodes->count();
        if (request()->user_role == 'STUDENT') {
            $user_classcodes = UserClasscode::where('user_id', request()->user_id)->get();
            $user_classcode_array = array_column($user_classcodes->toArray(), 'classcode_id');

            // return $user_classcodes;
            $filtered_collection = [];
            foreach ($collection_classcodes as $key => $cc) {
                if (in_array($cc->classcode_id, $user_classcode_array)) {
                    $filtered_collection[] = $cc;
                }
            }
            $collection_classcodes = $filtered_collection;
        }

        foreach ($collection_classcodes as $key => $collection_classcode) {
            $collection_classcode->featured_image_path = '';
            $is_featured_image = false;
            $collection_contents = $collection_classcode->collection->collection_contents;
            foreach ($collection_contents as $key => $cc) {
                if (
                    $cc->content &&
                    $cc->content->featured_image_path != null &&
                    $is_featured_image != true
                ) {
                    $is_featured_image = true;
                    $collection_classcode->featured_image_path = $cc->content->featured_image_path;
                }
            }
        }
        return response()->json([
            'data'     =>  $collection_classcodes,
            'count'    =>   $count,
            'success'  => true
        ], 200);
    }

    /*
     * To store a new collection_classcode
     *
     *@
     */
    public function store_1(Request $request)
    {
        // $request->validate([
        //     'collection_id'        =>  'required',
        // ]);
        $collectionClasscodeIdArray = array_pluck(CollectionClasscode::where('collection_id', '=', $request->collection_id)->get(), 'id');
        if ($collectionClasscodeIdArray)
            foreach ($collectionClasscodeIdArray as $differenceCollectionClasscodeId) {
                $collectionClasscode = CollectionClasscode::find($differenceCollectionClasscodeId);
                $collectionClasscode->delete();
            }

        if (isset($request->collection_classcodes))

            foreach ($request->collection_classcodes as $collecton_classcode) {
                $collection_classcode = new CollectionClasscode($collecton_classcode);
                $strore = $request->company->collection_classcodes()->save($collection_classcode);
            }
        if ($strore) {
            if ($collection_classcode->shared_by_id) {
                $user_classcodes = UserClasscode::where('classcode_id', $collection_classcode->classcode_id)->with('user')->get();
                foreach ($user_classcodes as $key => $uc) {
                    $description = "A new collection shared to you by .";
                    if ($uc->user->roles[0]->name == 'STUDENT') {
                        $user_id = $uc->user->id;
                        $notification_data = [
                            'user_id' => $user_id,
                            'description' => $description
                        ];
                        $notifications = new Notification($notification_data);
                        $request->company->notifications()->save($notifications);
                    }
                }
            }
        }

        return response()->json([
            'data'    =>  $collection_classcode
        ], 201);
    }
    public function store(Request $request)
    {
        $collection = Collection::find($request->collection_id);
        if (isset($request->collection_classcodes)) {
            $classcodeIdResponseArray = array_pluck($request->collection_classcodes, 'id');
        } else
            $classcodeIdResponseArray = [];
        $collectionId = $collection->id;
        $classcodeIdArray = array_pluck(CollectionClasscode::where('collection_id', '=', $collectionId)->get(), 'id');
        $differenceCollectionClasscodeIds = array_diff($classcodeIdArray, $classcodeIdResponseArray);
        // Delete which is there in the database but not in the response
        if ($differenceCollectionClasscodeIds)
            foreach ($differenceCollectionClasscodeIds as $differenceCollectionClasscodeId) {
                $classcode = CollectionClasscode::find($differenceCollectionClasscodeId);
                $c = Classcode::find($classcode['classcode_id']);
                $students = $c->students;
                $classcode->delete();
                // Create Notification Log
                foreach ($students as $key => $user) {
                    $description = "An existing Collection [ $collection->collection_name ] has been removed for Classcode [ $c->classcode ].";
                    $notification_data = [
                        'user_id' => $user->id,
                        'description' => $description
                    ];
                    $notifications = new Notification($notification_data);
                    $request->company->notifications()->save($notifications);
                }
            }

        // Update Collection Classcode
        if (isset($request->collection_classcodes))
            foreach ($request->collection_classcodes as $classcode) {
                $classcode['company_id'] = $collection->company_id;
                if (!isset($classcode['id'])) {
                    $collection_classcode = new CollectionClasscode($classcode);
                    $collection->collection_classcodes()->save($collection_classcode);
                    // Create Notification Log
                    $c = Classcode::find($classcode['classcode_id']);
                    $students = $c->students;
                    foreach ($students as $key => $user) {
                        $description = "A new Collection [ $collection->collection_name ] has been shared to you for Classcode [ $c->classcode ].";
                        $notification_data = [
                            'user_id' => $user->id,
                            'description' => $description
                        ];
                        $notifications = new Notification($notification_data);
                        $request->company->notifications()->save($notifications);
                    }
                } else {
                    $collection_classcode = CollectionClasscode::find($classcode['id']);
                    $collection_classcode->update($classcode);
                }
            }
        // ---------------------------------------------------
        $collection->collection_classcodes = $collection->collection_classcodes;
        return response()->json([
            'data'    =>  $collection
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
