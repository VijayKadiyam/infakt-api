<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\User;

class ResetPasswordController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Password Reset Controller
  |--------------------------------------------------------------------------
  |
  | This controller is responsible for handling password reset requests
  | and uses a simple trait to include this behavior. You're free to
  | explore this trait and override any methods you wish to tweak.
  |
  */

  use ResetsPasswords;

  /**
   * Where to redirect users after resetting their password.
   *
   * @var string
   */
  protected $redirectTo = '/home';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest');
  }

  /*
   * reset password
   *
   *@
   */
  public function reset_password(Request $request)
  {
    $this->validate($request, $this->rules(), $this->validationErrorMessages());

    $user = User::where('email', '=', $request->email)->first();

    $this->resetPassword($user, $request->password);

    return response()->json([
    'data'    =>  $user->toArray()
    ], 201); 
  }

  /**
   * Get the password reset validation rules.
   *
   * @return array
   */
  protected function rules()
  {
    return [
      'email'     => 'required|email',
      'password'  => 'required|confirmed|min:6',
    ];
  }
}
