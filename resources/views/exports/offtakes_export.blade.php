<table>
    <thead>
    <tr>
        <th>Sr. No.</th>
        <th>Type</th>
        <th>Date</th>
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
        <th>SKU</th>
        <th>HSN Code</th>
        <th>Category</th>
        <th>Sub Category</th>
        <th>MRP</th>
        <th>QTY</th>
        <th>VALUE</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($finalOrders as $order)
            @foreach ($order['order_details'] as $detail)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>
                    Offtake
                </td>
                <td>{{ $order['created_at'] }}</td>
                <td>{{ $order['user']['brand'] }}</td>
                <td>{{ $order['user']['region'] }}</td>
                <td>{{ $order['user']['channel'] }}</td>
                <td>{{ $order['user']['chain_name'] }}</td>
                <td>{{ $order['user']['city'] }}</td>
                <td>{{ $order['user']['state'] }}</td>
                <td>{{ $order['user']['employee_code'] }}</td>
                <td>{{ $order['user']['name'] }}</td>
                <td>{{ $order['user']['ba_name'] }}</td>
                <td>{{ $order['user']['pms_emp_id']}}</td>
                <td>{{ $order['user']['supervisor_name'] }}</td>
                <td>{{ isset($detail['sku']) != null ? $detail['sku']['name'] : "" }}</td>
                <td>{{ $detail['sku']['hsn_code'] }}</td>
                <td>{{ $detail['sku']['main_category'] }}</td>
                <td>{{ $detail['sku']['category'] }}</td>
                <td>{{ $detail['sku']['price'] }}</td>
                <td>{{ $detail['qty'] }}</td>
                <td>{{ $detail['value'] }}</td>
            </tr>
            @endforeach            
        @endforeach
    </tbody>
</table>