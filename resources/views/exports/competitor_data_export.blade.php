<table>
    <thead>
        <tr>
            <th rowspan="2">Sr. No.</th>
            <th rowspan="2">Brand</th>
            <th rowspan="2">Region</th>
            <th rowspan="2">Channel</th>
            <th rowspan="2">Chain Name</th>
            <th rowspan="2">City</th>
            <th rowspan="2">State</th>
            <th rowspan="2">Store Code</th>
            <th rowspan="2">Store Name</th>
            <th rowspan="2">BA Name</th>
            <th rowspan="2">PMS EMP ID</th>
            <th rowspan="2">Supervisor Name</th>
            @foreach($months as $month)
            <th colspan="7">{{ date("F", mktime(0, 0, 0, $month, 1)) }} 2022</th>
            @endforeach
            <!-- <th colspan="6">Week 2</th>
            <th colspan="6">Week 3</th>
            <th colspan="6">Week 4</th> -->
        </tr>
        <tr>
            @foreach($months as $month)
            <th>Bio Tech</th>
            <th>Derma Fique</th>
            <th>Nivea</th>
            <th>Neutrogena</th>
            <th>Olay</th>
            <th>Plum</th>
            <th>Wow</th>
            @endforeach

        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
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
            @foreach($months as $month)
            <td>{{ $user['m' . $month . '_Bio_Tech'] ?? '0'}}</td>
            <td>{{ $user['m' . $month . '_Derma_Fique'] ?? '0'}}</td>
            <td>{{ $user['m' . $month . '_Nivea'] ?? '0'}}</td>
            <td>{{ $user['m' . $month . '_Neutrogena'] ?? '0'}}</td>
            <td>{{ $user['m' . $month . '_Olay'] ?? '0'}}</td>
            <td>{{ $user['m' . $month . '_Plum'] ?? '0'}}</td>
            <td>{{ $user['m' . $month . '_Wow'] ?? '0'}}</td>
            @endforeach
        </tr>
        @endforeach

    </tbody>
</table>