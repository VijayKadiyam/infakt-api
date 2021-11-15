<?php

namespace App\Http\Controllers;

use App\ReportList;
use Illuminate\Http\Request;

class ReportListsController extends Controller
{
    public function __construct()
    {
      $this->middleware(['auth:api', 'company']);
    }
  
    /*
     * To get all units
       *
     *@
     */
    public function index()
    {
      $count = 0;
      if(request()->page && request()->rowsPerPage) {
        $report_lists = request()->company->report_lists();
        $count = $report_lists->count();
        $report_lists = $report_lists->paginate(request()->rowsPerPage)->toArray();
        $report_lists = $report_lists['data'];
      } else {
        $report_lists = request()->company->report_lists; 
        $count = $report_lists->count();
      }
  
      return response()->json([
        'data'     =>  $report_lists,
        'count'    =>   $count
      ], 200);
    }
  
    /*
     * To store a new units
     *
     *@
     */
    public function store(Request $request)
    {
      $request->validate([
        'report_type'    =>  'required'
      ]);
  
      $report_list = new ReportList($request->all());
      $request->company->report_lists()->save($report_list);
  
      return response()->json([
        'data'    =>  $report_list
      ], 201); 
    }
  
    /*
     * To view a single unit
     *
     *@
     */
    public function show(ReportList $reportList)
    {
      return response()->json([
        'data'   =>  $reportList
      ], 200);   
    }
  
    /*
     * To update a unit
     *
     *@
     */
    public function update(Request $request, ReportList $reportList)
    {
      $request->validate([
        'report_type'  =>  'required',
      ]);
  
      $reportList->update($request->all());
        
      return response()->json([
        'data'  =>  $reportList
      ], 200);
    }
  }
