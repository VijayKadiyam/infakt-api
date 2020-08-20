<!DOCTYPE html>
<html>
<head>
  <title>Form 11</title>
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
  <div align="right">
    <b>
      New Form No.11- Declaration Form
      <br>
      <small>(To be retained by the employer for future reference)</small>
    </b>
  </div>
  <br>
  <div class="wrapper">
    <div style="float: left; width: 10%;"> 
      <img src="/images/pf-logo.jpeg" style="width: 100px; height: 100px;">
    </div>
    <div style="float: left; width: 40%">
      <b>EMPLOYEES PROVIDENT FUND ORGANIZATION</b>
      <br>
      Employees provident funds scheme, 1952 (paragraph 34 & 57) &
      <br>
      Employees pension scheme 1995 (paragraph 24)
    </div>
    <div style="float: left; width: 50%;">
      <table>
        <tr>
          <td>
            <b>Emp Code:</b> {{ $user->employee_code }}
            <br><br>
            <b>DOJ:</b> {{ $user->doj }}
          </td>
        </tr>
      </table>
      <br>
    </div>
  </div>
  <div align="center">(Declaration by a person taking up employment in any establishment on which EPF Scheme, 1952 end /of EPS1995 is applicable)</div>
  <div class="wrapper">
    <table>
      <tr>
        <td>1</td>
        <td colspan="5">Name of the member</td>
        <td colspan="5">{{ $user->full_name }}</td>
      </tr>
      <tr>
        <td>2</td>
        <td colspan="5">Father’s Name</td>
        <td colspan="5">{{ $user->father_name }}</td>
      </tr>
      <tr>
        <td>3</td>
        <td colspan="5">Date of Birth (DD/MM/YYYY)</td>
        <td colspan="5">{{ $user->dob }}</td>
      </tr>
      <tr>
        <td>4</td>
        <td colspan="5">Gender: ( male / Female /Transgender )</td>
        <td colspan="5">{{ $user->gender }}</td>
      </tr>
      <tr>
        <td>5</td>
        <td colspan="5">Marital Status (married /Unmarried /widow/divorce)</td>
        <td colspan="5">{{ $user->marital_status }}</td>
      </tr>
      <tr>
        <td>6</td>
        <td colspan="5">(a) Email ID: <br><br> (b) Mobile No:</td>
        <td colspan="5">{{ $user->email }} <hr> {{ $user->phone }} </td>
      </tr>
      <tr>
        <td><span class="required">*</span>7</td>
        <td colspan="5">Whether earlier a member of Employees ‘provident Fund Scheme 1952</td>
        <td colspan="5">YES</td>
      </tr>
      <tr>
        <td><span class="required">*</span>8</td>
        <td colspan="5">Whether earlier a member of Employees ‘Pension Scheme ,1995</td>
        <td colspan="5">YES</td>
      </tr>
      <tr>
        <td rowspan="6">9</td>
        <td colspan="10"><b>If response to any or both of (7) & (8) above is yes. MANDATORY FILL UP THE (COLUMN 9)</b></td>
      </tr>
      <tr>
        <td colspan="5">a) Universal Account Number(UAN)</td>
        <td colspan="5">{{ $user->uan_no }}</td>
      </tr>
      <tr>
        <td colspan="5">b) Previous PF a/c No</td>
        <td colspan="5">{{ $user->pf_no }}</td>
      </tr>
      <tr>
        <td colspan="5">c) Date of exit from previous employment (DD/MM/YYY)</td>
        <td colspan="5"></td>
      </tr>
      <tr>
        <td colspan="5">d) Scheme Certificate No (if Issued )</td>
        <td colspan="5"></td>
      </tr>
      <tr>
        <td colspan="5">e) Pension Payment Order (PPO)No (if Issued)</td>
        <td colspan="5"></td>
      </tr>
      <tr>
        <td rowspan="4">10</td>
        <td colspan="5">a) International Worker:</td>
        <td colspan="5"></td>
      </tr>
      <tr>
        <td colspan="5">b) If Yes , State Country Of Origin (India /Name of Other Country)</td>
        <td colspan="5"></td>
      </tr>
      <tr>
        <td colspan="5">c) Passport No</td>
        <td colspan="5"></td>
      </tr>
      <tr>
        <td colspan="5">d) Validity Of Passport (DD/MM/YYY) to(DD/MM/YYY)</td>
        <td colspan="5"></td>
      </tr>
      <tr>
        <td rowspan="4">11</td>
        <td colspan="10"><b>KYC Details: (attach Self attested copies of following KYCs) **</b></td>
      </tr>
      <tr>
        <td colspan="5">a) Bank Account No .& IFS code</td>
        <td colspan="5">{{ $user->bank_acc_no }} - {{ $user->bank_ifsc_code }}</td>
      </tr>
      <tr>
        <td colspan="5">b) AADHAR Number (12 Digit)</td>
        <td colspan="5">{{ $user->adhaar_no }}</td>
      </tr>
      <tr>
        <td colspan="5">c) Permanent Account Number (PAN),If available</td>
        <td colspan="5">{{ $user->pan_no }}</td>
      </tr>
    </table>
  </div>
  <br>
  <div align="center">
    <b><u>UNDERTAKING</u></b>
  </div>
  1). Certified that the Particulars are true to the best of my Knowledge
  <br>
  2). I authorize EPFO to use my Aadhar for verification / e KYC purpose for service delivery
  <br>
  3). Kindly transfer the funds and service details, if applicable if applicable, from the previous PF account as declared above to the present P.F Account(The Transfer Would be possible only if the identified KYC details approved by previous employer has been verified by present employer
  <br>
  4). In case of changes In above details the same Will be intimate to employer at the earliest
  <br><br>
  <div class="wrapper">
    <div style="float: left; width: 50%;">
      Date: 
      <br>
      Place: 
    </div>
    <div style="float: left; width: 50%;">
      <img src="{{ env('AWS_LINK') }}{{ $user->form_11_sign_path }}" style="width: 100px; height: 100px;">
      <br>
      Signature of member
    </div>
  </div>
  <br><br>
  <div align="center"><b><u>DECLARATION BY PRESENT EMPLOYER</u></b></div>
  A). The member Mr./Ms./Mrs <b>{{ $user->full_name }} {{ $user->father_name }} {{ $user->surname }}</b> has joined on ................ and has been allotted PF Number..................................... 
  <br>
  B). In case person was earlier not a member of EPF Scheme ,1952 and EPS,1995
  <br>
  <ul>
    <li>
      <b>(Post allotment of UAN )</b> The UAN Allotted for the member is..............
    </li>
    <li>
      <b>Please tick the Appropriate Option:</b>
    </li>
    <li>
      The KYC details of the above member in the UAN database
    </li>
    <ul>
      <li>Have not been uploaded</li>
      <li>Have been uploaded but not approved</li>
      <li>Have been uploaded and approved with DSC</li>
    </ul>
  </ul>
  C). In case the person was earlier a member of EPF Scheme ,1952 and EPS, 1995:
  <ul>
    <li>The above PF account number /UAN of the member as mentioned in (a) above has been tagged with his /her UAN/previous member ID as declared by member</li>
    <li>Please Tick the Appropriate Option</li>
    <ul>
      <li>The KYC details of the above member in the UAN database have been approved with digital signature Certificate and transfer request has been generated on portal.</li>
      <li>As the DSC of establishment are not registered With EPFO the member has been informed to file physical claim (Form13) for transfer of funds from his previous establishment.</li>
    </ul>
  </ul>
  <br><br><br><br>
  <div class="wrapper">
    <div style="float: left; width: 50%">
      Date
    </div>
    <div style="float: left; width: 50%">
      <img src="https://pmsallcdn.s3.ap-south-1.amazonaws.com/documentation/authorized-signatory.png" style="width: 350px; height: 100px;">
      <br>
      Signature of Employer With seal of Establishment
    </div>
  </div>
</body>
</html>