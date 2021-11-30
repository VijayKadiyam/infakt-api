Dear Sir/Madam,
<br><br>
Please refer to the attached document.
<br><br>
<br>
<b>Date:</b> {{ $todayDate }}
<br>
@if($supervisorName == '')
    <b>Document Name:</b> BA-Report-{{ $todayDate }}.xlsx
    <br>
    <b>Document Path:</b> <a href="{{ env('BASE_URL') }}storage/reports/{{ $todayDate }}/BA-Report-{{ $todayDate }}.xlsx">Click to view report</a>
@else
    <b>Document Name:</b> {{ $supervisorName }}-BAs-Report-{{ $todayDate }}.xlsx
    <br>
    <b>Document Path:</b> <a href="{{ env('BASE_URL') }}storage/reports/{{ $todayDate }}/{{ $supervisorName }}-BAs-Report-{{ $todayDate }}.xlsx">Click to view report</a>
@endif
<br><br>
<b>
NOTE: This is an auto-email from the system
<br><br>
Thanks & Regards,
<br>
PMS Team
</b>