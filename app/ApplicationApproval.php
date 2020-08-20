<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicationApproval extends Model
{
  protected $fillable = [
    'supervisor_id', 'status'
  ];

  /*
   * An user application approval blongs to User applicaiton
   *
   *@
   */
  public function user_application()
  {
    return $this->belongsTo(UserApplication::class);
  }
}
