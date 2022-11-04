<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Collection;
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
