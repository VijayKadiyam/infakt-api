<?php

namespace App\Http\Controllers;

use App\PjpVisitedSupervisorExpense;
use Illuminate\Http\Request;

class PjpVisitedSupervisorExpensesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function masters(Request $request)
    {
        $expenseTypes = [
            [
                'id'    =>  'Food',
                'value' => 'Food'
            ],
            [
                'id'    =>  'Accommodation',
                'value' => 'Accommodation'
            ],
            [
                'id'    =>  'Travelling',
                'value' => 'Travelling'
            ],
        ];

        $travellingWays = [
            [
                'id'    =>  '1 Way',
                'value' => '1 Way'
            ],
            [
                'id'    =>  '2 Way',
                'value' => '2 Way'
            ],
        ];

        $transportModes = [
            [
                'id'    =>  'Bike',
                'value' => 'Bike'
            ],
            [
                'id'    =>  'Bus',
                'value' => 'Bus'
            ],
            [
                'id'    =>  'Car',
                'value' => 'Car'
            ],
            [
                'id'    =>  'Train',
                'value' => 'Train'
            ],
        ];


        return response()->json([
            'expenseTypes'            =>  $expenseTypes,
            'travellingWays'           =>  $travellingWays,
            'transportModes'           =>  $transportModes
        ], 200);
    }
    
    public function index()
    {
        $pjp_visited_supervisor_expenses = request()->company->pjp_visited_supervisor_expenses;
        $count = $pjp_visited_supervisor_expenses->count();

        return response()->json([
            'data'     =>  $pjp_visited_supervisor_expenses,
            'count'    =>   $count,
            'success' =>  true,
        ], 200);
    }

    /*
         * To store a new pjp_visited_supervisor_expenses
         *
         *@
         */
    public function store(Request $request)
    {
        $request->validate([
            'expense_type'    =>  'required'
        ]);

        $pjp_visited_supervisor_expense = new PjpVisitedSupervisorExpense($request->all());
        $request->company->pjp_visited_supervisor_expenses()->save($pjp_visited_supervisor_expense);

        return response()->json([
            'data'    =>  $pjp_visited_supervisor_expense
        ], 201);
    }

    /*
         * To view a single pjp_visited_supervisor_expense
         *
         *@
         */
    public function show(PjpVisitedSupervisorExpense $pjp_visited_supervisor_expense)
    {
        return response()->json([
            'data'   =>  $pjp_visited_supervisor_expense
        ], 200);
    }

    /*
         * To update a pjp_visited_supervisor_expense
         *
         *@
         */
    public function update(Request $request, PjpVisitedSupervisorExpense $pjp_visited_supervisor_expense)
    {
        $request->validate([
            'expense_type'  =>  'required',
        ]);

        $pjp_visited_supervisor_expense->update($request->all());

        return response()->json([
            'data'  =>  $pjp_visited_supervisor_expense
        ], 200);
    }
}
