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
        @foreach ($skus as $sku)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ $sku['user']['brand'] }}</td>
            <td>{{ $sku['user']['region'] }}</td>
            <td>{{ $sku['user']['channel'] }}</td>
            <td>{{ $sku['user']['chain_name'] }}</td>
            <td>{{ $sku['user']['city'] }}</td>
            <td>{{ $sku['user']['state'] }}</td>
            <td>{{ $sku['user']['employee_code'] }}</td>
            <td>{{ $sku['user']['name'] }}</td>
            <td>{{ $sku['user']['ba_name'] }}</td>
            <td>{{ $sku['user']['pms_emp_id'] }}</td>
            <td>{{ $sku['user']['supervisor_name'] }}</td>
            <td>{{ $sku['name'] ?? ""}}</td>
            <td>{{ $sku['price'] ?? ""}}</td>
            <td>{{ $sku['hsn_code'] ?? ""}}</td>
            <td>{{ $sku['opening_stock'] ?? ""}}</td>
            <td>{{ $sku['received_stock'] ?? ""}}</td>
            <td>{{ $sku['purchase_returned_stock'] ?? ""}}</td>
            <td>{{ $sku['sales_stock'] ?? ""}}</td>
            <td>{{ $sku['returned_stock'] ?? ""}}</td>
            <td>{{ $sku['closing_stock'] ?? ""}}</td>
        </tr>
        @endforeach
    </tbody>
</table>