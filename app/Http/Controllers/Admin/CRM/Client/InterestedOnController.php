<?php

namespace App\Http\Controllers\Admin\CRM\Client;

use App\Http\Controllers\Controller;
use App\Models\CRM\Client\InterestedOn;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class InterestedOnController extends Controller
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

                $InterestedOn = InterestedOn::latest()->get();

                return DataTables::of($InterestedOn)
                    ->addIndexColumn()
                    ->addColumn('status', function ($InterestedOn) {
                        if ($InterestedOn->status == 1) {
                            return '<button
                              onclick="showStatusChangeAlert(' . $InterestedOn->id . ')"
                               class="btn btn-sm btn-primary ">Active</button>';
                        } else {
                            return '<button
                              onclick="showStatusChangeAlert(' . $InterestedOn->id . ')"
                              class="btn btn-sm btn-warning">Inactive</button>';
                        }
                    })
                    ->addColumn('description', function ($InterestedOn) {
                        return $InterestedOn->description;
                    })
                    ->addColumn('action', function ($InterestedOn) {
                         if (Auth::user()->can('interested_edit')){
                            $editBtn = '<a class="btn btn-sm btn-success text-white edit-InterestedOn" style="cursor:pointer"
                            href="' . route('admin.crm.interested-on.edit', $InterestedOn->id) . '" title="Edit"><i class="bx bxs-edit"></i></a>';
                        }else{
                            $editBtn = '';
                        }
                        if (Auth::user()->can('interested_delete')){
                            $deleteBtn = '<a class="btn  btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $InterestedOn->id . ')"title="Delete"><i class="bx bxs-trash"></i></a>';
                        }else{
                            $deleteBtn = '';
                        }
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">'.$editBtn.$deleteBtn.'</div>';
                    })
                    ->rawColumns(['status', 'action', 'description'])
                    ->make(true);
            }

            return view('admin.crm.client.settings.interested-on.index');
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
         return view('admin.crm.client.settings.interested-on.create');
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
            'name' => 'required|unique:interested_ons,name',
            'status' => 'required',

        ]);
        // Validation End

        // Store Data
        try {

            $data = new InterestedOn();
            $data->name = $request->name;
            $data->status = $request->status;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->description = $request->description;
            $data->created_by = Auth::id();
            $data->save();

            return redirect()->route('admin.crm.interested-on.index')->with('message', 'Create successfully.');
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
    public function edit($id)
    {
        try {
            $InterestedOn = InterestedOn::where('id', $id)->first();
            return view('admin.crm.client.settings.interested-on.edit', compact('InterestedOn'));
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
            'name' => 'required|unique:interested_ons,name,id'. $id .',id',
            'status' => 'required',

        ]);
        // Validation End

        try {
            $data = InterestedOn::findOrFail($id);
            $data->name = $request->name;
            $data->status = $request->status;
            $data->description = $request->description;

            $data->updated_by  = Auth::id();


            $data->update();

            return redirect()->route('admin.crm.interested-on.index')->with('message', 'Update successfull.');
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
                InterestedOn::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Interested On Deleted Successfully.',
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

            $InterestedOn = InterestedOn::findOrFail($request->id);

            $InterestedOn->status == 1 ? $InterestedOn->status = 0 : $InterestedOn->status = 1;
            $InterestedOn->update();

            if ($InterestedOn->status == 1) {
                return "active";
            } else {
                return "inactive";
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

    }
}
