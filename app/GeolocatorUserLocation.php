<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeolocatorUserLocation extends Model
{
  protected $fillable = [
    'user', 'lat', 'long'
  ];

  /*
   * A geolocator user location belongs to user
   *
   *@
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
