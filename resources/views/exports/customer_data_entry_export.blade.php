<table>
    <thead>
        <tr>
            <th>Sr. No.</th>
            <!-- <th>Brand</th>
            <th>Channel</th>
            <th>Chain Name</th>
            <th>Store Name</th>
            <th>BA Name</th> -->
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
            <th>Date</th>
            <th>Customer Name</th>
            <th>Email</th>
            <th>Product Brought</th>
            <th>Sample Given</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customer_data_entries as $user)
        <tr>

            <td>{{ $loop->index + 1 }}</td>
            <td>{{ ucwords(strtolower($user['user']['brand'])) }}</td>
            <td>{{ ucwords(strtolower($user['user']['region'])) }}</td>
            <td>{{ $user['user']['channel'] }}</td>
            <td>{{ $user['user']['chain_name'] }}</td>
            <td>{{ $user['user']['city'] }}</td>
            <td>{{ $user['user']['state'] }}</td>
            <td>{{ $user['user']['employee_code'] }}</td>
            <td>{{ $user['user']['name'] }}</td>
            <td>{{ $user['user']['ba_name'] }}</td>
            <td>{{ $user['user']['pms_emp_id'] }}</td>
            <td>{{ $user['user']['supervisor_name'] }}</td>
            <td>{{ \Carbon\Carbon::parse($user['created_at'])->format('Y-m-d ')}}</td>
            <td>{{ $user['name'] }}</td>
            <td>{{ $user['email'] }}</td>
            <td>{{ $user['product_brought'] }}</td>
            <td>{{ $user['sample_given'] }}</td>
        </tr>
        @endforeach

    </tbody>
</table>