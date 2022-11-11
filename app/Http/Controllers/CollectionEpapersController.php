<?php

namespace App\Http\Controllers;

use App\CollectionEpaper;
use App\EpaperCollection;
use Illuminate\Http\Request;

class CollectionEpapersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api, company']);
    }

    public function index(Request $request)
    {
        $count = 0;
        if (request()->epaper_collection_id) {
            $collection_epapers = CollectionEpaper::with('epaper_collection', 'toi_article', 'et_article')->where('epaper_collection_id', request()->epaper_collection_id)->get();
            $epapers = [];
            foreach ($collection_epapers as $key => $collection_epaper) {
                if ($collection_epaper->toi_article_id != '' && $collection_epaper->toi_article_id != null) {
                    $epapers[] = $collection_epaper->toi_article;
                }
                if ($collection_epaper->et_article_id != '' && $collection_epaper->et_article_id != null) {
                    $epapers[] = $collection_epaper->et_article;
                }
                $collection_epapers['epapers'] = $epapers;
            }
            $count = $collection_epapers->count();
            // return $collection_epapers;
        } else {
            $collection_epapers = CollectionEpaper::all();
            $count = $collection_epapers->count();
        }



        return response()->json([
            'data'     =>  $collection_epapers,
            'count'    =>   $count
        ], 200);
    }

    /*
     * To store a new user_section
     *
     *@
     */

    public function store(Request $request)
    {
        $request->validate([
            'epaper_collection_id'        =>  'required',
        ]);

        $collection_epapers  = [];
        $msg = '';
        if (request()->toi_article_id) {
            $existing_collection_epaper = CollectionEpaper::where(['epaper_collection_id' => request()->epaper_collection_id, 'toi_article_id' => request()->toi_article_id])->first();
            if (!$existing_collection_epaper) {
                $collection_epapers = new CollectionEpaper(request()->all());
                $collection_epapers->save();
            } else {
                $msg = 'TOI Epaper already exist';
            }
        }
        if (request()->et_article_id) {
            $existing_collection_epaper = CollectionEpaper::where(['epaper_collection_id' => request()->epaper_collection_id, 'et_article_id' => request()->et_article_id])->first();
            if (!$existing_collection_epaper) {
                $collection_epapers = new CollectionEpaper(request()->all());
                $collection_epapers->save();
            } else {
                $msg = 'ET Epaper already exist';
            }
        }

        return response()->json([
            'data'    =>  $collection_epapers,
            'msg' => $msg
        ], 201);
    }

    public function toi_store(Request $request)
    {
        // return request()->all();
        $request->validate([
            'epaper_collection_id'        =>  'required',
        ]);
        $collection_epapers  = [];
        $msg = '';
        $existing_collection_epaper = CollectionEpaper::where(['epaper_collection_id' => request()->epaper_collection_id, 'toi_article_id' => request()->toi_article_id])->first();
        if (!$existing_collection_epaper) {
            $collection_epapers = new CollectionEpaper(request()->all());
            $collection_epapers->save();
        } else {
            $msg = 'TOI Epaper already exist';
        }


        return response()->json([
            'data'    =>  $collection_epapers,
            'msg' => $msg
        ], 201);
    }
    public function et_store(Request $request)
    {
        $request->validate([
            'epaper_collection_id'        =>  'required',
        ]);
        $collection_epapers  = [];
        $msg = '';
        $existing_collection_epaper = CollectionEpaper::where(['epaper_collection_id' => request()->epaper_collection_id, 'et_article_id' => request()->et_article_id])->first();
        if (!$existing_collection_epaper) {
            $collection_epapers = new CollectionEpaper(request()->all());
            $collection_epapers->save();
        } else {
            $msg = 'ET Epaper already exist';
        }


        return response()->json([
            'data'    =>  $collection_epapers,
            'msg' => $msg
        ], 201);
    }

    /*
     * To view a single user_section
     *
     *@
     */
    public function show(CollectionEpaper $collection_epaper)
    {
        return response()->json([
            'data'   =>  $collection_epaper,
            'success' =>  true
        ], 200);
    }

    /*
     * To update a user_section
     *
     *@
     */
    public function update(Request $request, CollectionEpaper $collection_epaper)
    {
        $collection_epaper->update($request->all());

        return response()->json([
            'data'  =>  $collection_epaper
        ], 200);
    }

    public function destroy($id)
    {
        $collection_epapers = CollectionEpaper::find($id);
        $collection_epapers->delete();

        return response()->json([
            'message' =>  'Deleted'
        ], 204);
    }
}
