<?php

namespace App\Http\Controllers\Admin\CRM\Client;

use App\Http\Controllers\Controller;
use App\Models\CRM\Client\Client;
use App\Models\Employee\EmployeeIdentity;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
class ClientIdentityController extends Controller
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
                'client_id' => 'required',
                'id_type_id' => 'required',
                'id_no' => 'required|numeric',
            ]);
            try {
                $data = new EmployeeIdentity();
                $data->employee_id = $request->client_id;
                $data->id_type_id = $request->id_type_id;
                $data->id_no = $request->id_no;
                $data->user_type =2; // client userType = 2
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

                return redirect()->route('admin.crm.client.show',$request->client_id)->with('message', 'Client Idenity Add successfully.');
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
                $ClientIdentity = EmployeeIdentity::where('employee_id',$id)->where('user_type',2)->with('identity')->latest()->get();
                return DataTables::of($ClientIdentity)
                    ->addIndexColumn()
                    ->addColumn('identityType', function ($ClientIdentity) {
                        return $ClientIdentity->identity->name;
                    })
                    ->addColumn('action', function ($ClientIdentity) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $ClientIdentity->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['identityType','action'])
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
                EmployeeIdentity::where('id',$id)->where('user_type',2)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Client Identity Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
