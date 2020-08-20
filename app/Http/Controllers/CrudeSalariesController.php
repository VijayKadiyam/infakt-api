<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Imports\SalaryImport;
use App\CrudeSalary;
use Maatwebsite\Excel\Facades\Excel;
use App\User;
use App\Salary;

class CrudeSalariesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth:api', 'company'])
      ->except(['index']);
  }

  public function index()
  {
    return response()->json([
      'data'  =>  CrudeSalary::all()
    ]);
  }

  public function uploadSalary(Request $request)
  {
    set_time_limit(0);
    
    if ($request->hasFile('salaryData')) {
      $file = $request->file('salaryData');

      Excel::import(new SalaryImport, $file);
      
      return response()->json([
        'data'    =>  CrudeSalary::all(),
        'success' =>  true
      ]);
    }
  }

  public function processSalary(Request $request)
  {
    set_time_limit(0);
    
    $crude_salaries = CrudeSalary::all();

    foreach($crude_salaries as $salary) {
      if($salary->emp_id) {
        $sal = Salary::where('emp_id', '=', $salary->emp_id)
          ->where('month', '=', $salary->month)
          ->where('year', '=', $salary->year)
          ->first();

        if(!$sal) {
          $user = User::where('employee_code', '=', $salary->emp_id)
            ->first();
          $data = [
            'month'                 =>  $request->month,
            'year'                  =>  $request->year,
            'emp_id'                =>  $salary->emp_id,
            'basic_salary'          =>  $salary->basic_salary,
            'dearness_allowance'    =>  $salary->dearness_allowance,
            'hra'                   =>  $salary->hra,
            'conveyance_allowance'  =>  $salary->conveyance_allowance,
            'mobile_charges'        =>  $salary->mobile_charges,
            'communication'         =>  $salary->communication,
            'medical_allowance'     =>  $salary->medical_allowance,
            'variable_pay'          =>  $salary->variable_pay,
            'special_allowance'     =>  $salary->special_allowance,
            'bonus_y'               =>  $salary->bonus_y,
            'incentives'            =>  $salary->incentives,
            'expense_reimbursement' =>  $salary->expense_reimbursement,
            'other_earnings'        =>  $salary->other_earnings,
            'bonus_m'               =>  $salary->bonus_m,
            'incentive_1'           =>  $salary->incentive_1,
            'lta'                   =>  $salary->lta,
            'travelling'            =>  $salary->travelling,
            'daily_allowance'       =>  $salary->daily_allowance,
            'other_allowance'       =>  $salary->other_allowance,
            'educational_allowance' =>  $salary->educational_allowance,
            'deputation_allowance'  =>  $salary->deputation_allowance,
            'leave_salary'          =>  $salary->leave_salary,
            'm_special'             =>  $salary->m_special,
            'fixed_allowance_1'     =>  $salary->fixed_allowance_1,
            'gross_salary'          =>  $salary->gross_salary,
            'other_deduction'       =>  $salary->other_deduction,
            'lwf'                   =>  $salary->lwf,
            'tds'                   =>  $salary->tds,
            'esic'                  =>  $salary->esic,
            'profession'            =>  $salary->profession,
            'provident_fund'        =>  $salary->provident_fund,
            'total_deductions'      =>  $salary->total_deductions,
            'net_pay'               =>  $salary->net_pay,
            'epf_employer'          =>  $salary->epf_employer,
            'eps_employer'          =>  $salary->eps_employer,
            'esic_employer'         =>  $salary->esic_employer,
            'mlwf'                  =>  $salary->mlwf,
            'edli_employer'         =>  $salary->edli_employer,
            'pf_admin_charge'       =>  $salary->pf_admin_charge,
            'm_bonus'               =>  $salary->m_bonus,
            'm_m_bonus'             =>  $salary->m_m_bonus,
            'm_bonus_m'             =>  $salary->m_bonus_m,
            'insurance'             =>  $salary->insurance,
            'wc_policy'             =>  $salary->wc_policy,
            'ctc'                   =>  $salary->ctc,
          ];
          $sal = new Salary($data);
          if($user) {
            $user->salaries()->save($sal);
          }
        }
      }
    }
  }

  public function truncate()
  {
    CrudeSalary::truncate();
  }
}
