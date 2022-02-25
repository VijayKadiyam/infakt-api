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
        <th>No Of Offtake days</th>
        <th>No Of Present days</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($Oftake_users as $offtake_user)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ ucwords(strtolower($offtake_user['brand'])) }}</td>
            <td>{{ ucwords(strtolower($offtake_user['region'])) }}</td>
            <td>{{ $offtake_user['channel'] }}</td>
            <td>{{ $offtake_user['chain_name'] }}</td>
            <td>{{ $offtake_user['city'] }}</td>
            <td>{{ $offtake_user['state'] }}</td>
            <td>{{ $offtake_user['employee_code'] }}</td>
            <td>{{ $offtake_user['name'] }}</td>
            <td>{{ $offtake_user['ba_name'] }}</td>
            <td>{{ $offtake_user['pms_emp_id'] }}</td>
            <td>{{ $offtake_user['supervisor_name'] }}</td>
            <td>{{ $offtake_user['Offtake_count'] }}</td>
            <td>{{ $offtake_user['present_days_count'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>