<?php

namespace App\Http\Controllers\Admin\CRM\Client;

use App\Http\Controllers\Controller;
use App\Models\CRM\Client\ClientType;
use App\Models\Settings\Priority;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $ClientType = ClientType::latest()->get();
                return DataTables::of($ClientType)
                    ->addIndexColumn()
                    ->addColumn('status', function ($ClientType) {
                        if ($ClientType->status == 1) {
                            return '<button onclick="showStatusChangeAlert(' . $ClientType->id . ')"
                                    class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button onclick="showStatusChangeAlert(' . $ClientType->id . ')"
                                    class="btn btn-sm btn-warning">Inactive</button>';
                        }
                    })
                    ->addColumn('description', function ($ClientType) {
                        return $ClientType->description;
                    })
                    ->addColumn('action', function ($ClientType) {

                        if (Auth::user()->can('client_type_edit')){
                            $editBtn = '<a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                            href="' . route('admin.crm.client-type.edit', $ClientType->id) . '" title="Edit"><i class="bx bxs-edit"></i></a>';
                        }else{
                            $editBtn = '';
                        }
                        if (Auth::user()->can('client_type_delete')){
                            $deleteBtn = '<a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $ClientType->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>';
                        }else{
                            $deleteBtn = '';
                        }
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">'.$editBtn .$deleteBtn.'</div>';
                    })
                    ->rawColumns(['status', 'action', 'description'])
                    ->make(true);
            }
            return view('admin.crm.client.settings.client-type.index');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try{
            $priorities = Priority::where('status', 1)->get();
            return view('admin.crm.client.settings.client-type.create',compact('priorities'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation Start
        $request->validate([
            'name' => 'required|unique:client_types,name',
            'status' => 'required',
            'priority' => 'required',
        ]);
        // Validation End

        // Store Data
        try {
            $data = new ClientType();
            $data->name = $request->name;
            $data->status = $request->status;
            $data->priority = $request->priority;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->description = $request->description;
            $data->created_by = Auth::id();
            $data->save();

            return redirect()->route('admin.crm.client-type.index')->with('message', 'Create successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            $priorities = Priority::where('status', 1)->get();
            $ClientType = ClientType::where('id', $id)->first();
            return view('admin.crm.client.settings.client-type.edit', compact('ClientType','priorities'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validation Start
        $request->validate([
            'name' => 'required|unique:client_types,name,'. $id .',id',
            'status' => 'required',
            'priority' => 'required',
        ]);
        // Validation End

        // Store Data
        try {
            $data = ClientType::where('id', $id)->first();

            $data->name = $request->name;
            $data->status = $request->status;
            $data->priority = $request->priority;
            $data->description = $request->description;
            $data->updated_by = Auth::id();

            $data->update();

            return redirect()->route('admin.crm.client-type.index')->with('message', 'Create successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                ClientType::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Item Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    //starts status change function
    public function statusUpdate(Request $request)
    {
        try {
            $clientType = ClientType::findOrFail($request->id);

            $clientType->status == 1 ? $clientType->status = 0 : $clientType->status = 1;

            $clientType->update();

            if ($clientType->status == 1) {
                return "active";
            } else {
                return "inactive";
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
