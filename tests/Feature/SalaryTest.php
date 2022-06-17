<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Salary;

class SalaryTest extends TestCase
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

    factory(Salary::class)->create([
      'user_id'  =>  $this->user->id 
    ]);

    $this->payload = [ 
      'month' =>  '02',
      'year'  =>  '2020',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/salaries')
      ->assertStatus(401); 
  }

  /** @test */
  function add_new_user_attendance()
  {
    $this->disableEH();
    $this->json('post', '/api/salaries', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'month' =>  '02',
            'year'  =>  '2020', 
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'month',
            'year',
            'user_id',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_user_sales()
  {
    $this->json('GET', '/api/salaries',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0 =>  [
              'month',
            ] 
          ]
        ]);
    $this->assertCount(1, Salary::all());
  }

  /** @test */
  function show_single_user_sale()
  {
    $this->json('get', "/api/salaries/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'month' =>  '01',       ]
        ]);
  }

  /** @test */
  function update_single_user_attendance()
  {
    $payload = [ 
      'month' =>  '022',
      'year'  =>  '2022',
    ];

    $this->json('patch', '/api/salaries/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'month' =>  '022',
            'year'  =>  '2022',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'user_id','month', 'year', 'emp_id', 'basic_salary', 'dearness_allowance', 'hra', 'conveyance_allowance', 'mobile_charges', 'communication', 'medical_allowance', 'variable_pay', 'special_allowance', 'bonus_y', 'incentives', 'expense_reimbursement', 'other_earnings', 'bonus_m', 'incentive_1', 'lta', 'travelling', 'daily_allowance', 'other_allowance', 'educational_allowance', 'deputation_allowance' , 'leave_salary', 'm_special', 'fixed_allowance_1', 'gross_salary', 'other_deduction', 'lwf', 'tds', 'esic', 'profession', 'provident_fund', 'total_deductions', 'net_pay', 'epf_employer', 'eps_employer', 'esic_employer', 'mlwf', 'edli_employer', 'pf_admin_charge', 'm_bonus', 'm_m_bonus', 'm_bonus_m', 'insurance', 'wc_policy', 'ctc',
            'created_at',
            'updated_at'
          ]
      ]);
  }

}
