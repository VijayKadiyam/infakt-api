<table>
  <thead>
    <tr>
      <th rowspan="12">Sr. No.</th>
      <th rowspan="12">Category</th>
      <th rowspan="12">Sub Category</th>
      <th rowspan="12">SKU Name</th>
      <th rowspan="12">Price</th>
      <th>Region</th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th colspan="6">{{ $userDailyOrderSummary['user']['region'] }}</th>
      @endforeach
    </tr>
    <tr>
      <th>Brand</th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th colspan="6">{{ $userDailyOrderSummary['user']['brand'] }}</th>
      @endforeach
    </tr>
    <tr>
      <th>Channel</th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th colspan="6">{{ $userDailyOrderSummary['user']['channel'] }}</th>
      @endforeach
    </tr>
    <tr>
      <th>Chain Name</th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th colspan="6">{{ $userDailyOrderSummary['user']['chain_name'] }}</th>
      @endforeach
    </tr>
    <tr>
      <th>City</th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th colspan="6">{{ $userDailyOrderSummary['user']['city'] }}</th>
      @endforeach
    </tr>
    <tr>
      <th>State</th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th colspan="6">{{ $userDailyOrderSummary['user']['state'] }}</th>
      @endforeach
    </tr>
    <tr>
      <th>Store Code</th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th colspan="6">{{ $userDailyOrderSummary['user']['employee_code'] }}</th>
      @endforeach
    </tr>
    <tr>
      <th>Store Name</th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th colspan="6">{{ $userDailyOrderSummary['user']['name'] }}</th>
      @endforeach
    </tr>
    <tr>
      <th>BA Name</th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th colspan="6">{{ $userDailyOrderSummary['user']['ba_name'] }}</th>
      @endforeach
    </tr>
    <tr>
      <th>PMS Emp ID</th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th colspan="6">{{ $userDailyOrderSummary['user']['pms_emp_id'] }}</th>
      @endforeach
    </tr>
    <tr>
      <th>Supervisor Name</th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th colspan="6">{{ $userDailyOrderSummary['user']['supervisor_name'] }}</th>
      @endforeach
    </tr>
    <tr>
      <th></th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th>OPENING</th>
      <th>RECEIVED</th>
      <th>RETURNED</th>
      <th>OFFTAKE</th>
      <th>OFFTAKE RETURN</th>
      <th>CLOSING</th>
      @endforeach
    </tr>
  </thead>
  <tbody>
    @foreach ($skus as $sku)
    <tr>
      <td>{{ $loop->index + 1 }}</td>
      <td>{{ $sku->main_category }}</td>
      <td>{{ $sku->category }}</td>
      <td>{{ $sku->name }}</td>
      <td>{{ $sku->price }}</td>
      <td>{{ $sku->hsn_code }}</td>
      @foreach ($sku->userDailyOrderSummaries as $userDailyOrderSummary)
      <td>{{ $userDailyOrderSummary['opening_stock'] }}</td>
      <td>{{ $userDailyOrderSummary['received_stock'] }}</td>
      <td>{{ $userDailyOrderSummary['purchase_returned_stock'] }}</td>
      <td>{{ $userDailyOrderSummary['sales_stock'] }}</td>
      <td>{{ $userDailyOrderSummary['returned_stock'] }}</td>
      <td>{{ $userDailyOrderSummary['closing_stock'] }}</td>
      @endforeach
    </tr>
    @endforeach
  </tbody>
</table>