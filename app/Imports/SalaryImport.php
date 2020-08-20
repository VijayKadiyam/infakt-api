<?php

namespace App\Imports;

use App\CrudeSalary;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class SalaryImport implements ToModel, WithHeadingRow
{
  public function model(array $row)
  {
    $data = [
      'company_id'            =>  request()->company->id,
      'emp_id'                =>  $row['EmpID'],
      'basic_salary'          =>  $row['Basic Salary'],
      'dearness_allowance'    =>  $row['Dearness Allowance'],
      'hra'                   =>  $row['H R A'],
      'conveyance_allowance'  =>  $row['Conveyance Allowance'],
      'mobile_charges'        =>  $row['Mobile Charges'],
      'communication'         =>  $row['Communication'],
      'medical_allowance'     =>  $row['Medical Allowance'],
      'variable_pay'          =>  $row['Variable Pay'],
      'special_allowance'     =>  $row['Special Allowance'],
      'bonus_y'               =>  $row['Bonus (y)'],
      'incentives'            =>  $row['Incentives'],
      'expense_reimbursement' =>  $row['Expenses Reimbursement'],
      'other_earnings'        =>  $row['Other Earning'],
      'bonus_m'               =>  $row['Bonus (M)'],
      'incentive_1'           =>  $row['Incentive 1'],
      'lta'                   =>  $row['LTA'],
      'travelling'            =>  $row['Travelling Allowance 75'],
      'daily_allowance'       =>  $row['Daily Allowance'],
      'other_allowance'       =>  $row['Other Allowance'],
      'educational_allowance' =>  $row['Educational Allowance'],
      'deputation_allowance'  =>  $row['Deputation Allowance'],
      'leave_salary'          =>  $row['Leave Salary'],
      'm_special'             =>  $row['M_Special Travelling Allowance'],
      'fixed_allowance_1'     =>  $row['Fixed Allowance 01'],
      'gross_salary'          =>  $row['Gross Salary'],
      'other_deduction'       =>  $row['Other Deduction'],
      'lwf'                   =>  $row['LWF'],
      'tds'                   =>  $row['TDS'],
      'esic'                  =>  $row['ESIC'],
      'profession'            =>  $row['Profession Tax'],
      'provident_fund'        =>  $row['Provident Fund'],
      'total_deductions'      =>  $row['Total Deductions'],
      'net_pay'               =>  $row['Net Pay'],
      'epf_employer'          =>  $row['EPF Employer'],
      'eps_employer'          =>  $row['EPS Employer'],
      'esic_employer'         =>  $row['ESIC Employer'],
      'mlwf'                  =>  $row['MLWF'],
      'edli_employer'         =>  $row['EDLI Employer'],
      'pf_admin_charge'       =>  $row['PF Admin Charge'],
      'm_bonus'               =>  $row['M_Bonus'],
      'm_m_bonus'             =>  $row['M_M_Bonus'],
      'm_bonus_m'             =>  $row['M_Bonus (M)'],
      'insurance'             =>  $row['Insurance'],
      'wc_policy'             =>  $row['WC Policy'],
      'ctc'                   =>  $row['CTC'],
    ];

    return new CrudeSalary($data);
  }

  public function headingRow(): int
  {
    return 2;
  }

  public function batchSize(): int
  {
    return 1000;
  }
}
