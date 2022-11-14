<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
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
    $this->middleware('guest')->except('logout');
  }

  public function login(Request $request)
  {
    $this->validateLogin($request);
    if ($this->attemptLogin($request)) {
      $user = $this->guard()->user();
      $user->generateToken();
      $user->roles = $user->roles;
      $user->is_valid_role = 'false';
      if ($user->roles[0]->name == $request->role) {
        $user->is_valid_role = 'true';
        $user->companies = $user->companies;
        return response()->json([
          'data'    =>  $user->toArray(),
          'message' =>  "User is Logged in Successfully",
          'token'   =>  $user->api_token,
          'success' =>  true,
          'currentAndroidVersionFromApi' =>  '1.0.0',
        ]);
      } else {
        return response()->json([
          'message' => 'invalid User Credential',
          'success' => false,
        ], 400);
      }
    } else {
      $this->sendFailedLoginResponse($request);
    }
  }

  /**
   * Get the needed authorization credentials from the request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  protected function credentials(Request $request)
  {
    return ['email' => $request->{$this->username()}, 'password' => $request->password, 'active' => 1];
    // return ['email' => $request->{$this->username()}, 'password' => $request->password, 'role' => $request->role, 'active' => 1];
  }

  public function logout()
  {
    $user = \Auth::guard('api')->user();
    if ($user) {
      // $user->api_token=null;
      $user->save();
      return response()->json([
        'data'    =>  $user->toArray(),
        'message' => 'user is logged out successfully',
        'success' =>  true,
        'currentAndroidVersionFromApi' =>  '1.0.0',
      ], 200);
    }
    return response()->json([
      'message' => 'User is not logged in'
    ], 204);
  }
}
