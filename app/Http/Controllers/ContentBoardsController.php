<?php

namespace App\Http\Controllers;

use App\ContentBoard;
use Illuminate\Http\Request;

class ContentBoardsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $content_boards = ContentBoard::all();
        $count = $content_boards->count();

        return response()->json([
            'data'     =>  $content_boards,
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
            'content_id'        =>  'required',
        ]);

        $content_boards = new ContentBoard(request()->all());
        $content_boards->save();

        return response()->json([
            'data'    =>  $content_boards
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(ContentBoard $content_board)
    {
        return response()->json([
            'data'   =>  $content_board,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, ContentBoard $content_board)
    {
        $content_board->update($request->all());

        return response()->json([
            'data'  =>  $content_board
        ], 200);
    }

    public function destroy($id)
    {
        $content_boards = ContentBoard::find($id);
        $content_boards->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
