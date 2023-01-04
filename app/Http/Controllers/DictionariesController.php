<?php

namespace App\Http\Controllers;

use App\Dictionary;
use Illuminate\Http\Request;

class DictionariesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        $dictionaries = $request->company->i_dictionaries;
        $count = $dictionaries->count();

        return response()->json([
            'data'     =>  $dictionaries,
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
            'keyword'        =>  'required',
        ]);

        $dictionaries = new Dictionary(request()->all());
        $request->company->i_dictionaries()->save($dictionaries);

        return response()->json([
            'data'    =>  $dictionaries
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(Dictionary $dictionary)
    {
        return response()->json([
            'data'   =>  $dictionary,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, Dictionary $dictionary)
    {
        $dictionary->update($request->all());
        return response()->json([
            'data'  =>  $dictionary
        ], 200);
    }

    public function destroy($id)
    {
        $dictionaries = Dictionary::find($id);
        $dictionaries->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
