<?php

namespace App\Http\Controllers;

use App\Job;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $jobs = Job::all();
        $count = $jobs->count();

        return response()->json([
            'data'     =>  $jobs,
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
            'title'        =>  'required',
        ]);

        $jobs = new Job(request()->all());
        $jobs->save();

        return response()->json([
            'data'    =>  $jobs
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(Job $job)
    {
        return response()->json([
            'data'   =>  $job,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, Job $job)
    {
        $job->update($request->all());

        return response()->json([
            'data'  =>  $job
        ], 200);
    }

    public function destroy($id)
    {
        $jobs = Job::find($id);
        $jobs->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
