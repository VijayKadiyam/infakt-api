<table>
    <thead>
    <tr>
        <th>Sr. No.</th>
        <th>Brand</th>
        <th>Region</th>
        <th>Channel</th>
        <th>Chain Name</th>
        <th>City</th>
        <th>State</th>
        <th>Store Code</th>
        <th>Store Name</th>
        <th>BA Name</th>
        <th>PMS EMP ID</th>
        <th>Supervisor Name</th>
        @for ($i = 1; $i <= $daysInMonth; $i++)
            <th>{{ $i }}</th>       
        @endfor
        <th>Total Present Days</th>
        <th>Total Weekly Off Days</th>
        <th>Total Leaves</th>
        <th>Total Absent Days</th>
                      
    </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $user['brand'] }}</td>
                <td>{{ $user['region'] }}</td>
                <td>{{ $user['channel'] }}</td>
                <td>{{ $user['chain_name'] }}</td>
                <td>{{ $user['city'] }}</td>
                <td>{{ $user['state'] }}</td>
                <td>{{ $user['employee_code'] }}</td>
                <td>{{ $user['name'] }}</td>
                <td>{{ $user['ba_name'] }}</td>
                <td>{{ $user['pms_emp_id'] }}</td>
                <td>{{ $user['supervisor_name'] }}</td>
                @for ($i = 1; $i <= $daysInMonth; $i++)
                    <td>
                        @if (isset($user['attendances'][$i]))
                            <span>{{ $user['attendances'][$i]['session_type'] }}</span>
                        @else
                            <span v-else> ABSENT</span>  
                        @endif
                    </td>
                @endfor
                <td>{{ $user['present_count'] }}</td>
                <td>{{ $user['weekly_off_count'] }}</td>
                <td>{{ $user['leave_count'] }}</td>
                <td>{{ $daysInMonth - $user['day_count'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>