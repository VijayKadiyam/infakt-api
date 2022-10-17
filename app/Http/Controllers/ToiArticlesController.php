<?php

namespace App\Http\Controllers;

use App\ToiArticle;
use App\ToiXml;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        if (request()->page && request()->rowsPerPage) {
            $toi_articles = new ToiArticle;
            $toi_articles = $toi_articles->where('word_count', '>', 100)->latest();
            if (request()->search_keyword) {
                $toi_articles = $toi_articles->where('edition_name', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('story_date', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('headline', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('byline', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('drophead', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('category', 'LIKE', '%' . request()->search_keyword . '%');
            }
            if (request()->word_count) {
                $toi_articles = $toi_articles->where('word_count', '>', request()->word_count);
            }
            if (request()->date_filter) {
                $date = date("F d Y", strtotime(request()->date_filter));
                // return $date;
                $toi_articles = $toi_articles->where('story_date', $date);
                // ->Where('story_date', $date);
            }
            // return $toi_articles = $toi_articles->get();
            $count = $toi_articles->count();
            $toi_articles = $toi_articles->paginate(request()->rowsPerPage)->toArray();
            $toi_articles = $toi_articles['data'];
        }

        // $toi_articles = DB::select("call portal_toi_articles()");

        return response()->json([
            'data'     =>  $toi_articles,
            'count'    =>   $count,
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
        $xml =  ToiXml::where('id', request()->id)->first();
        if ($xml->is_process != true) {
            $path = 'https://az-medas.s3.ap-south-1.amazonaws.com/' . request()->xmlpath;
            // return $path;
            $xmlString = file_get_contents($path);
            // return $xmlString;
            // $xmlString = file_get_contents("https://az-medas.s3.ap-south-1.amazonaws.com/infakt-api/TOI-Epaper210722.xml");
            $xmlObject = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

            $json = json_encode($xmlObject);
            $phpArray = json_decode($json, true);
            $editions = $phpArray['Edition'];
            $data = [];
            foreach ($editions as $i => $edition) {
                $edition_name = $edition["@attributes"]['EdName'];

                foreach ($edition['body'] as $k => $content) {
                    $headline = is_array($content['body.head']['headline']['h1']) ? '' : $content['body.head']['headline']['h1'];
                    $story_id = $content['body.head']['dateline']['story-id'];
                    $story_date = $content['body.head']['dateline']['storydate'];
                    $byline = is_array($content['body.head']['dateline']['byline']) ? '' : $content['body.head']['dateline']['byline'];
                    $category = is_array($content['body.head']['dateline']['category']) ? '' : $content['body.head']['dateline']['category'];
                    $drophead = is_array($content['body.head']['dateline']['drophead']) ? '' : $content['body.head']['dateline']['drophead'];
                    $content = is_array($content['body.content']['block']) ? '' : $content['body.content']['block'];
                    $word_count = str_word_count($content);

                    $data = [
                        'toi_xml_id'   => request()->id,
                        'story_id'     => $story_id,
                        'story_date'   => $story_date,
                        'category'     => $category,
                        'edition_name' => $edition_name,
                        'headline'     => $headline,
                        'byline'       => $byline,
                        'drophead'     => $drophead,
                        'content'      => $content,
                        'word_count'      => $word_count,
                    ];
                    $toi_article = new ToiArticle($data);
                    $toi_article->save($data);
                    $toi_articles[] = $toi_article;
                }
            }
            if ($toi_articles) {
                $toi_xml = ToiXml::where('id', '=', request()->id)->first();
                $toi_xml->is_process = true;
                $toi_xml->update();
            }
        } else {
            $toi_articles = 'Already Processed';
        }



        return response()->json([
            'data'  =>  $toi_articles,
        ]);
    }
}
