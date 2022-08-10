<?php

namespace App\Http\Controllers;

use App\ToiArticle;
use Illuminate\Http\Request;

class ToiArticlesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }
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

    public function processTOIXML()
    {

        $xmlString = file_get_contents("https://aaibuzz-spc-1.s3.ap-south-1.amazonaws.com/infakt-api/TOI-Epaper210722.xml");
        $xmlObject = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

        $json = json_encode($xmlObject);
        $phpArray = json_decode($json, true);
        $editions = $phpArray['Edition'];
        $data = [];
        foreach ($editions as $i => $edition) {
            $edition_name = $edition["@attributes"]['EdName'];

            foreach ($edition['body'] as $k => $content) {
                $headline = $content['body.head']['headline']['h1'];
                $story_id = $content['body.head']['dateline']['story-id'];
                $story_date = $content['body.head']['dateline']['storydate'];
                $byline = $content['body.head']['dateline']['byline'];
                $category = $content['body.head']['dateline']['category'];
                $drophead = $content['body.head']['dateline']['drophead'];
                $content = $content['body.content']['block'];

                $data = [
                    'toi_xml_id'   => 1,
                    'edition_name' => json_encode($edition_name),
                    'headline'     => json_encode($headline),
                    'story_id'     => json_encode($story_id),
                    'story_date'   => json_encode($story_date),
                    'byline'       => json_encode($byline),
                    'category'     => json_encode($category),
                    'drophead'     => json_encode($drophead),
                    'content'      => json_encode($content),
                ];
                // $data=json_encode($data);
                $toi_article = new ToiArticle($data);
                $toi_article->save($data);
                $toi_articles[] = $toi_article;
            }
        }

        return response()->json([
            'data'  =>  $toi_articles,
        ]);
    }
}
