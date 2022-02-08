<?php

namespace App\Http\Controllers;

use App\CrudeFocusedTarget;
use App\FocusedTarget;
use App\Imports\FocusedTargetImport;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CrudeFocusedTargetsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company'])
            ->except(['index']);
    }

    public function index()
    {
        return response()->json([
            'data'  =>  CrudeFocusedTarget::all()
        ]);
    }

    public function uploadFocusedTarget(Request $request)
    {
        set_time_limit(0);
        // return $request->company->id;
        if ($request->hasFile('focusedTargetData')) {
            $file = $request->file('focusedTargetData');

            Excel::import(new FocusedTargetImport, $file);
            return response()->json([
                'data'    =>  CrudeFocusedTarget::all(),
                'success' =>  true
            ]);
        }
    }

    public function processFocusedTarget(User $user)
    {
        set_time_limit(0);

        $crude_focused_targets = CrudeFocusedTarget::all();
        $category = [
            'baby', // category
            'baby_Kit',
            'baby_Oil',
            'bath_salt',
            'bathing',
            'body',
            'body_butter',
            'body_cream',
            'body_lotion',
            'body_scrub',
            'body_wash',
            'capsules',
            'cleanser',
            'cleansing',
            'combo_kit',
            'conditioner',
            'cream',
            'diaper',
            'dusting_powder',
            'face_cream',
            'face_free',
            'face_Mask',
            'face_Milk',
            'face_scrub',
            'face_Serum',
            'face_Spot',
            'face_toner',
            'facewash',
            'freebies',
            'gel',
            'gift_pack',
            'hair_care',
            'hair_Mask',
            'hair_Oil',
            'hair_Serum',
            'hand_cream',
            'hygine',
            'kajal',
            'kids_body',
            'lip_balm',
            'lotion',
            'mask',
            'moisturizer',
            'mosquito_protection',
            'oil',
            'oral',
            'otc',
            'peeling',
            'serum',
            'shampoo',
            'sheet_mask',
            'sub_cat',
            'sun_cat',
            'sun_care',
            'sunscreen',
            'tablets',
            'toner',
            'yogurt_for',
            'rice_range',
            'almond_range',
            'body_lotion_cold_cream',
        ];
        $focus_target = [];
        foreach ($crude_focused_targets as $column =>  $target) {
            if ($target->store_code) {
                $us = User::where('employee_code', '=', $target->store_code)
                    ->first();
                if ($us) {

                    $user_id = $us['id'];

                    $data = [
                        'company_id' => request()->company->id,
                        'user_id' => $user_id,
                        'month' => $target->month,
                        'year' => $target->year,
                    ];

                    foreach ($category as $key => $cat) {
                        if ($target->$cat) {
                            $category_target = $target[$cat];
                            $data['target'] = $category_target;
                            $data['category'] = $cat;
                            $User_target = FocusedTarget::where('user_id', '=', $user_id)
                                ->where('month', '=', $target->month)
                                ->where('year', '=', $target->year)
                                ->where('category', '=', $cat)->first();
                            if ($User_target) {
                                // Update FocusedTarget
                                $targetData = FocusedTarget::where('id', '=', $User_target->id);
                                $targetData->update($data);
                            } else {
                                // Insert FocusedTarget
                                $targetData = new FocusedTarget($data);
                                $targetData->save();
                            }
                        }
                    }
                }
            }
        }
    }

    public function truncate()
    {
        CrudeFocusedTarget::truncate();
    }
}
