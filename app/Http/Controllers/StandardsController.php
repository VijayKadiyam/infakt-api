<?php

namespace App\Http\Controllers;

use App\Classcode;
use App\Section;
use App\Standard;
use Illuminate\Http\Request;

class StandardsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $standards = [];
        if (request()->company) {
            $standards = request()->company->standards()
                ->where('is_deleted', false);
            if (request()->standard_id) {
                $standards = $standards->where('id', request()->standard_id);
            }
            $standards = $standards->get();
        }


        return response()->json([
            'data'  =>  $standards,
            'count' =>   sizeof($standards),
            'success' =>  true,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $request->validate([
            'name'  =>  'required',
        ]);

        if ($request->id == null || $request->id == '') {
            // Save Standard
            $standard = new Standard(request()->all());
            $request->company->standards()->save($standard);
            // Save Section
            if (isset($request->sections))
                foreach ($request->sections as $section) {
                    // dd($section['classcodes']);
                    $store_section = new Section($section);
                    $standard->sections()->save($store_section);

                    // Save Classcodes
                    $class_codes = $section['classcodes'];
                    // dd($class_codes);
                    if (isset($class_codes))
                        foreach ($class_codes as $classcode) {
                            $class_code = new Classcode($classcode);
                            $store_section->classcodes()->save($class_code);
                        }
                    // ---------------------------------------------------

                }
            // ---------------------------------------------------
        } else {
            // Update Content
            $standard = Standard::find($request->id);
            $standard->update($request->all());

            // Check if Section deleted
            if (isset($request->sections))
                $sectionIdResponseArray = array_pluck($request->sections, 'id');
            else
                $sectionIdResponseArray = [];
            $standardId = $standard->id;
            $sectionIdArray = array_pluck(Section::where('standard_id', '=', $standardId)->get(), 'id');
            $differenceSectionIds = array_diff($sectionIdArray, $sectionIdResponseArray);
            // Delete which is there in the database but not in the response
            if ($differenceSectionIds)
                foreach ($differenceSectionIds as $differenceSectionId) {
                    $section = Section::find($differenceSectionId);
                    $section->delete();
                }

            // Update Section

            if (isset($request->sections))

                foreach ($request->sections as $section) {
                    if (!isset($section['id'])) {
                        $section = new Section($section);
                        $standard->sections()->save($section);
                    } else {
                        $section = Section::find($section['id']);
                        $section->update($section);
                    }

                    // Check if Classcode deleted
                    $classcodes = $section['classcodes'];
                    if (isset($classcodes))
                        $classcodeIdResponseArray = array_pluck($classcodes, 'id');
                    else
                        $classcodeIdResponseArray = [];
                    $sectionId = $section->id;
                    $classcodeIdArray = array_pluck(Classcode::where('section_id', '=', $sectionId)->get(), 'id');
                    $differenceClasscodeIds = array_diff($classcodeIdArray, $classcodeIdResponseArray);
                    // Delete which is there in the database but not in the response
                    if ($differenceClasscodeIds)
                        foreach ($differenceClasscodeIds as $differenceClasscodeId) {
                            $classcode = Classcode::find($differenceClasscodeId);
                            $classcode->delete();
                        }
                    // Update Classcode
                    if (isset($classcodes))
                        foreach ($classcodes as $class_code) {
                            if (!isset($class_code['id'])) {
                                $classcode = new Classcode($class_code);
                                $section->classcodes()->save($classcode);
                            } else {
                                $classcode = Classcode::find($class_code['id']);
                                $classcode->update($classcode);
                            }
                        }
                }

            // ---------------------------------------------------

        }

        $standard->sections = $standard->sections;

        return response()->json([
            'data'  =>  $standard
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Standard $standard)
    {
        return response()->json([
            'data'  =>  $standard
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Standard $standard)
    {
        $request->validate([
            'name'  =>  'required',
        ]);

        $standard->update($request->all());

        return response()->json([
            'data'  =>  $standard
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $standard = request()->company->standards()
            ->where('id', $id)->first();
        $standard->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
