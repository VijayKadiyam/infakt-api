<table>
  <thead>
    <tr>
      <th>Sr. No.</th>
      <th>Category</th>
      <th>Sub Category</th>
      <th>SKU Name</th>
      <th>Price</th>
      <th>HSN Code</th>
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
    </tr>
    @endforeach
  </tbody>
</table>