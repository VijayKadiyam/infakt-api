@extends('letters.index')

@section('letter')
<style type="text/css">
  table, tr, td {
    border: 1px solid black;
    border-collapse: collapse
  }
</style>
<div align="center"><b>Salary Slip</b></div>
<br><br>
<table align="center">
  <tr>
    <th>Employee ID</th>
    <th>{{ $salary->emp_id }}</th>
  </tr>
  <tr>
    <td>Employee Name</td>
    <td>{{ $user->name }} </td>
  </tr>
  <tr>
    <td>Month</td>
    <td>{{ $salary->month }}</td>
  </tr>
  <tr>
    <td>Year</td>
    <td>{{ $salary->year }}</td>
  </tr>
  <tr>
    <td>Basic Salary</td>
    <td>{{ $salary->basic_salary }}</td>
  </tr>
  <tr>
    <td>Dearness Allowance</td>
    <td>{{ $salary->dearness_allowance }}</td>
  </tr>
  <tr>
    <td>HRA</td>
    <td>{{ $salary->hra }}</td>
  </tr>
  <tr>
    <td>Conveyance Allowance</td>
    <td>{{ $salary->conveyance_allowance }}</td>
  </tr>
  <tr>
    <td>Mobile Charges</td>
    <td>{{ $salary->mobile_charges }}</td>
  </tr>
  <tr>
    <td>Communication</td>
    <td>{{ $salary->communication }}</td>
  </tr>
  <tr>
    <td>Medical Allowance</td>
    <td>{{ $salary->medical_allowance }}</td>
  </tr>
  <tr>
    <td>Variable Pay</td>
    <td>{{ $salary->variable_pay }}</td>
  </tr>
  <tr>
    <td>Special Allowance</td>
    <td>{{ $salary->special_allowance }}</td>
  </tr>
  <tr>
    <td>Bonus (y)</td>
    <td>{{ $salary->bonus_y }}</td>
  </tr>
  <tr>
    <td>Incentives</td>
    <td>{{ $salary->incentives }}</td>
  </tr>
  <tr>
    <td>Expenses Reimbursement</td>
    <td>{{ $salary->expense_reimbursement }}</td>
  </tr>
  <tr>
    <td>Other Earning</td>
    <td>{{ $salary->other_earnings }}</td>
  </tr>
  <tr>
    <td>Bonus (M)</td>
    <td>{{ $salary->bonus_m }}</td>
  </tr>
  <tr>
    <td>Incentive 1</td>
    <td>{{ $salary->incentive_1 }}</td>
  </tr>
  <tr>
    <td>LTA</td>
    <td>{{ $salary->lta }}</td>
  </tr>
  <tr>
    <td>Travelling Allowance 75</td>
    <td>{{ $salary->travelling }}</td>
  </tr>
  <tr>
    <td>Daily Allowance</td>
    <td>{{ $salary->daily_allowance }}</td>
  </tr>
  <tr>
    <td>Other Allowance</td>
    <td>{{ $salary->other_allowance }}</td>
  </tr>
  <tr>
    <td>Educational Allowance</td>
    <td>{{ $salary->educational_allowance }}</td>
  </tr>
  <tr>
    <td>Deputation Allowance</td>
    <td>{{ $salary->deputation_allowance }}</td>
  </tr>
  <tr>
    <td>Leave Salary</td>
    <td>{{ $salary->leave_salary }}</td>
  </tr>
  <tr>
    <td>M_Special Travelling Allowance</td>
    <td>{{ $salary->m_special }}</td>
  </tr>
  <tr>
    <td>Fixed Allowance 01</td>
    <td>{{ $salary->fixed_allowance_1 }}</td>
  </tr>
  <tr>
    <td>Gross Salary</td>
    <td>{{ $salary->gross_salary }}</td>
  </tr>
  <tr>
    <td>Other Deduction</td>
    <td>{{ $salary->other_deduction }}</td>
  </tr>
  <tr>
    <td>LWF</td>
    <td>{{ $salary->lwf }}</td>
  </tr>
  <tr>
    <td>TDS</td>
    <td>{{ $salary->tds }}</td>
  </tr>
  <tr>
    <td>ESIC</td>
    <td>{{ $salary->esic }}</td>
  </tr>
  <tr>
    <td>Profession Tax</td>
    <td>{{ $salary->profession }}</td>
  </tr>
  <tr>
    <td>Provident Fund</td>
    <td>{{ $salary->provident_fund }}</td>
  </tr>
  <tr>
    <td>Total Deductions</td>
    <td>{{ $salary->total_deductions }}</td>
  </tr>
  <tr>
    <td>Net Pay</td>
    <td>{{ $salary->net_pay }}</td>
  </tr>
  <tr>
    <td>EPF Employer</td>
    <td>{{ $salary->epf_employer }}</td>
  </tr>
  <tr>
    <td>EPS Employer</td>
    <td>{{ $salary->eps_employer }}</td>
  </tr>
  <tr>
    <td>ESIC Employer</td>
    <td>{{ $salary->esic_employer }}</td>
  </tr>
  <tr>
    <td>MLWF</td>
    <td>{{ $salary->mlwf }}</td>
  </tr>
  <tr>
    <td>EDLI Employer</td>
    <td>{{ $salary->edli_employer }}</td>
  </tr>
  <tr>
    <td>PF Admin Charge</td>
    <td>{{ $salary->pf_admin_charge }}</td>
  </tr>
  <tr>
    <td>M_Bonus</td>
    <td>{{ $salary->m_bonus }}</td>
  </tr>
  <tr>
    <td>M_M_Bonus</td>
    <td>{{ $salary->m_m_bonus }}</td>
  </tr>
  <tr>
    <td>M_Bonus (M)</td>
    <td>{{ $salary->m_bonus_m }}</td>
  </tr>
  <tr>
    <td>Insurance</td>
    <td>{{ $salary->insurance }}</td>
  </tr>
  <tr>
    <td>WC Policy</td>
    <td>{{ $salary->wc_policy }}</td>
  </tr>
  <tr>
    <td>CTC</td>
    <td>{{ $salary->ctc }}</td>
  </tr>

</table>
@endsection