<?php

namespace App\Http\Controllers\Admin\CRM\Client;

use App\Http\Controllers\Controller;
use App\Models\CRM\Client\ClientBusinessCategory;
use App\Models\CRM\Client\ClientType;
use App\Models\Settings\Priority;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientBusinessController extends Controller
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
                $clientBusinessCategory = ClientBusinessCategory::orderBy('name')->get();
                return DataTables::of($clientBusinessCategory)
                    ->addIndexColumn()
                    ->addColumn('status', function ($clientBusinessCategory) {
                        if ($clientBusinessCategory->status == 1) {
                            return '<button onclick="showStatusChangeAlert(' . $clientBusinessCategory->id . ')"
                                    class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button onclick="showStatusChangeAlert(' . $clientBusinessCategory->id . ')"
                                    class="btn btn-sm btn-warning">Inactive</button>';
                        }
                    })

                    ->addColumn('action', function ($clientBusinessCategory) {

                        if (Auth::user()->can('business_category_edit')){
                            $editBtn = '<a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                            href="' . route('admin.crm.client-business-category.edit', $clientBusinessCategory->id) . '" title="Edit"><i class="bx bxs-edit"></i></a>';
                        }else{
                            $editBtn = '';
                        }
                        if (Auth::user()->can('business_category_delete')){
                            $deleteBtn = '<a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $clientBusinessCategory->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>';
                        }else{
                            $deleteBtn = '';
                        }
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">'.$editBtn .$deleteBtn.'</div>';
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('admin.crm.client.settings.client-business-category.index');
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
            return view('admin.crm.client.settings.client-business-category.create');
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
            'name' => 'required|unique:client_business_categories,name',
            'status' => 'required',
        ]);
        // Validation End

        // Store Data
        try {
            $data = new ClientBusinessCategory();
            $data->name = $request->name;
            $data->status = $request->status;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->created_by = Auth::id();
            $data->save();

            return redirect()->route('admin.crm.client-business-category.index')->with('message', 'Create successfully.');
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
            $clientBusinessCategory = ClientBusinessCategory::where('id', $id)->first();
            return view('admin.crm.client.settings.client-business-category.edit', compact('clientBusinessCategory'));
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
            'name' => 'required|unique:client_business_categories,name,'. $id .',id',
            'status' => 'required',
        ]);
        // Validation End

        // Store Data
        try {
            $data = ClientBusinessCategory::where('id', $id)->first();
            $data->name = $request->name;
            $data->status = $request->status;
            $data->updated_by = Auth::id();

            $data->update();

            return redirect()->route('admin.crm.client-business-category.index')->with('message', 'Update successfully.');
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
                ClientBusinessCategory::findOrFail($id)->delete();
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
            $clientBusinessCategory = ClientBusinessCategory::findOrFail($request->id);

            $clientBusinessCategory->status == 1 ? $clientBusinessCategory->status = 0 : $clientBusinessCategory->status = 1;

            $clientBusinessCategory->update();

            if ($clientBusinessCategory->status == 1) {
                return "active";
            } else {
                return "inactive";
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}

