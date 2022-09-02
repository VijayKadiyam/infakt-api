<?php

namespace App\Http\Controllers;

use App\ContactRequest;
use Illuminate\Http\Request;

class ContactRequestsController extends Controller
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
            $contact_requests = new ContactRequest;
            if (request()->search_keyword) {
                $contact_requests = $contact_requests
                    ->orwhere('name', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('interested_in', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('phone_no', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('status', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('remarks', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('created_at', 'LIKE', '%' . request()->search_keyword . '%')
                    ->where('is_deleted', false)
                    ->latest();
            }
            $count = $contact_requests->count();
            $contact_requests = $contact_requests->latest()->paginate(request()->rowsPerPage)->toArray();
            $contact_requests = $contact_requests['data'];
        }


        return response()->json([
            'data'     =>  $contact_requests,
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
            'name'  =>  'required',
            'email'  =>  'required',
            'school_name'  =>  'required',
            'role'  =>  'required',
            'city'  =>  'required',
            'state'  =>  'required',
            'pincode'  =>  'required',
        ]);

        $contact_request = new ContactRequest(request()->all());
        $contact_request->save();

        return response()->json([
            'data'  =>  $contact_request
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ContactRequest $contact_request)
    {
        return response()->json([
            'data'  =>  $contact_request
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContactRequest $contact_request)
    {
        $request->validate([
            'name'  =>  'required',
            'email'  =>  'required',
            'school_name'  =>  'required',
            'role'  =>  'required',
            'city'  =>  'required',
            'state'  =>  'required',
            'pincode'  =>  'required',
        ]);

        $contact_request->update($request->all());

        return response()->json([
            'data'  =>  $contact_request
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
        $contact_request = ContactRequest::find($id);
        $contact_request->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
