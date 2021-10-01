<?php

namespace App\Http\Controllers;

use App\Visitor;
use App\VisitorBa;
use App\VisitorStock;
use App\VisitorNpd;
use App\VisitorTester;
use Illuminate\Http\Request;

class VisitorsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    public function index()
    {
        $count = 0;
        if (request()->page && request()->rowsPerPage) {
            $visitors = request()->company->visitors();
            $count = $visitors->count();
            $visitors = $visitors->paginate(request()->rowsPerPage)->toArray();
            $visitors = $visitors['data'];
        } else {
            $visitors = request()->company->visitors;
            $count = $visitors->count();
        }

        return response()->json([
            'data'     =>  $visitors,
            'count'    =>   $count,
            'success'   =>  true
        ], 200);
    }

    /*
   * To store a new visitor
   *
   *@
   */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'  =>  'required',
        ]);
        
        // return request()->visitor_bas;
        if ($request->id == null || $request->id == '') {
            // Save Visitor
            $visitor = new Visitor(request()->all());
            $request->company->visitors()->save($visitor);
            // dd($visitor);

            // Save Visitor Bas
            // dd($request->all());
            if (isset($request->visitor_bas))
                foreach ($request->visitor_bas as $ba) {
                    $bas = new VisitorBa($ba);
                    $visitor->visitor_bas()->save($bas);
                }
            // ---------------------------------------------------
            // Save Visitor Npds
            if (isset($request->visitor_npds))
                foreach ($request->visitor_npds as $npd) {
                    $npds = new VisitorNpd($npd);
                    $visitor->visitor_npds()->save($npds);
                }
            // ---------------------------------------------------
            // Save Visitor Stocks
            if (isset($request->visitor_stocks))
                foreach ($request->visitor_stocks as $stock) {
                    $stocks = new VisitorStock($stock);
                    $visitor->visitor_stocks()->save($stocks);
                }
            // ---------------------------------------------------
            // Save Visitor Testers
            if (isset($request->visitor_testers))
                foreach ($request->visitor_testers as $tester) {
                    $testers = new VisitorTester($tester);
                    $visitor->visitor_testers()->save($testers);
                }
            // ---------------------------------------------------
        } else {
            // Update Visitor
            $visitor = Visitor::find($request->id);
            $visitor->update($request->all());
            
            // Check if Visitor Bas deleted
            if (isset($request->visitor_bas)) {
                $visitorBaIdResponseArray = array_pluck($request->visitor_bas, 'id');
            } else
                $visitorBaIdResponseArray = [];
            $visitorId = $visitor->id;
            $visitorBaIdArray = array_pluck(VisitorBa::where('visitor_id', '=', $visitorId)->get(), 'id');
            $differenceVisitorBaIds = array_diff($visitorBaIdArray, $visitorBaIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceVisitorBaIds)
                foreach ($differenceVisitorBaIds as $differenceVisitorBaId) {
                    $visitorBa = VisitorBa::find($differenceVisitorBaId);
                    $visitorBa->delete();
                }
                // dd(request()->visitor_bas);

            // Update Visitor Ba
            if (isset($request->visitor_bas))
                foreach ($request->visitor_bas as $ba) {
                    if (!isset($ba['id'])) {
                        $visitor_ba = new VisitorBa($ba);
                        $visitor->visitor_bas()->save($visitor_ba);
                    } else {
                        $visitor_ba = VisitorBa::find($ba['id']);
                        $visitor_ba->update($ba);
                    }
                }

            // ---------------------------------------------------
            // Check if Visitor Npds deleted

            if (isset($request->visitor_npds)) {
                $visitorNpdIdResponseArray = array_pluck($request->visitor_npds, 'id');
            } else
                $visitorNpdIdResponseArray = [];
            $visitorId = $visitor->id;
            $visitorNpdIdArray = array_pluck(VisitorNpd::where('visitor_id', '=', $visitorId)->get(), 'id');
            $differenceVisitorNpdIds = array_diff($visitorNpdIdArray, $visitorNpdIdResponseArray);
            // Delete which is there in the datanpdse but not in the response
            if ($differenceVisitorNpdIds)
                foreach ($differenceVisitorNpdIds as $differenceVisitorNpdId) {
                    $visitorNpd = VisitorNpd::find($differenceVisitorNpdId);
                    $visitorNpd->delete();
                }

            // Update Visitor Npd
            if (isset($request->visitor_npds))
                foreach ($request->visitor_npds as $npd) {
                    if (!isset($npd['id'])) {
                        $visitor_npd = new VisitorNpd($npd);
                        $visitor->visitor_npds()->save($visitor_npd);
                    } else {
                        $visitor_npd = VisitorNpd::find($npd['id']);
                        $visitor_npd->update($npd);
                    }
                }

            // ---------------------------------------------------
            // Check if Visitor Stocks deleted
            if (isset($request->visitor_stocks)) {
                $visitorStockIdResponseArray = array_pluck($request->visitor_stocks, 'id');
            } else
                $visitorStockIdResponseArray = [];
            $visitorId = $visitor->id;
            $visitorStockIdArray = array_pluck(VisitorStock::where('visitor_id', '=', $visitorId)->get(), 'id');
            $differenceVisitorStockIds = array_diff($visitorStockIdArray, $visitorStockIdResponseArray);
            // Delete which is there in the datastockse but not in the response
            if ($differenceVisitorStockIds)
                foreach ($differenceVisitorStockIds as $differenceVisitorStockId) {
                    $visitorStock = VisitorStock::find($differenceVisitorStockId);
                    $visitorStock->delete();
                }

            // Update Visitor Stock
            if (isset($request->visitor_stocks))
                foreach ($request->visitor_stocks as $stock) {
                    if (!isset($stock['id'])) {
                        $visitor_stock = new VisitorStock($stock);
                        $visitor->visitor_stocks()->save($visitor_stock);
                    } else {
                        $visitor_stock = VisitorStock::find($stock['id']);
                        $visitor_stock->update($stock);
                    }
                }

            // ---------------------------------------------------
            // Check if Visitor Tester deleted
            if (isset($request->visitor_testers)) {
                $visitorTesterIdResponseArray = array_pluck($request->visitor_testers, 'id');
            } else
                $visitorTesterIdResponseArray = [];
            $visitorId = $visitor->id;
            $visitorTesterIdArray = array_pluck(VisitorTester::where('visitor_id', '=', $visitorId)->get(), 'id');
            $differenceVisitorTesterIds = array_diff($visitorTesterIdArray, $visitorTesterIdResponseArray);
            // Delete which is there in the datatesterse but not in the response
            if ($differenceVisitorTesterIds)
                foreach ($differenceVisitorTesterIds as $differenceVisitorTesterId) {
                    $visitorTester = VisitorTester::find($differenceVisitorTesterId);
                    $visitorTester->delete();
                }

            // Update Visitor Tester
            if (isset($request->visitor_testers))
                foreach ($request->visitor_testers as $tester) {
                    if (!isset($tester['id'])) {
                        $visitor_tester = new VisitorTester($tester);
                        $visitor->visitor_testers()->save($visitor_tester);
                    } else {
                        $visitor_tester = VisitorTester::find($tester['id']);
                        $visitor_tester->update($tester);
                    }
                }

            // ---------------------------------------------------

        }

        $visitor->visitor_bas = $visitor->visitor_bas;
        $visitor->visitor_npds = $visitor->visitor_npds;
        $visitor->visitor_stocks = $visitor->visitor_stocks;
        $visitor->visitor_testers = $visitor->visitor_testers;
        return response()->json([
            'data'    =>  $visitor,
            // 'success'   =>  true
        ], 201);
    }

    /*
   * To view a single channel$visitor
   *
   *@
   */
    public function show(Visitor $visitor)
    {
        $visitor->visitor_bas = $visitor->visitor_bas;
        $visitor->visitor_npds = $visitor->visitor_npds;
        $visitor->visitor_stocks = $visitor->visitor_stocks;
        $visitor->visitor_testers = $visitor->visitor_testers;

        return response()->json([
            'data'   =>  $visitor,
            'success' =>  true
        ], 200);
    }

    /*
   * To update a channel$visitor
   *
   *@
   */
    public function update(Request $request, Visitor $visitor)
    {
        dd($request->all());
        $visitor->update($request->all());

        return response()->json([
            'data'  =>  $visitor,
            'success'   =>  true
        ], 200);
    }

    public function destroy($id)
    {
        $visitor = Visitor::find($id);
        $visitor->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
