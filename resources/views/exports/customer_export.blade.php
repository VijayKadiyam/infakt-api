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
            <th>PMS EMP ID</th>
            <th>Supervisor Name</th>
            <th>Date</th>
            <th>No Of Customer</th>
            <th>No Of Billed Customer</th>
            <th>More Than Two</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customers as $user)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ $user['user']['region'] }}</td>
            <td>{{ $user['user']['city'] }}</td>
            <td>{{ $user['user']['state'] }}</td>
            <td>{{ $user['user']['employee_code'] }}</td>
            <td>{{ $user['user']['name'] }}</td>
            <td>{{ $user['user']['ba_name'] }}</td>
            <td>{{ $user['user']['pms_emp_id'] }}</td>
            <td>{{ $user['user']['supervisor_name'] }}</td>
            <td>{{ \Carbon\Carbon::parse($user['created_at'])->format('Y-m-d ')}}</td>
            <td>{{ $user['no_of_customer'] }}</td>
            <td>{{ $user['no_of_billed_customer'] }}</td>
            <td>{{ $user['more_than_two'] }}</td>
        </tr>
        @endforeach

    </tbody>
</table>