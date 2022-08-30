<?php

namespace App\Http\Controllers;

use App\ToiXml;
use Illuminate\Http\Request;

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
        // $toi_xmls = ToiXml::get();
        if (request()->page && request()->rowsPerPage) {
            $toi_xmls = new ToiXml;
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
}
