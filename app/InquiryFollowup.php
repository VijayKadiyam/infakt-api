<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InquiryFollowup extends Model
{
  protected $fillable = [
    'user_id', 'status', 'description', 'date', 'call_type'
  ];
  
  public function inquiry()
  {
    return $this->belongsTo(Inquiry::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
