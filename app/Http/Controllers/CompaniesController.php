<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\CompanyBoard;
use App\CompanyDesignation;
use App\Mail\RegisterMail;
use Illuminate\Support\Facades\Mail;

class CompaniesController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api')
      ->except('index');
  }

  public function masters(Request $request)
  {
    $boardsController = new BoardsController();
    $boardsResponse = $boardsController->index($request);

    return response()->json([
      'boards' =>  $boardsResponse->getData()->data,
    ], 200);
  }
  /*
   * To get all companies
     *
   *@
   */
  public function index()
  {
    if (request()->page && request()->rowsPerPage) {
      $companies = new Company();
      if (request()->search_keyword) {
        $companies = $companies
          ->where('name', 'LIKE', '%' . request()->search_keyword . '%')
          ->orWhere('email', 'LIKE', '%' . request()->search_keyword . '%');
      }
      $count = $companies->count();
      $companies = $companies->with([
        'users'  => function ($query) {
          $query->whereHas('roles',  function ($q) {
            $q->where('name', '=', 'Admin');
          });
        }
      ])->paginate(request()->rowsPerPage)->toArray();
      $companies = $companies['data'];
    } else {
      $companies = Company::with([
        'users'  => function ($query) {
          $query->whereHas('roles',  function ($q) {
            $q->where('name', '=', 'Admin');
          });
        }
      ])->get();
    }
    return response()->json([
      'data'     =>  $companies,
      // 'count'    =>   $count,
      'success'   =>  true,
    ], 200);
  }

  /*
   * To store a new company
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'    =>  'required',
      'email'   =>  'required',
      'phone'   =>  'required',
      'address' =>  'required',
      'city' =>  'required',
      'state' =>  'required',
      'pincode' =>  'required',
    ]);

    if ($request->id == null || $request->id == '') {

      $company = new Company(request()->all());
      $company->save();

      // Save Company Boards
      if (isset($request->company_boards))
        foreach ($request->company_boards as $board) {
          $board = new CompanyBoard($board);
          $company->company_boards()->save($board);
        }
      // ---------------------------------------------------
      // Send Regstration Emal
      if (request()->is_mail_sent == true) {

        $mail = Mail::to($request->email)->send(new RegisterMail($company));
        $company->is_mail_sent = true;
        $company->update();
      }
    } else {
      // Update Company
      $company = Company::find($request->id);
      $company->update($request->all());
      // Check if Company Board deleted
      if (isset($request->company_boards)) {
        $companyBoardIdResponseArray = array_pluck($request->company_boards, 'id');
      } else
        $companyBoardIdResponseArray = [];
      $companyId = $company->id;
      $companyBoardIdArray = array_pluck(CompanyBoard::where('company_id', '=', $companyId)->get(), 'id');
      $differenceCompanyBoardIds = array_diff($companyBoardIdArray, $companyBoardIdResponseArray);
      // Delete which is there in the database but not in the response
      if ($differenceCompanyBoardIds)
        foreach ($differenceCompanyBoardIds as $differenceCompanyBoardId) {
          $companyBoard = CompanyBoard::find($differenceCompanyBoardId);
          $companyBoard->delete();
        }

      // Update Company Board
      if (isset($request->company_boards))
        foreach ($request->company_boards as $board) {
          if (!isset($board['id'])) {
            $company_board = new CompanyBoard($board);
            $company->company_boards()->save($company_board);
          } else {
            $company_board = CompanyBoard::find($board['id']);
            $company_board->update($board);
          }
        }

      // ---------------------------------------------------
    }
    $company->company_boards = $company->company_boards;
    return response()->json([
      'data'    =>  $company,
    ], 201);
  }

  /*
   * To view a single company
   *
   *@
   */
  public function show(Company $company)
  {
    $company->leave_patterns = $company->leave_patterns;
    $company->company_boards = $company->company_boards;

    return response()->json([
      'data'   =>  $company,
      'success' =>  true
    ], 200);
  }

  /*
   * To update a company
   *
   *@
   */
  public function update(Request $request, Company $company)
  {
    $request->validate([
      'name'    =>  'required',
      'email'   =>  'required',
      'phone'   =>  'required',
      'address' =>  'required',
      'city'    =>  'required',
      'state'   =>  'required',
      'pincode' =>  'required',
    ]);

    $company->update($request->all());

    return response()->json([
      'data'  =>  $company
    ], 200);
  }

  public function SendMail()
  {
    $school_id = request()->school_id;
    $school = Company::find($school_id);
    $mail = Mail::to($school->email)->send(new RegisterMail($school));
    // return $mail;
    // if ($mail) {
    $school->is_mail_sent = true;
    $school->update();
    return $school;
    // }
  }
}
