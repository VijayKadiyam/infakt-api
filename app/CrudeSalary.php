<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrudeSalary extends Model
{
  protected $fillable = [
    'company_id', 'emp_id', 'basic_salary', 'dearness_allowance', 'hra', 'conveyance_allowance', 'mobile_charges', 'communication', 'medical_allowance', 'variable_pay', 'special_allowance', 'bonus_y', 'incentives', 'expense_reimbursement', 'other_earnings', 'bonus_m', 'incentive_1', 'lta', 'travelling', 'daily_allowance', 'other_allowance', 'educational_allowance', 'deputation_allowance' , 'leave_salary', 'm_special', 'fixed_allowance_1', 'gross_salary', 'other_deduction', 'lwf', 'tds', 'esic', 'profession', 'provident_fund', 'total_deductions', 'net_pay', 'epf_employer', 'eps_employer', 'esic_employer', 'mlwf', 'edli_employer', 'pf_admin_charge', 'm_bonus', 'm_m_bonus', 'm_bonus_m', 'insurance', 'wc_policy', 'ctc'
  ];
}
