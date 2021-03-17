<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\ReferencePlan;

class DstributorReferencePlanTest extends TestCase
{
  use DatabaseTransactions;

  public function setUp()
  {
    parent::setUp();

    $this->company = factory(\App\Company::class)->create([
      'name' => 'test'
    ]);
  }

  /** @test */
  function user_requires_distributor_and_user()
  {
    $this->json('post', '/api/distributor_reference_plan', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"     =>  [
            "distributor_id"    =>  ["The distributor id field is required."],
            "reference_plan_id" =>  ["The reference plan id field is required."]
          ],
          "message"    =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function assign_reference_plan()
  {
    $distributor = factory(\App\User::class)->create();
    $referencePlan = factory(\App\ReferencePlan::class)->create([
      'company_id'  =>  $this->company->id 
    ]);
    $distributor->assignReferencePlan($referencePlan->id);
    $check = $distributor->hasReferencePlan($referencePlan->id);
    $this->assertTrue($check);
  }

  /** @test */
  function assign_distributor_to_user()
  {
    $this->disableEH();
    $distributor = factory(\App\User::class)->create();
    $referencePlan = factory(\App\ReferencePlan::class)->create([
      'company_id'  =>  $this->company->id 
    ]);
    
    $distributor = factory(\App\User::class)->create();
    $this->payload = [ 
      'reference_plan_id' => $referencePlan->id,
      'distributor_id'    => $distributor->id
    ];
    $this->json('post', '/api/distributor_reference_plan?op=assign', $this->payload)
      ->assertStatus(201)
      ->assertJson([
          'data'  =>  [
            'name'                    =>  $distributor->name,
            'phone'                   =>  $distributor->phone,
            'email'                   =>  $distributor->email,
            'doj'                     =>  $distributor->doj,
            'dob'                     =>  $distributor->dob,
            'company_designation_id'  =>  $distributor->company_designation_id,
            'company_state_branch_id' =>  $distributor->company_state_branch_id,
            'pf_no'                   =>  $distributor->pf_no,
            'uan_no'                  =>  $distributor->uan_no,
            'esi_no'                  =>  $distributor->esi_no,
            'salary'                  =>  $distributor->salary,
            'image_path'              =>  $distributor->image_path,
            'terms_accepted'          =>  $distributor->terms_accepted,
            'reference_plans'                   =>  [
              0 =>  [
                'name'  =>  $referencePlan->name,
              ]
            ]
          ]
        ])
      ->assertJsonStructureExact([
        'data'  =>  [
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
          'so_id', 'asm_id', 'rms_id', 'nsm_id', 'distributor_id',
          'region',
          'state_code',
          'reference_plans'
        ]
      ]);
  }
}
