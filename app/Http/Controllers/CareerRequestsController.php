<?php

namespace App\Http\Controllers;

use App\CareerRequest;
use Illuminate\Http\Request;

class CareerRequestsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (request()->page && request()->rowsPerPage) {
            $career_requests = new CareerRequest;
            if (request()->search_keyword) {
                $career_requests = $career_requests

                    ->orwhere('name', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('status', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('remarks', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('created_at', 'LIKE', '%' . request()->search_keyword . '%')
                    ->where('is_deleted', false);
            }
            $count = $career_requests->count();
            $career_requests = $career_requests->paginate(request()->rowsPerPage)->toArray();
            $career_requests = $career_requests['data'];
        }


        return response()->json([
            'data'     =>  $career_requests,
            'count'    =>   $count,
            'success'   =>  true,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          =>  'required',
            'email'         =>  'required',
            'description'   =>  'required',

        ]);

        $career_request = new CareerRequest(request()->all());
        $career_request->save();

        return response()->json([
            'data'  =>  $career_request
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CareerRequest $career_request)
    {
        return response()->json([
            'data'  =>  $career_request
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CareerRequest $career_request)
    {
        $request->validate([
            'name'          =>  'required',
            'email'         =>  'required',
            'description'   =>  'required',

        ]);

        $career_request->update($request->all());

        return response()->json([
            'data'  =>  $career_request
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $career_request = CareerRequest::find($id);
        $career_request->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
