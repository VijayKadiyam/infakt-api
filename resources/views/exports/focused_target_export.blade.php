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
            <th>Target</th>
            <th>Achieved</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($targets as $user)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ ucwords(strtolower($user['brand'])) }}</td>
            <td>{{ ucwords(strtolower($user['region'])) }}</td>
            <td>{{ $user['channel'] }}</td>
            <td>{{ $user['chain_name'] }}</td>
            <td>{{ $user['city'] }}</td>
            <td>{{ $user['state'] }}</td>
            <td>{{ $user['employee_code'] }}</td>
            <td>{{ $user['name'] }}</td>
            <td>{{ $user['ba_name'] }}</td>
            <td>{{ $user['pms_emp_id'] }}</td>
            <td>{{ $user['supervisor_name'] }}</td>
            <td>
                @foreach( $user['monthly_targets'] as $target)
                <p>
                    {{ $target->category}} : {{$target->target ?? "0"}}
                </p>
                @endforeach
            </td>
            <td>
                @foreach( $user['monthly_targets'] as $target)
                <p>
                    {{ $target->category}} : {{$target->achieved ?? "0"}}
                </p>
                @endforeach
            </td>
        </tr>
        @endforeach
    </tbody>
</table>