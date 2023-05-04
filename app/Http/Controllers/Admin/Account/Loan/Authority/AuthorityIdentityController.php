<?php

namespace App\Http\Controllers\Admin\Account\Loan\Authority;

use App\Http\Controllers\Controller;
use App\Models\Account\Investment\InvestorIdentity;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
class AuthorityIdentityController extends Controller
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
            $request->validate([
                'authority_id' => 'required',
                'id_type_id' => 'required',
                'id_no' => 'required|numeric',
            ]);
            try {
                $data = new InvestorIdentity();
                $data->investor_id = $request->authority_id;
                $data->id_type_id = $request->id_type_id;
                $data->id_no = $request->id_no;
                $data->user_type =2;
                $data->created_by = Auth::user()->id;
                $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $data->save();

                return redirect()->route('admin.loan-authority.show',$request->authority_id)->with('message', 'Investor Idenity Add successfully.');
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
                $investorIdentity = InvestorIdentity::where('investor_id',$id)->where('user_type',2)->with('identity')->latest()->get();
                return DataTables::of($investorIdentity)
                    ->addIndexColumn()
                    ->addColumn('identityType', function ($investorIdentity) {
                        return $investorIdentity->identity->name;
                    })
                    ->addColumn('action', function ($investorIdentity) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $investorIdentity->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['identityType','action'])
                    ->make(true);
            }
            return view('admin.account.loan.authority.show');
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
                InvestorIdentity::where('id',$id)->where('user_type',2)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Author Identity Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
