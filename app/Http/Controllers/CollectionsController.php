<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Collection;
use App\CollectionContent;
use App\Notification;
use App\UserClasscode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $collections =  Collection::where('user_id', '=', $request->user_id)
                ->where('is_deleted', false)
                ->get();
        } elseif ($request->all_collection_by_AT_IT) {
            $collections =  Collection::where('company_id', '=', null)
                ->where('is_deleted', false)
                ->where('status', true)
                ->get();
        } elseif ($request->is_pending_collection) {
            $collections =  Collection::where('company_id', '=', null)
                ->where('is_deleted', false)
                ->where('status', false)
                ->get();
        } else {
            $collections =  $request->company->collections()
                ->where('is_deleted', false)->get();
            $count = $collections->count();
        }

        // return $collections;

        foreach ($collections as $key => $collection) {
            $collection->featured_image_path = '';
            $is_featured_image = false;
            $collection_contents = $collection->collection_contents;
            foreach ($collection_contents as $key => $cc) {
                if (
                    $cc->content &&
                    $cc->content->featured_image_path != null &&
                    $is_featured_image != true
                ) {
                    $is_featured_image = true;
                    $collection->featured_image_path = $cc->content->featured_image_path;
                }
            }
        }

        return response()->json([
            'data'     =>  $collections,
            'success'     =>  true,
            'count'    =>   sizeof($collections)
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
        $user = Auth::user();
        $user_role = $user->roles[0]->name;
        $existing_collection = Collection::where(['user_id' => $request->user_id, 'collection_name' => request()->collection_name])
            ->first();
        if (!$existing_collection) {
            if ($user_role == "ACADEMIC TEAM") {
                // If role is Academic Team, Then All Collection are approved 
                $status = true;
            } else if ($user_role == "INFAKT TEACHER") {
                // If role is INFAKT TEACHER, Then All Collection are in pending 
                $status = false;
                $description = "A new collection is created. Waiting for your approval.";
                // fetch Academic Team 
                $usersController = new UsersController();
                $request->request->add(['role_id' => 6]);
                $users = $usersController->index($request)->getData()->data;
                foreach ($users as $key => $user) {
                    $notification_data = [
                        'user_id' => $user->id,
                        'description' => $description
                    ];
                    $notifications = new Notification($notification_data);
                    $notifications->save();
                }
            } else {
                $status = true;
                $request->request->add(['company_id' => $user->companies[0]->id]);
            }
            $request->request->add(['status' => $status]);
            $collection = new Collection($request->all());
            $collection->save();

            if ($collection->id) {
                $collection_contents  = [];
                $msg = '';
                $existing_collection_content = CollectionContent::where(['collection_id' => $collection->id, 'content_id' => request()->content_id])->first();
                if (!$existing_collection_content) {
                    $data = [
                        'collection_id' => $collection->id,
                        'content_id' => request()->content_id,
                    ];
                    $collection_contents = new CollectionContent($data);
                    $collection_contents->save();

                    $user = Auth::user();
                    $user_role = $user->roles[0]->name;
                    if ($user_role == "INFAKT TEACHER") {
                        // If role is INFAKT TEACHER, Then All Collection are in pending
                        $collection = Collection::find($collection->id)->update(['status' => false]);
                    }
                } else {
                    $msg = 'Content already exist';
                }
            }
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
        $assignment_id = request()->assignment_id;
        $collection->collection_contents = $collection->collection_contents;
        $user_role   =     Auth::user()->roles[0]->name;
        if ($user_role == 'STUDENT') {
            $user_id = Auth::user()->id;
            $user_classcodes = UserClasscode::where('user_id', $user_id)->get();
            $user_classcode_array = array_column($user_classcodes->toArray(), "classcode_id");
            $currentDate = date_create(date('Y-m-d'));

            foreach ($collection->collection_contents as $key => $collection_content) {
                if ($assignment_id) {
                    $assignment = Assignment::find($assignment_id);
                    $assignment_classcodes = $assignment->my_assignment_classcodes()->whereIn('classcode_id', $user_classcode_array)->get();
                    foreach ($assignment_classcodes as $key => $assignmentClasscode) {
                        $startDate = date_create($assignmentClasscode->start_date);
                        $endDate = date_create($assignmentClasscode->end_date);
                        $startDiff = date_diff($currentDate, $startDate)->format("%R%a");
                        $endDiff = date_diff($currentDate, $endDate)->format("%R%a");
                        $inProgress = $startDiff < 0 && $endDiff >= 0 ? true : false;
                        if ($inProgress == true) {
                            $collection->student_instructions = $assignment->student_instructions;
                        }
                    }
                }
                // Random Subject Image 
                $image_Array = [];
                $collection_content->content->subject_image = "";
                if (sizeOf($collection_content->content->content_subjects)) {
                    for ($i = 1; $i < 6; $i++) {
                        $name = "imagepath_" . $i;
                        if ($collection_content->content->content_subjects[0]->subject->$name) {
                            $image_Array[] = $collection_content->content->content_subjects[0]->subject->$name;
                        }
                    }
                    $rand_subject_image = array_rand(
                        $image_Array,
                        1
                    );
                    $collection_content->content->subject_image = $image_Array[$rand_subject_image];
                }
            }
        }
        $collection->assignments = $collection->assignments;

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
        if ($collection->status != $request->status) {
            // If Existing Is Approved Status differs from the request
            if ($request->status == 1) {
                $description = "Hurray! Collection [ $collection->collection_name ] has been approved.";
            }
            if ($request->status == 2) {
                $description = "Oops, Looks like your Collection [ $collection->collection_name ] has been rejected by the Academic Team. Kindly review the remark.";
            }
            $notification_data = [
                'user_id' => $collection->user_id,
                'description' => $description
            ];
            $notifications = new Notification($notification_data);
            $notifications->save();
        }
        $user = Auth::user();
        $user_role = $user->roles[0]->name;
        if ($user_role == "INFAKT TEACHER") {
            // If role is INFAKT TEACHER, Then All Collection are in pending 
            $status = false;
            $request->request->add(['status' => $status]);
        }

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
