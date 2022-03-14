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
            <th>SKU Name</th>
            <th>Price/Unit</th>
            <th>HSN Code</th>
            <th>Opening Stocks</th>
            <th>Received Stocks</th>
            <th>Stock Return</th>
            <th>Offtake</th>
            <th>Offtake Return</th>
            <th>Closing Stock</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($dailyOrderSummaries as $sku)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ ucwords(strtolower($sku['user']['brand'])) }}</td>
            <td>{{ ucwords(strtolower($sku['user']['region'])) }}</td>
            <td>{{ $sku['user']['channel'] }}</td>
            <td>{{ $sku['user']['chain_name'] }}</td>
            <td>{{ $sku['user']['city'] }}</td>
            <td>{{ $sku['user']['state'] }}</td>
            <td>{{ $sku['user']['employee_code'] }}</td>
            <td>{{ $sku['user']['name'] }}</td>
            <td>{{ $sku['user']['ba_name'] }}</td>
            <td>{{ $sku['user']['pms_emp_id'] }}</td>
            <td>{{ $sku['user']['supervisor_name'] }}</td>
            <td>{{ $sku['sku']['name'] ?? ""}}</td>
            <td>{{ $sku['sku']['price'] ?? ""}}</td>
            <td>{{ $sku['sku']['hsn_code'] ?? ""}}</td>
            <td>{{ $sku['opening_stock'] ? abs($sku['opening_stock']) : 0 }}</td>
            <td>{{ $sku['received_stock'] ? abs($sku['received_stock']) :  0 }}</td>
            <td>{{ $sku['purchase_returned_stock'] ? abs($sku['purchase_returned_stock']) : 0 }}</td>
            <td>{{ $sku['sales_stock'] ? abs($sku['sales_stock']) : 0 }}</td>
            <td>{{ $sku['returned_stock'] ? abs($sku['returned_stock']) : 0 }}</td>
            <td>{{ $sku['closing_stock'] ? abs($sku['closing_stock']) : 0 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>