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
            <th>Market Name</th>
            <th>Visited Date</th>
            <th>Status</th>
            <th>Location</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pjps as $pjp)
        @foreach ( $pjp['pjp_markets'] as $pjp_market )
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['brand'])) }}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['region'])) }}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['channel'] ))}}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['chain_name'] ))}}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['city'] ))}}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['state'] ))}}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['employee_code'] ))}}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['name'] ))}}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['ba_name'] ))}}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['pms_emp_id'] ))}}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['user']['supervisor_name'] ))}}</td>
            <td>{{ ucwords(strtolower($pjp['pjp_supervisor']['date'] ))}}</td>
            <td>{{ ucwords(strtolower($pjp['location'] ))}}</td>
            
            <td>{{$pjp_market['market_name']}}</td>
            @if ($pjp_market->pjp_visited_supervisor!="nhi mila" )
            <td>{{ucwords(strtolower($pjp_market->pjp_visited_supervisor->created_at))}}</td>
            <td>{{$pjp_market->pjp_visited_supervisor->is_visited?"Yes":"No"}}</td>
            <td>{{ucwords(strtolower($pjp_market->pjp_visited_supervisor->gps_address))}}</td>
            @else
            <td></td>
            <td></td>
            <td></td>
            @endif
            <td>{{ ucwords(strtolower($pjp['remarks'])) }}</td>
            
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>