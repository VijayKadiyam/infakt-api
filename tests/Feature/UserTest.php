<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\UserAppointmentLetter;

class UserTest extends TestCase
{
  use DatabaseTransactions;
  
  public function setUp()
  {
    parent::setUp();

    $this->company = factory(\App\Company::class)->create([
      'name' => 'test'
    ]);
    $this->user->assignCompany($this->company->id);
    $this->headers['company-id'] = $this->company->id;

    $this->payload = [ 
      'name'                 =>'sangeetha',
      'phone'                => 9844778380,
      'email'                =>'sangeetha@gmail.com',
      'doj'               =>  '12-02-2019',
      'dob'               =>  '04-05-1992',
      'company_designation_id'  =>  1,
      'company_state_id'  =>  1,
      'company_state_branch_id' => 1,
      'pf_no'                   =>  '1234567654',
      'uan_no'                  =>  '1234565432',
      'esi_no'                  =>  '234565',
      'role_id'                 =>  3
    ];
  }

  /** @test */
  function user_must_be_logged_in()
  {
    $this->json('post', '/api/users')
         ->assertStatus(401);
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/users', [], $this->headers)
         ->assertStatus(422)
         ->assertExactJson([
            "errors"  =>  [
              "name"                    =>  ["The name field is required."],
              "email"                   =>  ["The email field is required."],
              "phone"                   =>  ["The phone field is required."],
              // "doj"                     =>  ["The doj field is required."],
              // "dob"                     =>  ["The dob field is required."],
              // "company_designation_id"  =>  ["The company designation id field is required."],
              "role_id"  =>  ["The role id field is required."],
            ],
            "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_user()
  {
    $this->json('post', '/api/users', $this->payload, $this->headers)
     ->assertStatus(201)
     ->assertJson([
        'data'  =>  [
          'name'                 =>'sangeetha',
          'phone'                => 9844778380,
          'email'                =>'sangeetha@gmail.com',
          'doj'               =>  '12-02-2019',
          'dob'               =>  '04-05-1992',
          'company_designation_id'  =>  1,
          'company_state_id' => 1,
          'company_state_branch_id' => 1,
          'pf_no'                   =>  '1234567654',
          'uan_no'                  =>  '1234565432',
          'esi_no'                  =>  '234565'
        ]
      ])
      ->assertJsonStructure([
          'data'  =>  [
            'name',
            'phone',
            'email',
          ]
        ])
      ->assertJsonStructureExact([
          'data'  =>  [
            'name',
            'phone',
            'email',
            'doj',
            'dob',
            'company_designation_id',
            'company_state_id',
            'company_state_branch_id',
            'pf_no',
            'uan_no',
            'esi_no',
            'password_backup',
            'updated_at',
            'created_at',
            'id',
            'roles',
            'companies'
          ]
        ]);
  }

  /** @test */
  public function list_of_users()
  {
    $this->disableEH();
    $user = factory(\App\User::class)->create();
    $user->assignRole(3);
    $user->assignCompany($this->company->id);

    $this->json('get', '/api/users?role_id=3', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => []
        ]);
    $this->assertCount(1, User::whereHas('roles',  function($q) {
                                $q->where('name', '!=', 'Admin');
                                $q->where('name', '!=', 'Super Admin');
                              })->get());
  }

  /** @test */
  public function list_of_users_of_search()
  {
    $this->disableEH();
    $user = factory(\App\User::class)->create();
    $user->assignRole(3);
    $user->assignCompany($this->company->id);

    $this->json('get', '/api/users?searchEmp=' . $user->name, [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => []
        ]);
    $this->assertCount(1, User::whereHas('roles',  function($q) {
                                $q->where('name', '!=', 'Admin');
                                $q->where('name', '!=', 'Super Admin');
                              })->get());
  }

  /** @test */
  public function list_of_users_of_report()
  {
    $this->disableEH();
    $user = factory(\App\User::class)->create();
    $user->assignRole(3);
    $user->assignCompany($this->company->id);

    $this->json('get', '/api/users?role_id=3&report=monthly', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => []
        ]);
    $this->assertCount(1, User::whereHas('roles',  function($q) {
                                $q->where('name', '!=', 'Admin');
                                $q->where('name', '!=', 'Super Admin');
                              })->get());
  }

  /** @test */
  public function list_of_users_of_month_and_year()
  {
    $this->disableEH();
    $user = factory(\App\User::class)->create();
    $user->assignRole(3);
    $user->assignCompany($this->company->id);

    $this->json('get', '/api/users?role_id=3&month=01&year=2020', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => []
        ]);
    $this->assertCount(1, User::whereHas('roles',  function($q) {
                                $q->where('name', '!=', 'Admin');
                                $q->where('name', '!=', 'Super Admin');
                              })->get());
  }

  /** @test */
  public function list_of_users_of_endreport()
  {
    $this->disableEH();
    $user = factory(\App\User::class)->create();
    $user->assignRole(3);
    $user->assignCompany($this->company->id);

    factory(UserAppointmentLetter::class)->create([
      'user_id'  =>  $this->user->id,
      'end_date'  =>  '2020-01-10'
    ]);

    $this->json('get', '/api/users?role_id=3&endreport=monthly', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => []
        ]);
    $this->assertCount(1, User::whereHas('roles',  function($q) {
                                $q->where('name', '!=', 'Admin');
                                $q->where('name', '!=', 'Super Admin');
                              })->get());
  }

  /** @test */
  public function list_of_users_of_birthday()
  {
    $this->disableEH();
    $user = factory(\App\User::class)->create([
      'dob' =>  \Carbon\Carbon::now()->format("Y-m-d")
    ]);
    $user->assignRole(3);
    $user->assignCompany($this->company->id);

    factory(UserAppointmentLetter::class)->create([
      'user_id'  =>  $this->user->id,
      'end_date'  =>  '2020-01-10'
    ]);

    $this->json('get', '/api/users?role_id=3&birthday=today', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => []
        ]);
    $this->assertCount(1, User::whereHas('roles',  function($q) {
                                $q->where('name', '!=', 'Admin');
                                $q->where('name', '!=', 'Super Admin');
                              })->get());
  }

  /** @test */
  function show_single_user_details()
  {
    $this->json('get', "/api/users/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data'  =>  [
            'name',
            'phone',
            'email' 
          ]
        ]);
  }

  /** @test */
  function update_single_user_details()
  {
    $this->disableEH();
    $payload  = [ 
      'name'  =>  'sangeetha',
      'phone' =>  9088597123,
      'email' =>  'preethi@gmail.com',
      'doj'               =>  '12-02-2019',
      'dob'               =>  '04-05-1992',
      'company_designation_id'  =>  1,
      'company_state_branch_id' => 1,
      'pf_no'                   =>  '1234567654',
      'uan_no'                  =>  '1234565432',
      'esi_no'                  =>  '234565'
    ];
    $this->json('patch', '/api/users/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    =>  [
            'phone' =>  9088597123,
            'email' =>  'preethi@gmail.com',
          ]
        ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'name',
            'email',
            'email_verified_at',
            'active',
            'phone',
            'api_token',
            'doj',
            'dob',
            'company_designation_id',
            'company_state_branch_id',
            'pf_no',
            'uan_no',
            'esi_no',
            'created_at',
            'updated_at',
            'salary',
            'image_path',
            'employee_code',
            'asm_area',
            'asm_name',
            'uid_no',
            'terms_accepted',
            'company_state_id',
            'address',
            'unique_id',
            'appointment_letter',
            'contract_expiry',
            'resume_path',
            'photo_path', 
            'residential_proof_path',
            'education_proof_path',
            'pan_card_path',
            'adhaar_card_path',
            'esi_card_path',
            'cancelled_cheque_path',
            'salary_slip_path',
            'birth_certificate_path',
            'passport_path',
            'driving_license_path',
            'school_leaving_certificate_path',
            'mark_sheet_path',
            'experience_certificate_path',
            'prev_emp_app_letter_path',
            'form_2_path',
            'form_11_path',
            'graduity_form_path',
            'app_letter_path',
            'pds_form_path',
            'full_name',
            'father_name',
            'surname',
            'mother_name',
            'marital_status',
            'pan_no',
            'adhaar_no',
            'pre_room_no',
            'pre_building',
            'pre_area',
            'pre_road',
            'pre_city',
            'pre_state',
            'pre_pincode',
            'pre_mobile',
            'pre_email',
            'per_room_no',
            'per_building',
            'per_area',
            'per_road',
            'per_city',
            'per_state',
            'per_pincode',
            'per_mobile',
            'per_email',
            'blood_group',
            'bank_name',
            'bank_acc_no',
            'bank_ifsc_code',
            'bank_branch_name',
            'data_submitted',
            'is_fresher',
            'pds_form_sign_path',
            'form_2_sign_path',
            'form_11_sign_path',
            'graduity_form_sign_path',
            'password_backup',
            'gender',
            'pds_form_checked',
            'form_2_checked', 
            'form_11_checked', 
            'graduity_form_checked',
            'beat_type_id',
            'roles',
            'sites'
          ],
          'success'
        ]);
  }

  /** @test */
  function update_unique_id()
  {
    $this->disableEH();
    $payload = [
      'unique_id' =>  '123'
    ];

    $this->json('patch', '/api/users/1/uniqueID', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    =>  [
            'unique_id' => '123',
          ],
          'success' => true
        ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'name',
            'email',
            'email_verified_at',
            'active',
            'phone',
            'api_token',
            'doj',
            'dob',
            'company_designation_id',
            'company_state_branch_id',
            'pf_no',
            'uan_no',
            'esi_no',
            'created_at',
            'updated_at',
            'salary',
            'image_path',
            'employee_code',
            'asm_area',
            'asm_name',
            'uid_no',
            'terms_accepted',
            'company_state_id',
            'address',
            'unique_id',
            'appointment_letter',
            'contract_expiry',
            'resume_path',
            'photo_path', 
            'residential_proof_path',
            'education_proof_path',
            'pan_card_path',
            'adhaar_card_path',
            'esi_card_path',
            'cancelled_cheque_path',
            'salary_slip_path',
            'birth_certificate_path',
            'passport_path',
            'driving_license_path',
            'school_leaving_certificate_path',
            'mark_sheet_path',
            'experience_certificate_path',
            'prev_emp_app_letter_path',
            'form_2_path',
            'form_11_path',
            'graduity_form_path',
            'app_letter_path',
            'pds_form_path',
            'full_name',
            'father_name',
            'surname',
            'mother_name',
            'marital_status',
            'pan_no',
            'adhaar_no',
            'pre_room_no',
            'pre_building',
            'pre_area',
            'pre_road',
            'pre_city',
            'pre_state',
            'pre_pincode',
            'pre_mobile',
            'pre_email',
            'per_room_no',
            'per_building',
            'per_area',
            'per_road',
            'per_city',
            'per_state',
            'per_pincode',
            'per_mobile',
            'per_email',
            'blood_group',
            'bank_name',
            'bank_acc_no',
            'bank_ifsc_code',
            'bank_branch_name',
            'data_submitted',
            'is_fresher',
            'pds_form_sign_path',
            'form_2_sign_path',
            'form_11_sign_path',
            'graduity_form_sign_path',
            'password_backup',
            'gender',
            'pds_form_checked',
            'form_2_checked', 
            'form_11_checked', 
            'graduity_form_checked',
            'beat_type_id',
          ],
          'success'
        ]);
  }

  /** @test */
  function check_for_wrong_unique_id()
  {
    $this->disableEH();
    $payload = [
      'unique_id' =>  '123'
    ];

    $this->json('patch', '/api/users/1/uniqueID', $payload, $this->headers);

    $payload = [
      'unique_id' =>  '1234'
    ];

    $this->json('patch', '/api/users/1/uniqueID', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    =>  [
            'unique_id' => '123',
          ],
          'success' => false
        ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'name',
            'email',
            'email_verified_at',
            'active',
            'phone',
            'api_token',
            'doj',
            'dob',
            'company_designation_id',
            'company_state_branch_id',
            'pf_no',
            'uan_no',
            'esi_no',
            'created_at',
            'updated_at',
            'salary',
            'image_path',
            'employee_code',
            'asm_area',
            'asm_name',
            'uid_no',
            'terms_accepted',
            'company_state_id',
            'address',
            'unique_id',
            'appointment_letter',
            'contract_expiry',
            'resume_path',
            'photo_path', 
            'residential_proof_path',
            'education_proof_path',
            'pan_card_path',
            'adhaar_card_path',
            'esi_card_path',
            'cancelled_cheque_path',
            'salary_slip_path',
            'birth_certificate_path',
            'passport_path',
            'driving_license_path',
            'school_leaving_certificate_path',
            'mark_sheet_path',
            'experience_certificate_path',
            'prev_emp_app_letter_path',
            'form_2_path',
            'form_11_path',
            'graduity_form_path',
            'app_letter_path',
            'pds_form_path',
            'full_name',
            'father_name',
            'surname',
            'mother_name',
            'marital_status',
            'pan_no',
            'adhaar_no',
            'pre_room_no',
            'pre_building',
            'pre_area',
            'pre_road',
            'pre_city',
            'pre_state',
            'pre_pincode',
            'pre_mobile',
            'pre_email',
            'per_room_no',
            'per_building',
            'per_area',
            'per_road',
            'per_city',
            'per_state',
            'per_pincode',
            'per_mobile',
            'per_email',
            'blood_group',
            'bank_name',
            'bank_acc_no',
            'bank_ifsc_code',
            'bank_branch_name',
            'data_submitted',
            'is_fresher',
            'pds_form_sign_path',
            'form_2_sign_path',
            'form_11_sign_path',
            'graduity_form_sign_path',
            'password_backup',
            'gender',
            'pds_form_checked',
            'form_2_checked', 
            'form_11_checked', 
            'graduity_form_checked',
            'beat_type_id',
          ],
          'success'
        ]);
  }
}
