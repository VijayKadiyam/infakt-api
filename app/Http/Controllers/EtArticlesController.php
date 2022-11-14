<?php

namespace App\Http\Controllers;

use App\EtArticle;
use App\EtXml;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EtArticlesController extends Controller
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
        // return 1;
        if (request()->page && request()->rowsPerPage) {
            $et_articles = new EtArticle();
            $et_articles = $et_articles->with('contents')->where('word_count', '>', 100)
                ->orderBy('story_date', 'DESC');
            if (request()->search_keyword) {
                $et_articles = $et_articles->where('edition_name', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('id', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('story_date', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('headline', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('byline', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('drophead', 'LIKE', '%' . request()->search_keyword . '%')
                    ->orWhere('category', 'LIKE', '%' . request()->search_keyword . '%');
            }
            if (request()->word_count) {
                $et_articles = $et_articles->where('word_count', '>', request()->word_count);
            }
            if (request()->date_filter) {
                $et_articles = $et_articles->where('story_date', request()->date_filter);
            }
            $count = $et_articles->count();
            $et_articles = $et_articles->paginate(request()->rowsPerPage)->toArray();
            $et_articles = $et_articles['data'];
        }
        return response()->json([
            'data'     =>  $et_articles,
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

        $et_article = new EtArticle($request->all());
        $et_article->save();

        return response()->json([
            'data'    =>  $et_article
        ], 201);
    }

    /*
     * To view a single reference plan
     *
     *@
     */
    public function show(EtArticle $et_article)
    {
        return response()->json([
            'data'   =>  $et_article
        ], 200);
    }

    /*
     * To update a reference plan
     *
     *@
     */
    public function update(Request $request, EtArticle $et_article)
    {
        $request->validate([
            'edition_name'  =>  'required',
        ]);

        $et_article->update($request->all());

        return response()->json([
            'data'  =>  $et_article
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
        $xml =  EtXml::where('id', request()->id)->first();
        if ($xml->is_process != true) {
            $path = 'https://az-medias.s3.ap-south-1.amazonaws.com/' . request()->xmlpath;
            $xmlString = file_get_contents($path);
            // $xmlString = file_get_contents("https://az-medias.s3.ap-south-1.amazonaws.com/infakt-api/TOI-Epaper210722.xml");
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
                        'et_xml_id'   => request()->id,
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
                    return $data;
                    $et_article = new EtArticle($data);
                    $et_article->save($data);
                    $et_articles[] = $et_article;
                }
            }
            if ($et_articles) {
                $et_xml = EtXml::where('id', '=', request()->id)->first();
                $et_xml->is_process = true;
                $et_xml->update();
            }
        } else {
            $et_articles = 'Already Processed';
        }

        return response()->json([
            'data'  =>  $et_articles,
        ]);
    }
}
