<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Role;
use App\Permission;

class PermissionRoleTest extends TestCase
{
  use DatabaseTransactions;

  /** @test */
  function user_must_be_logged_in()
  {
    $this->json('post', '/api/permission_role')
      ->assertStatus(401);
  }

  /** @test */
  function user_requires_following_details()
  {
    $this->json('post', '/api/permission_role', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"     =>  [
            "role_id"  =>  ["The role id field is required."],
            "permission_id"  =>  ["The permission id field is required."]
          ],
          "message"    =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function assign_permission()
  {
    $role = Role::find(1);
    $role->assignPermission(1);
    $check = $role->hasPermission(1);
    $this->assertTrue($check);
    $this->assertCount(26, $role->permissions);
  }

  /** @test */
  function assign_permission_to_role()
  {
    $this->disableEH();
    $role = Role::find(1);
    $permission = Permission::find(1);
    $this->payload = [ 
      'permission_id' => 1,
      'role_id'       => 1
    ];
    $this->json('post', '/api/permission_role?op=assign', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'  =>  [
            'name'                    =>  $role->name,
            'permissions'              =>  [
              0 =>  [
                'name'  =>  $permission->name
              ]
            ]
          ]
        ])
      ->assertJsonStructureExact([
        'data'  =>  [
          'id',
          'name',
          'created_at',
          'updated_at',
          'permissions'
        ]
      ]);
  }

  /** @test */
  function unassign_permission()
  {
    $role = \App\Role::find(1);
    $role->assignPermission(1);
    $check = $role->hasPermission(1);
    $this->assertTrue($check);
    $this->assertCount(26, $role->permissions);
    $role->unassignpermission(1);
    $this->assertCount(25, $role->permissions);
  }

  /** @test */
  function unassign_permission_from_role()
  {
    $this->disableEH();
    $role = Role::find(1);
    $permission = Permission::find(1);
    $role->assignPermission(1);
    $check = $role->hasPermission(1);
    $this->assertCount(26, $role->permissions);

    $this->payload        = [ 
      'role_id'         => $role->id,
      'permission_id'   => 1
    ];

    $this->json('post', '/api/permission_role?op=unassign', $this->payload , $this->headers)
         ->assertStatus(201);

    $role->refresh();
    $this->assertCount(25, $role->permissions);
  }
}
