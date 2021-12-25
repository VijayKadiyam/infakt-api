<table>
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>No Of Customer</th>
            <th>No Of Billed Customer</th>
            <th>More Than Two</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customers as $user)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ $user['no_of_customer'] }}</td>
            <td>{{ $user['no_of_billed_customer'] }}</td>
            <td>{{ $user['more_than_two'] }}</td>
        </tr>
        @endforeach

    </tbody>
</table>