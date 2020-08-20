<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanTravellingDetail extends Model
{
  protected $fillable = [
    'from', 'to', 'travelling_way_id', 'transport_mode_id', 'fare', 'image_path'
  ];

  /*
   * A plan travelling detail belongs to plan
   *
   *@
   */
  public function plan()
  {
    return $this->belongsTo(Plan::class);
  }

  /*
   * A plan travelling detail belongs to travelling_way
   *
   *@
   */
  public function travelling_way()
  {
    return $this->belongsTo(TravellingWay::class);
  }

  /*
   * A plan travelling detail belongs to transport_mode
   *
   *@
   */
  public function transport_mode()
  {
    return $this->belongsTo(TransportMode::class);
  }
}
