<!DOCTYPE html>
<html>
<head>
  <title>Graduity Form</title>
  <style type="text/css">
    .wrapper {
      width: 100%;
    }
    table {
      width: 100%;
      table-layout: fixed;
      border: 1px solid black;
      border-collapse: collapse;
    }
    tr, td {
      border: 1px solid black;
      padding-left: 10px;
    }
    .required {
      color: red;
    }
  </style>
</head>
<body style="padding: 20px;">
  <div align="center">
    <h1><b>
      Payment of Gratuity (Central) Rules 
      <br>
      FORM 'F'
    </b></h1>
    See sub-rule (1) of Rule 6
    <br><br>
    <b>Nomination</b>
  </div>
  To,
  <br>
  Poussé Management Services Pvt.Ltd,
  <br>
  306, Corporate Center, Nirmal Life Style, LBS Road, Mulund West, Mumbai- 400080
  <br><br>
  I, Shri /Shrimati /Kumari <b>{{ $user->full_name }} {{ $user->father_name }} {{ $user->surname }}</b> whose particulars are given in the statement below, hereby nominate the person(s) mentioned below to receive the gratuity payable after my death as also the gratuity standing to my credit in the event of my death before that amount has become payable, or having become payable has not been paid and direct that the said amount of gratuity shall be paid in proportion indicated against the name(s) of the nominee(s).
  <br><br>
  2.  I hereby certify that the person(s) mentioned is/are a member(s) of my family within the meaning of clause
  (h) of Section 2 of the Payment of Gratuity Act, 1972.
  <br><br>
  3.  I hereby declare that I have no family within the meaning of clause (h) of Section 2 of the said Act.
  <br><br>
  4 (a) My father/mother/parents is/are not dependent on me.
  <br>
  (b) My husband's father/mother/parents is/are not dependent on my husband.
  <br><br>
  5.  I have excluded my husband from my family by a notice dated the ______________________  to the controlling authority in terms of the proviso to clause (h) of Section 2 of the said Act.
  <br><br>
  6.  Nomination made herein invalidates my previous nomination.
  <br><br>
  <div align="center"><b>Nominee(s)</b></div>
  <br>
  <div class="wrapper">
    <table>
      <thead>
        <tr>
          <th>Sr. No.</th>
          <th>Name in full with full address of nominee(s)</th>
          <th>Relationship with the employee</th>
          <th>Age of nominee</th>
          <th>Proportion by which the gratuity will be shared</th>
        </tr>
      </thead>
      <tbody>
        @foreach($user->user_family_details as $family_detail)
        <tr align="center">
          <td>{{ $loop->iteration }}</td>
          <td>
            {{ $family_detail->name }}
            <br>
            {{ $user->pre_room_no }}, {{ $user->pre_building }}, {{ $user->pre_building }}, {{ $user->pre_area }}, {{ $user->pre_road }}, {{ $user->pre_city }}, {{ $user->pre_state }}, {{ $user->pincode }}
          </td>
          <td>{{ $family_detail->relation }}</td>
          <td>{{ $family_detail->dob }}</td>
          <td></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <br><br>
  <hr>
  <br>
  <div align="center"><b>Statement</b></div>
  <b>1. Name of employee in full: </b>{{ $user->full_name }} {{ $user->father_name }} {{ $user->surname }}   
  <br>
  <b>2. Sex: </b>    
  <br>
  <b>3.  Religion: </b>     
  <br>
  <b>4. Whether unmarried/married/widow/widower: </b> {{ $user->marital_status }}
  <br>
  <b>5. Department/Branch/Section where employed</b>          
  <br>
  <b>6. Post held with Ticket No. or Serial No., if any </b>          
  <br>
  <b>7. Date of appointment  </b>           
  <br>
  <b>8. Permanent address: </b>{{ $user->per_room_no }}, {{ $user->per_building }}, {{ $user->per_building }}, {{ $user->per_area }}, {{ $user->per_road }}, {{ $user->per_city }}, {{ $user->per_state }}, {{ $user->pincode }}
  <br>
  <hr>
  <br>
  <div class="wrapper">
    <div style="float: left; width: 50%">
      Place:
      <br>
      Date:
    </div>
    <div style="float: left; width: 50%">
      <img src="{{ env('AWS_LINK') }}{{ $user->pds_form_sign_path }}" style="width: 100px; height: 100px;">
      <br>
      Signature/Thumb-impression of the Employee
    </div>
  </div>
  <br>
  <hr>
  <br>
  <div align="center"><b>Declaration by Witnesses</b></div>
  Nomination signed/thumb-impressed before me
  <div class="wrapper">
    <div style="float: left; width: 50%">
      Name in full and full address of witnesses
      <br>
      1. __________________________
      <br>
      2. __________________________
    </div>
    <div style="float: left; width: 50%">
      Signature of Witnesses.
      <br>
      1. __________________________
      <br>
      2. __________________________
      <br><br>
    </div>
  </div>
  <br><hr><br>
  <div align="center"><b>Certificate by the Employer</b></div>
  Certified that the particulars of the above nomination have been verified and recorded in this establishment. Employer's 
  <br><br>
  <div class="wrapper">
    <div style="float: left; width: 50%">
      Reference No., if any   ______________________
      <br>
      <b>Poussé Management Services Pvt Ltd. </b>
      <br>
      306, Corporate Center, Nirmal Life Style,
      <br>
      LBS Road, Mulund West, 
      <br><br>
      Date:
    </div>
    <div style="float: left; width: 50%">
      Signature of the employer/Officer 
      <br>
      <b>Poussé Management Services Pvt Ltd</b>
      <br><br><br>
      Authorised Signatory
      <br>Mumbai - 400080
    </div>
  </div>
</body>
</html>