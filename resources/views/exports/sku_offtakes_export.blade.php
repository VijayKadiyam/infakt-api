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
    </tr>
    </thead>
    <tbody>
        @foreach ($productsOfftakes as $offtake)
        <tr> 
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ ucwords(strtolower($offtake['user']['brand'])) }}</td>
            <td>{{ ucwords(strtolower($offtake['user']['region'])) }}</td>
            <td>{{ $offtake['user']['channel'] }}</td>
            <td>{{ $offtake['user']['chain_name'] }}</td>
            <td>{{ $offtake['user']['city'] }}</td>
            <td>{{ $offtake['user']['state'] }}</td>
            <td>{{ $offtake['user']['employee_code'] }}</td>
            <td>{{ $offtake['user']['name'] }}</td>
            <td>{{ $offtake['user']['ba_name'] }}</td>
            <td>{{ $offtake['user']['pms_emp_id'] }}</td>
            <td>{{ $offtake['user']['supervisor_name'] }}</td>
            @for ($i = 1; $i <= $daysInMonth; $i++)
                <td>{{ $offtake["date$i"] }}</td>       
            @endfor
        </tr>    
        @endforeach
    </tbody>
</table>