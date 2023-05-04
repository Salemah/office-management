<?php

namespace App\Http\Controllers\Admin\CRM\Client;

use App\Http\Controllers\Controller;
use App\Models\CRM\Client\Client;
use App\Models\Documents;
use App\Models\Employee\EmployeeDocuments;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
class ClientDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            // Validation Start
            $request->validate([
                'client_id' => 'required',
                'document_name' => 'required',
                'document' => 'required',
            ]);
            try {
                $data = new Documents();
                $data->document_id = $request->client_id;
                $data->document_name = $request->document_name;
                $data->document_type =2; // client userType = 2

                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/employee/documents/'), $filename);
                $data->document_file = $filename;

                $data->created_by = Auth::user()->id;
                $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $data->save();

                $client = Client::findOrFail($request->client_id);
                if ($client->is_assign == false) {
                    $client->is_assign = 1;
                    $own_assign = json_decode($client->own_assign);
                    $own_assign [] = Auth::id();
                    $client->own_assign = json_encode($own_assign);
                    $client->update();
                }


                return redirect()->route('admin.crm.client.show',$request->client_id)->with('message', 'Client Document Add successfully.');
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                $ClientDocuments = Documents::where('document_id',$id)->where('document_type',2)->latest()->get();
                return DataTables::of($ClientDocuments)
                    ->addIndexColumn()
                    ->addColumn('action', function ($ClientDocuments) {
                        $url= asset('img/employee/documents/'.$ClientDocuments->document_file);
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a href="'. $url .'" class="btn btn-sm btn-success text-white" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="documentDeleteConfirm(' . $ClientDocuments->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.crm.client.show');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                Documents::where('id',$id)->where('document_type',2)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Client Document Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
