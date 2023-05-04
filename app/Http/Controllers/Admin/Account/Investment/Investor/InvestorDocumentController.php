<?php

namespace App\Http\Controllers\Admin\Account\Investment\Investor;

use App\Http\Controllers\Controller;
use App\Models\Account\Investment\InvestorDocument;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
class InvestorDocumentController extends Controller
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
                'investor_id' => 'required',
                'document_name' => 'required',
                'document' => 'required',
            ]);
            try {
                $data = new InvestorDocument();
                $data->investor_id = $request->investor_id;
                $data->document_name = $request->document_name;
                $data->user_type =1;

                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/investor/documents/'), $filename);
                $data->document = $filename;

                $data->created_by = Auth::user()->id;
                $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $data->save();

                return redirect()->route('admin.investor.show',$request->investor_id)->with('message', 'Investor Document Add successfully.');
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
                $investorDocuments = InvestorDocument::where('investor_id',$id)->where('user_type',1)->latest()->get();
                return DataTables::of($investorDocuments)
                    ->addIndexColumn()
                    ->addColumn('action', function ($investorDocuments) {
                        $url= asset('/img/investor/documents/'.$investorDocuments->document);
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a href="'. $url .'" class="btn btn-sm btn-success text-white" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="documentDeleteConfirm(' . $investorDocuments->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.account.investment.investor.show',$id);
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
                InvestorDocument::where('id',$id)->where('user_type',1)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Investor Document Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
