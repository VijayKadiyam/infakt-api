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
  <br><br>
  Greetings for the day.
  <br><br>
  Please refer the asset status.
  <br><br>
  <table width="100%">
    <tr style="background-color: yellow;">
      <td>Date</td>
      <td>Asset Name</td>
      <td>Unique ID</td>
      <td>Store Name</td>
      <td>Store Address</td>
      <td>Contact Person</td>
      <td>Mobile No</td>
      <td>Status</td>
      <td>Description</td>
    </tr>
    <tr>
      <td>{{ \Carbon\Carbon::parse($asset->created_at)->format('d-m-Y') }}</td>
      <td>{{ $asset->asset_name }}</td>
      <td>{{ $asset->unique_id }}</td>
      <td>{{ $asset->retailer->name }}</td>
      <td>{{ $asset->retailer->address }}</td>
      <td>{{ $asset->retailer->proprietor_name }}</td>
      <td>{{ $asset->retailer->phone }}</td>
      <td>{{ $asset->status }}</td>
      <td>{{ $asset->description }}</td>
    </tr>
  </table>
  <br><br><br>
  Regards,
  <br>
  PMS
</body>
