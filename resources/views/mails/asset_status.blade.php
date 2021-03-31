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
    <tr style="background-color: yellow;" >
      <th>Asset Name</th>
      <th>Status</th>
      <th>Description</th>
    </tr>
    <tr>
      <td>{{ $asset->asset_name }}</td>
      <td>{{ $asset->status }}</td>
      <td>{{ $asset->description }}</td>
    </tr>
  </table>
  <br><br>
  <table width="100%">
    <caption>Status</caption>
    <tr style="background-color: yellow;">
      <th>Date</th>
      <th>Status</th>
      <th>Description</th>
    </tr>
    @foreach($asset->asset_statuses as $status )
    <tr>
      <td>{{ $status->date }}</td>
      <td>{{ $status->status }}</td>
      <td>{{ $status->description }}</td>
    </tr>
    @endforeach
  </table>
  <br><br><br>
  Regards,
  <br>
  PMS
</body>
