<?php

namespace App\Http\Controllers;

use App\EtArticle;
use App\EtXml;
use Illuminate\Http\Request;

class EtXmlsController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:api']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $et_xmls = EtXml::get();
        if (request()->page && request()->rowsPerPage) {
            $et_xmls = new EtXml;
            if (request()->search_keyword) {
                $et_xmls = $et_xmls
                    ->where('xmlpath', 'LIKE', '%' . request()->search_keyword . '%');
            }
            $count = $et_xmls->count();
            $et_xmls = $et_xmls->paginate(request()->rowsPerPage)->toArray();
            $et_xmls = $et_xmls['data'];
        }

        return response()->json([
            'data'     =>  $et_xmls,
            'count'    =>   $count,
            'success'   =>  true,
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
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

    public function processETXML()
    {
        $xml =  EtXml::where('id', request()->id)->first();
        if ($xml->is_process != true) {
            $path = 'https://aaibuzz-spc-1.s3.ap-south-1.amazonaws.com/' . request()->xmlpath;
            $xmlString = file_get_contents($path);
            // $xmlString = file_get_contents("https://aaibuzz-spc-1.s3.ap-south-1.amazonaws.com/infakt-api/TOI-Epaper210722.xml");
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
