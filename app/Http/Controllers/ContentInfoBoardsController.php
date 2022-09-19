<?php

namespace App\Http\Controllers;
use App\ContentInfoBoard;
use Illuminate\Http\Request;

class ContentInfoBoardsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['company']);
    }

    public function index(Request $request)
    {
        $count = 0;

        $content_info_boards = ContentInfoBoard::all();
        $count = $content_info_boards->count();

        return response()->json([
            'data'     =>  $content_info_boards,
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

        $content_info_board = new ContentInfoBoard(request()->all());
        $content_info_board->save();

        return response()->json([
            'data'    =>  $content_info_board
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(ContentInfoBoard $content_info_board)
    {
        return response()->json([
            'data'   =>  $content_info_board,
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, ContentInfoBoard $content_info_board)
    {
        $content_info_board->update($request->all());

        return response()->json([
            'data'  =>  $content_info_board
        ], 200);
    }

    public function destroy($id)
    {
        $content_info_board = ContentInfoBoard::find($id);
        $content_info_board->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}

