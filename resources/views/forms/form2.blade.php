<!DOCTYPE html>
<html>
<head>
  <title>Form 2</title>
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
  <div align="right">(FORM 2 REVISED)</div>
  <br>
  <div align="center"><b>NOMINATION AND DECLARATION FORM FOR UNEXEMPTED/EXEMPTED ESTABLISHMENTS</b></div>
  <div align="center">
    <small>
      Declaration and Nomination Form under the Employees Provident Funds and Employees Pension Schemes
      <br>
      (Paragraph 33 and 61 (1) of the Employees Provident Fund Scheme 1952 and Paragraph 18 of the Employees Pension Scheme 1995)
    </small>
  </div>
  <br>
  <b>1. Name (IN BLOCK LETTERS) :</b> {{ strtoupper($user->full_name) }} {{ strtoupper($user->father_name) }} {{ strtoupper($user->surname) }}  
  <br><br>
  <b>2. Date of Birth :</b> {{ $user->dob }} <b>3. Account No.</b> 
  <br><br>
  <b>4. <span class="required">*</span>Sex : MALE/FEMALE:</b> MALE <b>5. Marital Status :</b> {{ $user->marital_status }}
  <br><br>
  <b>6. Address Permanent / Temporary :</b> {{ $user->pre_room_no }}, {{ $user->pre_building }}, {{ $user->pre_building }}, {{ $user->pre_area }}, {{ $user->pre_road }}, {{ $user->pre_city }}, {{ $user->pre_state }}, {{ $user->pincode }}
  <br><br>
  <div align="center"><b>PART – A (EPF)</b></div>
  <div align="center">
    <small>I hereby nominate the person(s)/cancel the nomination made by me previously and nominate the person(s) mentioned below to receive the amount standing to my credit in the Employees Provident Fund, in the event of my death.</small>
  </div>
  <div class="wrapper">
    <table>
      <thead>
        <tr>
          <th>Name of the Nominee (s)</th>
          <th>Address</th>
          <th>Nominee’s relationship with the member</th>
          <th>Date of Birth</th>
          <th>Total amount or share of accumulations in Provident Funds to be paid to each nominee</th>
          <th>If the nominee is minor name and address of the guardian who may receive the amount during the minority of the nominee</th>
        </tr>
      </thead>
      <tbody>
        <tr align="center">
          <td>1</td>
          <td>2</td>
          <td>3</td>
          <td>4</td>
          <td>5</td>
          <td>6</td>
        </tr>
        @foreach($user->user_family_details as $family_detail)
        <tr align="center">
          <td>{{ $family_detail->name }}</td>
          <td>{{ $user->pre_room_no }}, {{ $user->pre_building }}, {{ $user->pre_building }}, {{ $user->pre_area }}, {{ $user->pre_road }}, {{ $user->pre_city }}, {{ $user->pre_state }}, {{ $user->pincode }}</td>
          <td>{{ $family_detail->relation }}</td>
          <td>{{ $family_detail->dob }}</td>
          <td></td>
          <td></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <br>
  <div align="center">
    1. <span class="required">*</span>Certified that I have no family as defined in para 2 (g) of the Employees Provident Fund Scheme 1952 and should I acquire a family hereafter the above nomination should be deemed as cancelled.
    <br><br>
    2. <span class="required">*</span>Certified that my father/mother is/are dependent upon me.
  </div>
  <br><br><br><br>
  <div class="wrapper">
    <div style="float: left; width: 50%">
      Strike out whichever is not applicable
    </div>
    <div style="float: left; width: 50%">
      <img src="{{ env('AWS_LINK') }}{{ $user->form_2_sign_path }}" style="width: 100px; height: 100px;">
      <br>
      Signature/or thumb impression of the subscriber
    </div>
  </div>
  <br><br>
  <div align="center">
    PART – (EPS) 
    <br>
    Para 18
    <br>
    I hereby furnish below particulars of the members of my family who would be eligible to receive Widow/Children Pension in the event of my premature death in service.
  </div>
  <div class="wrapper">
    <table>
      <thead>
        <tr>
          <th>Sr. No.</th>
          <th>Name & Address of the Family Member</th>
          <th>Age</th>
          <th>Relationship with the member</th>
        </tr>
      </thead>
      <tbody>
        <tr align="center">
          <td>(1)</td>
          <td>(2)</td>
          <td>(3)</td>
          <td>(4)</td>
        </tr>
        @foreach($user->user_family_details as $family_detail)
        <tr align="center">
          <td>{{ $loop->iteration }}</td>
          <td>
            {{ $family_detail->name }}
            <br>
            {{ $user->pre_room_no }}, {{ $user->pre_building }}, {{ $user->pre_building }}, {{ $user->pre_area }}, {{ $user->pre_road }}, {{ $user->pre_city }}, {{ $user->pre_state }}, {{ $user->pincode }}
          </td>
          <td>{{ $family_detail->dob }}</td>
          <td>{{ $family_detail->relation }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <br><br>
  <div align="center">
    Certified that I have no family as defined in para 2 (vii) of the Employees’s Family Pension Scheme 1995 and should I acquire a family hereafter I shall furnish Particulars there on in the above form.
    <br><br>
    I hereby nominate the following person for receiving the monthly widow pension (admissible under para 16 2 (a) (i) & (ii) in the event of my death without leaving any eligible family member for receiving pension.
  </div>
  <div class="wrapper">
    <table>
      <thead>
        <tr>
          <th>Name & Address of the Nominee</th>
          <th>Date of Birth</th>
          <th>Relationship with the member</th>
        </tr>
      </thead>
      <tbody>
        @foreach($user->user_family_details as $family_detail)
        <tr align="center">
          <td>
            {{ $family_detail->name }}
            <br>
            {{ $user->pre_room_no }}, {{ $user->pre_building }}, {{ $user->pre_building }}, {{ $user->pre_area }}, {{ $user->pre_road }}, {{ $user->pre_city }}, {{ $user->pre_state }}, {{ $user->pincode }}
          </td>
          <td>{{ $family_detail->dob }}</td>
          <td>{{ $family_detail->relation }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <br>
  Date: 
  <div align="right">
    <img src="{{ env('AWS_LINK') }}{{ $user->form_2_sign_path }}" style="width: 100px; height: 100px;">
    <br>
    Signature or thumb impression of the subscriber
  </div>
  <br>
  <hr>
  <div align="center">
    <b>CERTIFICATE BY EMPLOYER</b>
    <br><br>
    Certified that the above declaration and nomination has been signed / thumb impressed before me by Shri / Smt./Miss <b>{{ $user->full_name }} {{ $user->father_name }} {{ $user->surname }}</b> employed in my establishment after he/she has read the entries / the entries have been read over to him/her by me and got confirmed by him/her.
    <br><br><br><br>
  </div>
  <div class="wrapper">
    <div style="float: left; width: 50%">
      Date: 
      <br><br><br><br>
      Name & address of the Factory /Establishment
    </div>
    <div style="float: left; width: 50%">
      <img src="https://pmsallcdn.s3.ap-south-1.amazonaws.com/documentation/authorized-signatory.png" style="width: 350px; height: 100px;">
      <br>
      Signature of the employer or other authorised officer of the establishment
      Place : 
      <br><br>
      Date :
    </div>
  </div>
</body>
</html>