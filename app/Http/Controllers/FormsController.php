<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use PDF;

class FormsController extends Controller
{
  public function pdsForm(Request $request)
  {
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references')
      ->where('id', '=', $request->id)
      ->first();

    return view('forms.pds', compact('user'));
  }

  public function pdsFormDownload(Request $request)
  {
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references')
      ->where('id', '=', $request->id)
      ->first();

    $data['user'] = $user;

    $pdf = PDF::loadView('forms.pds', $data);

    return $pdf->download($user->name . '-pds-form.pdf');
  }

  public function form2(Request $request)
  {
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references')
      ->where('id', '=', $request->id)
      ->first();

    return view('forms.form2', compact('user'));
  }

  public function form2Download(Request $request)
  {
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references')
      ->where('id', '=', $request->id)
      ->first();

    $data['user'] = $user;

    $pdf = PDF::loadView('forms.form2', $data);

    return $pdf->download($user->name . '-form-2.pdf');
  }

  public function form11(Request $request)
  {
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references')
      ->where('id', '=', $request->id)
      ->first();

    return view('forms.form11', compact('user'));
  }

  public function form11Download(Request $request)
  {
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references')
      ->where('id', '=', $request->id)
      ->first();

    $data['user'] = $user;

    $pdf = PDF::loadView('forms.form11', $data);

    return $pdf->download($user->name . '-form-11.pdf');
  }

  public function graduityForm(Request $request)
  {
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references')
      ->where('id', '=', $request->id)
      ->first();

    return view('forms.graduity', compact('user'));
  }

  public function graduityFormDownload(Request $request)
  {
    $user = User::with('user_work_experiences', 'user_educations', 'user_family_details', 'user_references')
      ->where('id', '=', $request->id)
      ->first();

    $data['user'] = $user;

    $pdf = PDF::loadView('forms.graduity', $data);

    return $pdf->download($user->name . '-graduity.pdf');
  }
}
