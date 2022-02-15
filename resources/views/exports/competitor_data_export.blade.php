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
            <th colspan="6">Week 1</th>
            <th colspan="6">Week 2</th>
            <th colspan="6">Week 3</th>
            <th colspan="6">Week 4</th>
        </tr>
        <tr>
            <th>Bio Tech</th>
            <th>Derma Fique</th>
            <th>Nivea</th>
            <th>Neutrogena</th>
            <th>Olay</th>
            <th>Plum</th>

            <th>Bio Tech</th>
            <th>Derma Fique</th>
            <th>Nivea</th>
            <th>Neutrogena</th>
            <th>Olay</th>
            <th>Plum</th>

            <th>Bio Tech</th>
            <th>Derma Fique</th>
            <th>Nivea</th>
            <th>Neutrogena</th>
            <th>Olay</th>
            <th>Plum</th>

            <th>Bio Tech</th>
            <th>Derma Fique</th>
            <th>Nivea</th>
            <th>Neutrogena</th>
            <th>Olay</th>
            <th>Plum</th>
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
            <td>{{ $user['w1_Biotech'] ?? '0'}}</td>
            <td>{{ $user['w1_Derma_Fique'] ?? '0'}}</td>
            <td>{{ $user['w1_Nivea'] ?? '0'}}</td>
            <td>{{ $user['w1_Neutrogena'] ?? '0'}}</td>
            <td>{{ $user['w1_Olay'] ?? '0'}}</td>
            <td>{{ $user['w1_Plum'] ?? '0'}}</td>
            <td>{{ $user['w2_Biotech'] ?? '0'}}</td>
            <td>{{ $user['w2_Derma_Fique'] ?? '0'}}</td>
            <td>{{ $user['w2_Nivea'] ?? '0'}}</td>
            <td>{{ $user['w2_Neutrogena'] ?? '0'}}</td>
            <td>{{ $user['w2_Olay'] ?? '0'}}</td>
            <td>{{ $user['w2_Plum'] ?? '0'}}</td>
            <td>{{ $user['w3_Biotech'] ?? '0'}}</td>
            <td>{{ $user['w3_Derma_Fique'] ?? '0'}}</td>
            <td>{{ $user['w3_Nivea'] ?? '0'}}</td>
            <td>{{ $user['w3_Neutrogena'] ?? '0'}}</td>
            <td>{{ $user['w3_Olay'] ?? '0'}}</td>
            <td>{{ $user['w3_Plum'] ?? '0'}}</td>
            <td>{{ $user['w4_Biotech'] ?? '0'}}</td>
            <td>{{ $user['w4_Derma_Fique'] ?? '0'}}</td>
            <td>{{ $user['w4_Nivea'] ?? '0'}}</td>
            <td>{{ $user['w4_Neutrogena'] ?? '0'}}</td>
            <td>{{ $user['w4_Olay'] ?? '0'}}</td>
            <td>{{ $user['w4_Plum'] ?? '0'}}</td>

        </tr>
        @endforeach

    </tbody>
</table>