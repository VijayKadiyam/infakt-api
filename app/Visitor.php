<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tests\Feature\VisitorTest;

class Visitor extends Model
{
    protected $fillable = [
        'user_id',
        'retailer_id'
    ];

    public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function visitor_bas()
  {
    return $this->hasMany(VisitorBa::class);
  }

  public function visitor_npds()
  {
    return $this->hasMany(VisitorNpd::class);
  }

  public function visitor_stocks()
  {
    return $this->hasMany(VisitorStock::class);
  }

  public function visitor_testers()
  {
    return $this->hasMany(VisitorTester::class);
  }
}
