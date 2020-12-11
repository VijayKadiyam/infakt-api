<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
  public function daySummary(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'date'    =>  'required',
    ]);

    $data = [
      'beat_names' => [
        'Beat 1', 'Beat 2',
      ],
      'total_outlets'  =>  15,
      'orders_not_taken'  =>  3,
      'orders_taken'      =>  12, 
      'total_order_value' =>  5000,
      'coverage'          =>  [
        'percent' =>  70,
        'value'   =>  12,
        'total'   =>  15
      ],
      'productivity'  =>  [
        'percent' =>  80,
        'value'   =>  10,
        'total'   =>  12
      ]
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function kpiReport(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    $data = [
      'target'    =>  2000,
      'achieved'  =>  1500,
      'percent'   =>  50,
      'days'      =>  [
        0 =>  [
          'date'      =>  '01-11-2020',
          'achieved'  =>  200,
        ],
        0 =>  [
          'date'      =>  '02-11-2020',
          'achieved'  =>  220,
        ]
      ]
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function targetVsAchieved(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    $data = [
      'target'    =>  2000,
      'achieved'  =>  1500,
      'percent'   =>  50,
      'outlets'      =>  [
        0 =>  [
          'outlet'    =>  'OUTLET 1',
          'achieved'  =>  200,
        ],
        0 =>  [
          'outlet'    =>  'OUTLET 2',
          'achieved'  =>  220,
        ]
      ]
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function salesTrend(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    $data = [
      'last_month'    =>  2000,
      'current_month' =>  3000,
      'outlets' =>  [
        0 =>  [
          'outlet'  =>  'OUTLET 1',
          'last_month'    =>  200,
          'current_month' =>  100,
        ],
        1 =>  [
          'outlet'  =>  'OUTLET 2',
          'last_month'    =>  201,
          'current_month' =>  101,
        ],
      ],
      'months'  =>  [
        [
          'month' =>  'January',
          'value' =>  1000,
        ],
        [
          'month' =>  'February',
          'value' =>  2000,
        ],
        [
          'month' =>  'March',
          'value' =>  3000,
        ],
        [
          'month' =>  'April',
          'value' =>  4000,
        ],
        [
          'month' =>  'May',
          'value' =>  5000,
        ],
      ]
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function billedOutlet(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    $data = [
      'outlets' =>  [
        0 =>  [
          'outlet'    =>  'OUTLET 1',
          'no_of_inv' =>  2,
          'value'     =>  100,
        ],
        1 =>  [
          'outlet'    =>  'OUTLET 2',
          'no_of_inv' =>  3,
          'value'   =>  101,
        ],
      ],
      'months'  =>  [
        [
          'month' =>  'January',
          'value' =>  1000,
        ],
        [
          'month' =>  'February',
          'value' =>  2000,
        ],
        [
          'month' =>  'March',
          'value' =>  3000,
        ],
        [
          'month' =>  'April',
          'value' =>  4000,
        ],
        [
          'month' =>  'May',
          'value' =>  5000,
        ],
      ]
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function invoiceDetail(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    $data = [
      'invoices' =>  [
        [
          'invoice_no'  =>  1,
          'invoice_date'=>  '01-11-2020',
          'value'       =>  100,
        ],
      ],
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }

  public function attendanceCalendar(Request $request)
  {
    $request->validate([
      'userId'  =>  'required',
      'month'   =>  'required',
    ]);

    $data = [
      'total_worked_hrs_day'  =>  '23:30:20 / 10',
      'avg_working_hrs'       =>  '02:33:22 Hrs',
      'present'               =>  '10 Days',
      'absent'                =>  '3 Days',
      'less_than_5_hrs'       =>  '2 Days',
      'greater_than_5_hrs'    =>  '3 Days',
      'attendances'  =>  [
        [
          'date'    =>  '2020-12-08',
          'status'  =>  'Present',
          'color'   =>  '#00FF00',
        ],
        [
          'date'    =>  '2020-12-09',
          'status'  =>  'Absent',
          'color'   =>  '#FF0000',
        ]
      ]
    ];

    return response()->json([
      'data'    =>  $data,
      'success' =>  true
    ]);
  }
}
