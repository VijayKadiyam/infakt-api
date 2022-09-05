<?php

namespace App\Http\Controllers;

use App\CompanyBoard;
use Illuminate\Http\Request;

class CompanyBoardsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        $company_boards = $request->company->company_boards;
        $count = $company_boards->count();

        return response()->json([
            'data'     =>  $company_boards,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new company_board
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'board_id'        =>  'required',
        ]);

        $company_boards = new CompanyBoard(request()->all());
        $request->company->company_boards()->save($company_boards);

        return response()->json([
            'data'    =>  $company_boards
        ], 201);
    }

    /*
     * To view a single company_board
     *
     *@
     */
    public function show(CompanyBoard $company_board)
    {
        return response()->json([
            'data'   =>  $company_board,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a company_board
     *
     *@
     */
    public function update(Request $request, CompanyBoard $company_board)
    {
        $company_board->update($request->all());

        return response()->json([
            'data'  =>  $company_board
        ], 200);
    }

    public function destroy($id)
    {
        $company_boards = CompanyBoard::find($id);
        $company_boards->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}