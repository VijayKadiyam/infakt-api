<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Target;

class TargetsController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company']);
  }

  public function masters(Request $request)
  {
    $usersController = new UsersController();
    $request->request->add(['search' => 'all']);
    $usersResponse = $usersController->index($request);

    $months = [
      ['text'  =>  'JANUARY', 'value' =>  1],
      ['text'  =>  'FEBRUARY', 'value' =>  2],
      ['text'  =>  'MARCH', 'value' =>  3],
      ['text'  =>  'APRIL', 'value' =>  4],
      ['text'  =>  'MAY', 'value' =>  5],
      ['text'  =>  'JUNE', 'value' =>  6],
      ['text'  =>  'JULY', 'value' =>  7],
      ['text'  =>  'AUGUST', 'value' =>  8],
      ['text'  =>  'SEPTEMBER', 'value' =>  9],
      ['text'  =>  'OCTOBER', 'value' =>  10],
      ['text'  =>  'NOVEMBER', 'value' =>  11],
      ['text'  =>  'DECEMBER', 'value' =>  12],
    ];

    $years = ['2020', '2021'];

    return response()->json([
      'users'   =>  $usersResponse->getData()->data,
      'months'  =>  $months,
      'years'   =>  $years
    ], 200);
  }

  public function search(Request $request)
  {
    $targets = $request->company->targets();
    if($request->userId) {
      $targets = $targets
        ->where('user_id', '=', $request->userId);
    }
    if($request->month) {
      $targets = $targets
        ->where('month', '=', $request->month);
    }
    if($request->year) {
      $user = User::find($request->userId);
      $targets = $targets
        ->where('year', '=', $request->year);
    }
    $targets = $targets->get();

    return response()->json([
      'data'     =>  $targets,
      'success'   =>  true
    ], 200);
  }


  public function index(Request $request, User $user)
  {
    $targets = $user->targets;

    return response()->json([
      'data'     =>  $targets,
      'success'   =>  true
    ], 200);
  }

  public function store(Request $request, User $user)
  {
    $request->validate([
      'month'   =>  'required',
      'year'    =>  'required',
      'target'  =>  'required',
    ]);

    $target = new Target($request->all());
    $user->targets()->save($target);

    return response()->json([
      'data'    =>  $target
    ], 201); 
  }

  public function show(User $user, Target $target)
  {
    return response()->json([
      'data'   =>  $target
    ], 200);   
  }

  public function update(Request $request, User $user, Target $target)
  {
    $request->validate([
      'target'        =>  'required',
    ]);

    $target->update($request->all());
      
    return response()->json([
      'data'  =>  $target
    ], 200);
  }
}