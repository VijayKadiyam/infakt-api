<table>
  <thead>
    <tr>
      <th>Sr. No.</th>
      <th>Category</th>
      <th>Sub Category</th>
      <th>SKU Name</th>
      <th>Price</th>
      <th>HSN Code</th>
      @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
      <th colspan="6">{{ $userDailyOrderSummary['user']['name'] }}</th>
      @endforeach
    </tr>
    @foreach ($skus[0]->userDailyOrderSummaries as $userDailyOrderSummary)
    <th>OPENING</th>
    <th>RECEIVED</th>
    <th>RETURNED</th>
    <th>OFFTAKE</th>
    <th>OFFTAKE RETURN</th>
    <th>CLOSING</th>
    @endforeach
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