<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\ShelfAnalysis;

class ShelfAnalysisController extends Controller
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

    public function masters(Request $request)
    {
        $referencePlansController = new ReferencePlansController();
        $referencePlansResponse = $referencePlansController->index($request);

        return response()->json([
            'reference_plans'  =>  $referencePlansResponse->getData()->data,
        ], 200);
    }

    public function index()
    {
        $shelfAnalyses = request()->company->shelf_analysis;
        // dd('testing'. $shelfAnalysis);
        return response()->json([
            'data'     => $shelfAnalyses,
            'success'  => true
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'description'    =>  'required',
        ]);
        $imagePath1 = "";
        $imagePath2 = "";
        $imagePath3 = "";
        $imagePath4 = "";
        if ($request->hasFile('imagepath1')) {
            $file = $request->file('imagepath1');
            $name = time() . $request->filename . 'image1.';
            $name = $name . $file->getClientOriginalExtension();
            $imagePath1 = 'shelfAnalysis/' . $name;
            Storage::disk('local')->put($imagePath1, file_get_contents($file), 'public');
        }
        if ($request->hasFile('imagepath2')) {
            $file = $request->file('imagepath2');
            $name = time() . $request->filename . 'image2.';
            $name = $name . $file->getClientOriginalExtension();
            $imagePath2 = 'shelfAnalysis/' . $name;
            Storage::disk('local')->put($imagePath2, file_get_contents($file), 'public');
        }
        if ($request->hasFile('imagepath3')) {
            $file = $request->file('imagepath3');
            $name = time() . $request->filename . 'image3.';
            $name = $name . $file->getClientOriginalExtension();
            $imagePath3 = 'shelfAnalysis/' . $name;
            Storage::disk('local')->put($imagePath3, file_get_contents($file), 'public');
        }
        if ($request->hasFile('imagepath4')) {
            $file = $request->file('imagepath4');
            $name = time() . $request->filename . 'image4.';
            $name = $name . $file->getClientOriginalExtension();
            $imagePath4 = 'shelfAnalysis/' . $name;
            Storage::disk('local')->put($imagePath4, file_get_contents($file), 'public');
        }

        $shelfAnalysis = new ShelfAnalysis($request->all());
        $shelfAnalysis->image_path_1 = $imagePath1;
        $shelfAnalysis->image_path_2 = $imagePath2;
        $shelfAnalysis->image_path_3 = $imagePath3;
        $shelfAnalysis->image_path_4 = $imagePath4;
        $request->company->shelf_analysis()->save($shelfAnalysis);

        return response()->json([
            'data'    =>  $shelfAnalysis,
            'success' =>  true
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShelfAnalysis $shelfAnalysis)
    {
        return response()->json([
            'data'   =>  $shelfAnalysis
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShelfAnalysis $shelfAnalysis)
    {
        $request->validate([
            'description'  =>  'required',
        ]);
        
        $shelfAnalysis->update($request->all());

        return response()->json([
            'data'  =>  $shelfAnalysis,
            'success' =>  true
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShelfAnalysis $shelfAnalysis)
    {
        $shelfAnalysis->delete();
    }

    public function uploadShelfAnalysisImage(Request $request)
    {
        $shelfAnalyses = ShelfAnalysis::where('id', '=', request()->id)->get();
        $imagePath1 = $shelfAnalyses[0]->image_path_1;
        $imagePath2 = $shelfAnalyses[0]->image_path_2;
        $imagePath3 = $shelfAnalyses[0]->image_path_3;
        $imagePath4 = $shelfAnalyses[0]->image_path_4;

        if ($request->hasFile('imagepath1')) {
            $file = $request->file('imagepath1');
            $name = time() . $request->filename . 'image1.';
            $name = $name . $file->getClientOriginalExtension();
            $imagePath1 = 'shelfAnalysis/' . $name;
            Storage::disk('local')->put($imagePath1, file_get_contents($file), 'public');
        }
        if ($request->hasFile('imagepath2')) {
            $file = $request->file('imagepath2');
            $name = time() . $request->filename . 'image2.';
            $name = $name . $file->getClientOriginalExtension();
            $imagePath2 = 'shelfAnalysis/' . $name;
            Storage::disk('local')->put($imagePath2, file_get_contents($file), 'public');
        }
        if ($request->hasFile('imagepath3')) {
            $file = $request->file('imagepath3');
            $name = time() . $request->filename . 'image3.';
            $name = $name . $file->getClientOriginalExtension();
            $imagePath3 = 'shelfAnalysis/' . $name;
            Storage::disk('local')->put($imagePath3, file_get_contents($file), 'public');
        }
        if ($request->hasFile('imagepath4')) {
            $file = $request->file('imagepath4');
            $name = time() . $request->filename . 'image4.';
            $name = $name . $file->getClientOriginalExtension();
            $imagePath4 = 'shelfAnalysis/' . $name;
            Storage::disk('local')->put($imagePath4, file_get_contents($file), 'public');
        }
        $shelfAnalyses = ShelfAnalysis::where('id', '=', request()->id)->first();
        $shelfAnalyses->image_path_1 = $imagePath1;
        $shelfAnalyses->image_path_2 = $imagePath2;
        $shelfAnalyses->image_path_3 = $imagePath3;
        $shelfAnalyses->image_path_4 = $imagePath4;
        $shelfAnalyses->update();

        return response()->json([
            'data'  => [
              'image_path_1'  =>  $imagePath1,
              'image_path_2'  =>  $imagePath2,
              'image_path_3'  =>  $imagePath3,
              'image_path_3'  =>  $imagePath4,
            ],
            'success' =>  true
          ]);
    }
}
