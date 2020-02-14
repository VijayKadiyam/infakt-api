<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InquiryRemark extends Model
{
  protected $fillable = [
    'user_id', 'meeting_time', 'meeting_time', 'venue', 'date', 'note'
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
