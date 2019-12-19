Hi, {{ $user->full_name }},
<br><br>
Greetings for the day.
<br><br>
Please refer the Experience Letter as attached below.
<br><br>
@if($letter->signed == 1) 
  Your Signature:
  <br>
  <img src="https://pmsallcdn.s3.ap-south-1.amazonaws.com/documentation/{{ $letter->sign_path }}" height="100px; width: 200px;">
@else
  Please visit the mobile app and sign the Experience Letter.
@endif
<br><br><br>
Regards,
<br>
PMS
