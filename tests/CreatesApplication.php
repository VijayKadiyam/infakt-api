<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use App\Exceptions\Handler;
use Exception;
use Tests\AssertJsonTrait;

trait CreatesApplication
{
  use AssertJsonTrait;

  protected $user, $headers;

  /**
   * Creates the application.
   *
   * @return \Illuminate\Foundation\Application
   */
  public function createApplication()
  {
    $app = require __DIR__.'/../bootstrap/app.php';

    $app->make(Kernel::class)->bootstrap();

    $this->setUpAssertJsonTrait();

    $this->user = factory(\App\User::class)->create();
    $this->user->generateToken();

    $this->headers    = [
      'Authorization' =>  "Bearer " . $this->user->api_token,
    ];

    return $app;
  }

  /*
   * To get the error details
   *
   *@
   */
  public function disableEH()
  {
    app()->instance(Handler::Class, new class extends Handler {
      public function __construct(){}
      public function report(Exception $exception) {}
      public function render($request, Exception $exception)
      {
          throw $exception;
      }
    });
  }
}
