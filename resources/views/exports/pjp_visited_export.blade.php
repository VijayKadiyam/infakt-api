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
            <th>Date</th>
            <th>Location</th>
            <th>Remarks</th>
            <th>Markets</th>
            <th>Mardaskets</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pjps as $pjp)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['brand'])) }}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['region'])) }}</td>
            <td>{{ $pjp['pjp_supervisor']['user']['channel'] }}</td>
            <td>{{ $pjp['pjp_supervisor']['user']['chain_name'] }}</td>
            <td>{{ $pjp['pjp_supervisor']['user']['city'] }}</td>
            <td>{{ $pjp['pjp_supervisor']['user']['state'] }}</td>
            <td>{{ $pjp['pjp_supervisor']['user']['employee_code'] }}</td>
            <td>{{ $pjp['pjp_supervisor']['user']['name'] }}</td>
            <td>{{ $pjp['pjp_supervisor']['user']['ba_name'] }}</td>
            <td>{{ $pjp['pjp_supervisor']['user']['pms_emp_id'] }}</td>
            <td>{{ $pjp['pjp_supervisor']['user']['supervisor_name'] }}</td>
            <td>{{ $pjp['pjp_supervisor']['date'] }}</td>
            <td>{{ $pjp['location'] }}</td>
            <td>{{ $pjp['remarks'] }}</td>
            <td>
                <table>
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Market Name</th>
                            <th>Visited Date</th>
                            <th>Status</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ( $pjp['pjp_markets'] as $pjp_market )
                        <td>{{ $loop->index + 1 }}</td>
                        <td>{{$pjp_market['market_name']}}</td>
                        @if ($pjp_market->pjp_visited_supervisor!="nhi mila" )
                        <td>{{$pjp_market->pjp_visited_supervisor->created_at}}</td>
                        <td>{{$pjp_market->pjp_visited_supervisor->is_visited?"YES":"NO"}}</td>
                        <td>{{$pjp_market->pjp_visited_supervisor->gps_address}}</td>
                        @else
                        <td></td>
                        <td></td>
                        <td></td>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>