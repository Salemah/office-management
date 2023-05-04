<?php

namespace App\Http\Controllers\Admin\CRM\Client\ContactPerson;

use App\Http\Controllers\Controller;
use App\Models\CRM\Client\Client;
use App\Models\CRM\Client\ClientContactPeople;
use App\Models\Documents;
use App\Models\Employee\EmployeeDocuments;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
class ContactPersonController extends Controller
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
                'name' => 'required',
                'phone' => 'required',
            ]);
            try {
                $data = new ClientContactPeople();
                $data->client_id = $request->client_id;
                $data->name = $request->name;
                $data->email = $request->email;
                $data->phone = $request->phone;
                $data->designation = $request->designation;
                $data->description = $request->description;
                $data->date_of_birth = $request->date_of_birth;
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

                return redirect()->route('admin.crm.client.show',$request->client_id)->with('message', 'Contact Person Add successfully.');
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
                $clientContactPerson = ClientContactPeople::where('client_id',$id)->latest()->get();
                return DataTables::of($clientContactPerson)
                    ->addIndexColumn()
                    ->addColumn('action', function ($clientContactPerson) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="contactPersonDeleteConfirm(' . $clientContactPerson->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a></div>';
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
         // Validation Start
         $request->validate([
            'client_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
        ]);
        try {
            $data = ClientContactPeople::findOrFail($id);
            $data->client_id = $request->client_id;
            $data->name = $request->name;
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->designation = $request->designation;
            $data->description = $request->description;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->update();

            return redirect()->route('admin.crm.client.show',$request->client_id)->with('message', 'Contact Person Update successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
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
                ClientContactPeople::where('id',$id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Contact Person Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
