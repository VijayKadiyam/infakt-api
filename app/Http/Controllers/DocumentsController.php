<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;

class DocumentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'company']);
    }

    /*
     * To get all assembly types
     *
     *@
     */
    public function index(Request $request)
    {
        if (request()->page && request()->rowsPerPage) {
            // Pagination
            $documents = request()->company->documents();
            $documents = $documents->paginate(request()->rowsPerPage)->toArray();
            $documents = $documents['data'];
        } elseif ($request->search != '') {
            // Search
            $documents = request()->company->documents();
            $documents = $documents
                ->where('description', 'LIKE', '%' . $request->search . '%');
            if ($request->date != "undefined") {

                $documents = $documents
                    ->orwhereDate('created_at', $request->date);
            }
            $documents = $documents->latest()->get();
        } else {
            // Entire List
            $documents = request()->company->documents;
        }
        return response()->json([
            'data'     =>  $documents,
            'count' => sizeof($documents),
            'success'   =>  true
        ], 200);
    }


    /*
     * To store a new assembly type
     *
     *@
     */
    public function store(Request $request)
    {
        $request->validate([
            'description'    =>  'required',
        ]);

        $document = new Document($request->all());
        $request->company->documents()->save($document);

        return response()->json([
            'data'    =>  $document
        ], 201);
    }

    /*
     * To view a single assembly type
     *
     *@
     */
    public function show(Document $document)
    {

        return response()->json([
            'data'   =>  $document
        ], 200);
    }

    /*
     * To update an assembly type
     *
     *@
     */
    public function update(Request $request, Document $document)
    {

        $request->validate([
            'description'  =>  'required',
        ]);

        $document->update($request->all());

        return response()->json([
            'data'  =>  $document
        ], 200);
    }
}
