<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  protected $fillable = [
    'name'
  ];

  /*
   * A role belongs to many users
   *
   *@
   */
  public function users()
  {
    return $this->belingsToMany(User::class);
  }

  /*
   * A role belongs to mnany permissions
   *
   *@
   */
  public function permissions()
  {
    return $this->belongsToMany(Permission::class);
  }

  /**
   * Assign permission to role
   *
   * @ 
   */
  public function assignPermission($permission)
  {
    $this->permissions()->syncWithoutDetaching([$permission]);
    $this->refresh();

    return $this;
  }

  /**
   * Detach permission fom role
   *
   * @ 
   */
  public function unassignPermission($permission)
  {
    $this->permissions()->detach([$permission]);
    $this->refresh();

    return $this;
  }

  /**
   * Check if the permission has permission
   *
   * @ 
   */
  public function hasPermission($permission)
  {
      return $this->permissions ? in_array($permission, $this->permissions->pluck('id')->toArray()) : false;
  }
}
