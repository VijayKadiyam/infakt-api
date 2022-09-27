<?php

namespace App\Http\Controllers;

use App\EtArticle;
use App\EtXml;
use App\ToiXml;
use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\Storage;

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
        $et_search = 'ET-Epaper';
        if (request()->page && request()->rowsPerPage) {
            $et_xmls = ToiXml::where('xmlpath', 'LIKE', '%' . $et_search . '%');;
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

    public function et_xml_imap()
    {
        // return 1;
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", "-1");

        set_time_limit(0);
        /** @var \Webklex\PHPIMAP\Client $client */
        $client = Client::account('default');

        //Connect to the IMAP Server
        $client->connect();


        //Get all Mailboxes
        /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
        $folders = $client->getFolders();
        // $previous_date = Carbon::now();
        // day -1
        // $previous_date = Carbon::now()->subDays(1);
        // return $previous_date;
        //Loop through every Mailbox
        /** @var \Webklex\PHPIMAP\Folder $folder */
        // return $folders;
        foreach ($folders as $folder) {
            // return $folder;

            //Get all Messages of the current Mailbox $folder
            /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
            $messages = $folder->messages()->get();
            // return $messages;
            // $messages = $folder->messages()->since($previous_date)->get();
            // dd($messages);
            /** @var \Webklex\PHPIMAP\Message $message */
            foreach ($messages as $message) {
                // return $message;
                $message->getAttachments()->each(function ($oAttachment) use ($message) {
                    // echo $message;
                    // return $message->getMessageId();
                    // if ($message->getSubject() == "ET XML") {
                    $check_email_existing = EtXml::where('message_id', $message->getMessageId())
                        ->first();
                    // return $check_email_existing;
                    if (!$check_email_existing) {
                        // return 1;
                        $path = 'infakt/et-xmls/' . $message->getMessageId() . '/' . $oAttachment->name;
                        Storage::disk('s3')->put($path, $oAttachment->content, 'public');

                        $data = [
                            // 'subject' => $message->getSubject(),
                            // 'body' => $message->getHTMLBody(),
                            'xmlpath' => $path,
                            'message_id' => $message->getMessageId(),

                        ];
                        // return $data;
                        // dd($data);

                        $et_xml = new EtXml($data);
                        $et_xml->save();
                    }
                });
            }
        }
    }
}
