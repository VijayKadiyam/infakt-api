<!DOCTYPE html>
<html>
<head>
  <title>PDS Form</title>

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
<body>
  
  <div class="wrapper">
    <div style="float: left; width: 80%">
      <div align="center">
        <h1><u>Poussé Management Services Pvt. Ltd. - PDS</u></h1>
      </div>
      <!-- Name -->
      <table>
        <thead>
          <tr>
            <th><span class="required">*</span>Name</th>
            <th><span class="required">*</span>Father Name</th>
            <th><span class="required">*</span>Surname</th>
            <th><span class="required">*</span>Mother Name</th>
          </tr>
        </thead>
        <tbody>
          <tr align="center">
            <td>{{ $user->full_name }}</td>
            <td>{{ $user->father_name }}</td>
            <td>{{ $user->surname }}</td>
            <td>{{ $user->mother_name }}</td>
          </tr>
        </tbody>
      </table>
      <!-- End Name -->
      <!-- DOB -->
      <table>
        <thead>
          <tr>
            <th><span class="required">*</span>DOB</th>
            <th><span class="required">*</span>Marital Status</th>
            <th><span class="required">*</span>Pan No</th>
            <th><span class="required">*</span>Adhaar No</th>
          </tr>
        </thead>
        <br>
        <tbody>
          <tr align="center">
            <td>{{ $user->dob }}</td>
            <td>{{ $user->marital_status }}</td>
            <td>{{ $user->pan_no }}</td>
            <td>{{ $user->adhaar_no }}</td>
          </tr>
        </tbody>
      </table>
      <!-- End DOB -->
    </div>
    <div style="float: left; width: 20%" >
      
      <img src="{{ env('AWS_LINK') }}{{ $user->photo_path }}" style="width: 170px; height: 170px; margin: 20px; border: 2px solid black;">
      
    </div>
  </div>  
  <div align="center">(*) Fields are Mandatory</div>
  <!-- Address -->
  <div class="wrapper">
    <div style="float: left; width: 50%"> 
      <div align="center"><span class="required">*</span><u><b>Present Address</b></u></div>
      <table>
        <tr>
          <td><b>Room No.</b></td>
          <td colspan="3">{{ $user->pre_room_no }}</td>
        </tr>
        <tr>
          <td><b>Building</b></td>
          <td colspan="3">{{ $user->pre_building }}</td>
        </tr>
        <tr>
          <td><b>Area</b></td>
          <td colspan="3">{{ $user->pre_area }}</td>
        </tr>
        <tr>
          <td><b>Road</b></td>
          <td colspan="3">{{ $user->pre_road }}</td>
        </tr>
        <tr>
          <td><b>City</b></td>
          <td colspan="3">{{ $user->pre_city }}</td>
        </tr>
        <tr>
          <td><b>State</b></td>
          <td colspan="3">{{ $user->pre_state }}</td>
        </tr>
        <tr>
          <td><b>Pin Code</b></td>
          <td colspan="3">{{ $user->pre_pincode }}</td>
        </tr>
        <tr>
          <td><b>Mobile No</b></td>
          <td colspan="3">{{ $user->pre_mobile }}</td>
        </tr>
        <tr>
          <td><b>Email</b></td>
          <td colspan="3">{{ $user->pre_email }}</td>
        </tr>
      </table>
    </div>
    <div style="float: left; width: 50%"> 
      <div align="center"><span class="required">*</span><u><b>Permanent Address</b></u></div>
      <table>
        <tr>
          <td><b>Room No.</b></td>
          <td colspan="3">{{ $user->per_room_no }}</td>
        </tr>
        <tr>
          <td><b>Building</b></td>
          <td colspan="3">{{ $user->per_building }}</td>
        </tr>
        <tr>
          <td><b>Area</b></td>
          <td colspan="3">{{ $user->per_area }}</td>
        </tr>
        <tr>
          <td><b>Road</b></td>
          <td colspan="3">{{ $user->per_road }}</td>
        </tr>
        <tr>
          <td><b>City</b></td>
          <td colspan="3">{{ $user->per_city }}</td>
        </tr>
        <tr>
          <td><b>State</b></td>
          <td colspan="3">{{ $user->per_state }}</td>
        </tr>
        <tr>
          <td><b>Pin Code</b></td>
          <td colspan="3">{{ $user->per_pincode }}</td>
        </tr>
        <tr>
          <td><b>Mobile No</b></td>
          <td colspan="3">{{ $user->per_mobile }}</td>
        </tr>
        <tr>
          <td><b>Email</b></td>
          <td colspan="3">{{ $user->per_email }}</td>
        </tr>
      </table>
      <br>
    </div>
  </div>
  <!-- End Address -->
  <!-- Bank Details -->
  <div><b><u>Bank Details</u></b></div>
  <div class="wrapper">
    <table>
      <tr>
        <td><span class="required">*</span><b>Bank Name</b></td>
        <td>{{ $user->bank_name }}</td>
        <td><span class="required">*</span><b>IFSC Code</b></td>
        <td>{{ $user->bank_ifsc_code }}</td>
      </tr>
      <tr>
        <td><span class="required">*</span><b>Bank A/c No.</b></td>
        <td>{{ $user->bank_acc_no }}</td>
        <td><span class="required">*</span><b>Branch Name</b></td>
        <td>{{ $user->bank_branch_name }}</td>
      </tr>
    </table>
  </div>
  <!-- End Bank Details -->
  <br>
  <!-- Work Experience -->
  <div><b><u>Work Experience</u></b></div>
  <div class="wrapper">
    <table>
      <thead>
        <tr>
          <th>Sr. No.</th>
          <th>Company Name</th>
          <th>From</th>
          <th>To</th>
          <th>Designation</th>
          <th>UAN / PF No.</th>
          <th>ESIC No.</th>
        </tr>
      </thead>
      @foreach($user->user_work_experiences as $work_experience)
      <tr align="center">
        <td>{{ $loop->iteration }}</td>
        <td>{{ $work_experience->company_name }}</td>
        <td>{{ $work_experience->from }}</td>
        <td>{{ $work_experience->to }}</td>
        <td>{{ $work_experience->designation }}</td>
        <td>{{ $work_experience->uan_no }}</td>
        <td>{{ $work_experience->esic_no }}</td>
      </tr>
      @endforeach
    </table>
  </div>
  <!-- End Work Experience -->
  <br>
  <!-- Education -->
  <div><b><u>Education</u></b></div>
  <div class="wrapper">
    <table>
      <thead>
        <tr>
          <th>Examination</th>
          <th>School/College/University</th>
          <th>Passing Year</th>
          <th>% Marks</th>
        </tr>
      </thead>
      @foreach($user->user_educations as $education)
      <tr align="center">
        <td>{{ $education->examination }}</td>
        <td>{{ $education->school }}</td>
        <td>{{ $education->passing_year }}</td>
        <td>{{ $education->percent }}</td>
      </tr>
      @endforeach
    </table>
  </div>
  <!-- End Education -->
  <br>
  <!-- Family Details -->
  <div><b><u>Family Details</u></b></div>
  <div class="wrapper">
    <table>
      <thead>
        <tr>
          <th>Sr. No.</th>
          <th>Name</th>
          <th>Date of Birth</th>
          <th>Gender</th>
          <th>Relation</th>
          <th>Occupation</th>
          <th>Contact Number</th>
        </tr>
      </thead>
      @foreach($user->user_family_details as $family_detail)
      <tr align="center">
        <td>{{ $loop->iteration }}</td>
        <td>{{ $family_detail->name }}</td>
        <td>{{ $family_detail->dob }}</td>
        <td>{{ $family_detail->gender }}</td>
        <td>{{ $family_detail->relation }}</td>
        <td>{{ $family_detail->occupation }}</td>
        <td>{{ $family_detail->contact_number }}</td>
      </tr>
      @endforeach
    </table>
  </div>
  <!-- End Family Details -->
  <br>
  <!-- References: ( Supervisors in Previous / current Job / Colleagues / Other Professionals ) -->
  <div><b><u>References: ( Supervisors in Previous / current Job / Colleagues / Other Professionals )</u></b></div>
  <div class="wrapper">
    <table>
      <thead>
        <tr>
          <th>Sr. No.</th>
          <th>Name</th>
          <th>Company Name</th>
          <th>Designation</th>
          <th>Tel Nos.</th>
        </tr>
      </thead>
      @foreach($user->user_references as $reference)
      <tr align="center">
        <td>{{ $loop->iteration }}</td>
        <td>{{ $reference->name }}</td>
        <td>{{ $reference->company_name }}</td>
        <td>{{ $reference->designation }}</td>
        <td>{{ $reference->contact_number }}</td>
      </tr>
      @endforeach
    </table>
  </div>
  <!-- End References: ( Supervisors in Previous / current Job / Colleagues / Other Professionals ) -->
  <br>
  <div><b>Current Salary / Last Salary drawn: Rs. {{ $user->salary ?? '__________________' }}/- pm Gross.</b></div>
  <br>
  <div><b><u>Documents Required</u></b></div>
  <ul>
    <li>Resume / Curriculum Vite / Bio data</li>
    <li>2 Photographs</li>
    <li>Photocopy of Residential Proof / Driving Licence</li>
    <li>Photocopy of Education Proof</li>
    <li>Photocopy of Pan Card</li>
    <li>Photo Copy of ADHAAR Card</li>
    <li>In case of ESI member, Photocopy of ESI card</li>
    <li>PF Account number if currently covered under PF</li>
    <li>Blank cheque – cancelled of currently operating bank account</li>
    <li>Last 2 month’s Payslip</li>
  </ul>
  <div class="wrapper">
    <div style="float: left; width: 50%">
      <b>Date:</b>
    </div>
    <div style="float: left; width: 50%">
      <img src="{{ env('AWS_LINK') }}{{ $user->pds_form_sign_path }}" style="width: 100px; height: 100px;">
      <br>
      <b>Applicant’s Signature and Name</b>
    </div>
  </div>
  <br>
  <hr>
  <div align="center"><b><u>For Office Use Only</u></b></div>
  <br>
  <div align="center"><b>Documents Checked by: ________________ Date: _______________________ Sign: __________________</b></div>
  <br>
  <div align="center"><b>Info Recorded by : ____________________ Date : _______________________ Sign: __________________</b></div>
</body>
</html>