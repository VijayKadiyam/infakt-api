<?php

namespace App\Http\Controllers;

use App\UserSection;
use Illuminate\Http\Request;

class UserSectionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        if ($request->user_id) {
            $user_sections = request()->company->user_sections()
                ->where('user_id', '=', $request->user_id)
                ->get();
        } else {

            $user_sections = request()->company->user_sections;
            $count = $user_sections->count();
        }

        return response()->json([
            'data'     =>  $user_sections,
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
        ]);

        $user_section = new UserSection(request()->all());
        $request->company->user_sections()->save($user_section);

        return response()->json([
            'data'    =>  $user_section
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(UserSection $user_section)
    {
        return response()->json([
            'data'   =>  $user_section,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, UserSection $user_section)
    {
        $user_section->update($request->all());

        return response()->json([
            'data'  =>  $user_section
        ], 200);
    }

    public function destroy($id)
    {
        $user_section = UserSection::find($id);
        $user_section->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
