<?php

namespace App\Http\Controllers;

use App\ToiArticle;
use Illuminate\Http\Request;

class ToiArticlesController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth:api', 'company']);
    // }
    /*
     * To get all reference plans
       *
     *@
     */


    public function index(Request $request)
    {
        $toi_articles = ToiArticle::get();

        return response()->json([
            'data'     =>  $toi_articles,
            'success'   =>  true,
        ], 200);
    }

    /*
     * To store a new reference plan
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'edition_name'       =>  'required',
        ]);

        $toi_article = new ToiArticle($request->all());
        $toi_article->save();

        return response()->json([
            'data'    =>  $toi_article
        ], 201);
    }

    /*
     * To view a single reference plan
     *
     *@
     */
    public function show(ToiArticle $toi_article)
    {
        return response()->json([
            'data'   =>  $toi_article
        ], 200);
    }

    /*
     * To update a reference plan
     *
     *@
     */
    public function update(Request $request, ToiArticle $toi_article)
    {
        $request->validate([
            'edition_name'  =>  'required',
        ]);

        $toi_article->update($request->all());

        return response()->json([
            'data'  =>  $toi_article
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
        //
    }
}