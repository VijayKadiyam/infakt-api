<?php

namespace App\Http\Controllers;

use App\ToiXml;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Webklex\IMAP\Facades\Client;

class ToiXmlsController extends Controller
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
        $toi_search = 'TOI-Epaper';
        if (request()->page && request()->rowsPerPage) {
            // $toi_xmls = new ToiXml;
            $toi_xmls = ToiXml::where('xmlpath', 'LIKE', '%' . $toi_search . '%');
            if (request()->search_keyword) {
                $toi_xmls = $toi_xmls
                    ->where('xmlpath', 'LIKE', '%' . request()->search_keyword . '%');
            }
            $count = $toi_xmls->count();
            $toi_xmls = $toi_xmls->paginate(request()->rowsPerPage)->toArray();
            $toi_xmls = $toi_xmls['data'];
        }

        return response()->json([
            'data'     =>  $toi_xmls,
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

    public function toi_xml_imap()
    {
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
        foreach ($folders as $folder) {
            // return $folder;

            //Get all Messages of the current Mailbox $folder
            /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
            $messages = $folder->messages()->all()->get();
            // $messages = $folder->messages()->since($previous_date)->get();
            // dd($messages);
            /** @var \Webklex\PHPIMAP\Message $message */
            foreach ($messages as $message) {
                // return $message;
                $message->getAttachments()->each(function ($oAttachment) use ($message) {
                    // print_r($message);
                    // exit;
                    // return $message;
                    // if ($message->getSubject() == "TOI XML") {
                    $check_email_existing = ToiXml::where('message_id', $message->getMessageId())
                        ->first();
                    // // return $check_email_existing;
                    if (!$check_email_existing) {
                        // return 1;
                        // $path = 'infakt/xmls/' . $message->getMessageId() . '/' . $oAttachment->name;
                        $path = 'infakt/xmls/' . $oAttachment->name;
                        Storage::disk('local')->put($path, $oAttachment->content, 'public');
                        // Storage::disk('s3')->put($path, $oAttachment->content, 'public');

                        $data = [
                            // 'subject' => $message->getSubject(),
                            // 'body' => $message->getHTMLBody(),
                            'xmlpath' => $path,
                            'message_id' => $message->getMessageId(),

                        ];
                        // return $data;
                        // dd($data);

                        $toi_xml = new ToiXml($data);
                        $toi_xml->save();
                        // }
                    }
                });
            }
        }
    }
}
