<?php

namespace App\Http\Controllers;

use App\CompetitorData;
use App\CrudeCompetitorData;
use App\Imports\CompetitorDataImport;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class CrudeCompetitorDatasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company'])
            ->except(['index']);
    }

    public function index()
    {
        return response()->json([
            'data'  =>  CrudeCompetitorData::all()
        ]);
    }

    public function uploadCompetitorData(Request $request)
    {
        set_time_limit(0);
        // return $request->company->id;
        if ($request->hasFile('competitorData')) {
            $file = $request->file('competitorData');

            Excel::import(new CompetitorDataImport, $file);
            return response()->json([
                'data'    =>  CrudeCompetitorData::all(),
                'success' =>  true
            ]);
        }
    }

    public function processCompetitorData(User $user)
    {
        set_time_limit(0);

        $crude_competitor_data = CrudeCompetitorData::all();
        $category = [
            'bio_tech',
            'derma_fique',
            'nivea',
            'neutrogena',
            'olay',
            'plum',
            'wow',
        ];
        $competitor_data = [];
        foreach ($crude_competitor_data as $column =>  $competitor) {
            if ($competitor->store_code) {
                $us = User::where('employee_code', '=', $competitor->store_code)
                    ->first();
                if ($us) {

                    $user_id = $us['id'];

                    $data = [
                        'company_id' => request()->company->id,
                        'user_id' => $user_id,
                        'month' => $competitor->month,
                        'year' => $competitor->year,
                    ];

                    foreach ($category as $key => $cat) {
                        if ($competitor->$cat) {
                            $category_ompetitor = $competitor[$cat];
                            $data['amount'] = $category_ompetitor;
                            $check = str_replace("_", " ", $cat);
                            $categ = ucwords($check);
                            $data['competitor'] = $categ;
                            $User_competitor_data = CompetitorData::where('user_id', '=', $user_id)
                                ->where('month', '=', $competitor->month)
                                ->where('year', '=', $competitor->year)
                                ->where('competitor', '=', $categ)->first();
                            if ($User_competitor_data) {
                                // Update CompetitorData
                                $competitorData = CompetitorData::where('id', '=', $User_competitor_data->id);
                                $competitorData->update($data);
                            } else {
                                // Insert CompetitorData
                                $competitorData = new CompetitorData($data);
                                $competitorData->save();
                            }
                        }
                    }
                }
            }
        }
    }

    public function truncate()
    {
        CrudeCompetitorData::truncate();
    }
}
