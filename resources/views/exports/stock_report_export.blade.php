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
            <th>Opening Stocks value</th>
            <th>Received Stocks value</th>
            <th>Stocks Returned value</th>
            <th>Offtake value</th>
            <th>Offtake Returned value</th>
            <th>Closing Stocks value</th>

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
            <td>{{ $user['total_opening_stocks'] ?? ""}}</td>
            <td>{{ $user['total_received_stock'] ?? ""}}</td>
            <td>{{ $user['total_purchase_returned_stock'] ?? ""}}</td>
            <td>{{ $user['total_sales_stock'] ?? ""}}</td>
            <td>{{ $user['total_returned_stock'] ?? ""}}</td>
            <td>{{ $user['total_closing_stocks'] ?? ""}}</td>
        </tr>
        @endforeach
    </tbody>
</table>