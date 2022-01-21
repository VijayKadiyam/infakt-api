<table>
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Date</th>
            <th>Channel</th>
            <th>Chain Name</th>
            <th>Store Name</th>
            <th>BA Name</th>
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
            <td>{{ $user['user']['channel'] }}</td>
            <td>{{ $user['user']['chain_name'] }}</td>
            <td>{{ \Carbon\Carbon::parse($user['created_at'])->format('Y-m-d ')}}</td>
            <td>{{ $user['user']['name'] }}</td>
            <td>{{ $user['user']['ba_name'] }}</td>
            <td>{{ $user['name'] }}</td>
            <td>{{ $user['email'] }}</td>
            <td>{{ $user['product_brought'] }}</td>
            <td>{{ $user['sample_given'] }}</td>
        </tr>
        @endforeach

    </tbody>
</table>