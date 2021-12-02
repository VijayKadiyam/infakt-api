<table>
    <thead>
    <tr>
        <th>Sr. No.</th>
        <th>Region</th>
        <th>City</th>
        <th>State</th>
        <th>Store Code</th>
        <th>Store Name</th>
        <th>BA Name</th>
        <th>Supervisor Name</th>
        <th>Date</th>
        <th>Login Status</th>
        <th>Login Time</th>
        <th>Logout Time</th>
        <th>Login Selfie</th>
        <th>Logout Selfie</th>
        <th>Login Address</th>
        <th>Logout Address</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($userAttendances as $userAttendance)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ $userAttendance->user->region }}</td>
            <td>{{ $userAttendance->user->city }}</td>
            <td>{{ $userAttendance->user->state }}</td>
            <td>{{ $userAttendance->user->employee_code }}</td>
            <td>{{ $userAttendance->user->name }}</td>
            <td>{{ $userAttendance->user->ba_name }}</td>
            <td>{{ $userAttendance->user->supervisor_name }}</td>
            <td>{{ $userAttendance->date }}</td>
            <td>{{ $userAttendance->session_type }}</td>
            <td>{{ $userAttendance->login_time }}</td>
            <td>{{ $userAttendance->logout_time }}</td>
            <td>
                <a target="_blank" href="{{ env('BASE_URL') }}{{ $userAttendance->selfie_path }}">
                    Click to view Image
                </a>
            </td>
            <td>
                <a target="_blank" href="{{ env('BASE_URL') }}{{ $userAttendance->logout_selfie_path }}">
                    Click to view Image
                </a>
            </td>
            <td>{{ $userAttendance->login_address }}</td>
            <td>{{ $userAttendance->logout_address }}</td>
        </tr>    
        @endforeach
    </tbody>
</table>