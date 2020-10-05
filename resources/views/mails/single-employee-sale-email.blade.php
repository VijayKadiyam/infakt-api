<!DOCTYPE html><html>
<?php $total = 0; ?>
<head>
  <style type="text/css">
    table, tr, td {
      border: 1px solid black;
      border-collapse: collapse;
    }
    td {
      padding: 5px;
    }

    .yellow {
      background-color: lightyellow;
    }

    .cyan {
      background-color: lightcyan;
    }
  </style>
  <title></title>
</head>
<body>
  <div align="center"><u><h2>{{ $user->name }} | {{ $date }} | Sales Report</h2></u></div>

  <div align="center">
    <table>
      <tr>
        <td class="yellow">Sr. No.</td>
        <td class="yellow">Salesman Name</td>
        <td class="yellow">Empl ID</td>
        <td class="yellow">Beat</td>
        <td class="yellow">Town</td>
        <td class="yellow">Region</td>
        <td class="yellow">Branch</td>
        <td class="yellow">Outlet Name</td>
        <td class="yellow">UID</td>
        <td class="yellow">Category</td>
        <td class="yellow">Class</td>
        <td class="yellow">SKU</td>
        <td class="yellow">QTY</td>
        <td class="yellow">Value</td>
      </tr>
      @foreach($sales as $sale)
        <?php $total = $total + $sale->value; ?>
        <tr>
          <td>{{ $loop->index + 1 }}</td>
          <td>{{ $user->name }}</td>
          <td>{{ $user->employee_code }}</td>
          <td>{{ $sale->retailer ? $sale->retailer->reference_plan->name : '' }}</td>
          <td>{{ $sale->retailer ? $sale->retailer->address : '' }}</td>
          <td>Maharashtra</td>
          <td>Mumbai</td>
          <td>{{ $sale->retailer ? $sale->retailer->name : '' }}</td>
          <td>{{ $sale->retailer ? $sale->retailer->retailer_code : '$sale->retailer' }}</td>
          <td>{{ $sale->retailer ? $sale->retailer->retailer_category->name : '' }}</td>
          <td>{{ $sale->retailer ? $sale->retailer->retailer_classification->name : '' }}</td>
          <td>{{ $sale->sku->name }}</td>
          <td>{{ $sale->qty }}</td>
          <td>&#x20B9;{{ $sale->value }}</td>
        </tr>
      @endforeach
      <tr class="yellow">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>Total:</td>
        <td>&#x20B9;{{ $total }}</td>
      </tr>
    </table>  
  </div>
</body>
</html>