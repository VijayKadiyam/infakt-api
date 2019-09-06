<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class FormsController extends Controller
{
  public function pdsForm(Request $request)
  {
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references')
      ->where('id', '=', $request->id)
      ->first();

    return view('forms.pds', compact('user'));
  }

  public function form2(Request $request)
  {
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references')
      ->where('id', '=', $request->id)
      ->first();

    return view('forms.form2', compact('user'));
  }

  public function form11(Request $request)
  {
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references')
      ->where('id', '=', $request->id)
      ->first();

    return view('forms.form11', compact('user'));
  }

  public function graduityForm(Request $request)
  {
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references')
      ->where('id', '=', $request->id)
      ->first();

    return view('forms.graduity', compact('user'));
  }
}
