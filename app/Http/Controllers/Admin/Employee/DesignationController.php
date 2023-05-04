<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee\Designation;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignationController extends Controller
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
                $Designation = Designation::latest()->get();
                return DataTables::of($Designation)
                    ->addIndexColumn()
                    ->addColumn('status', function ($Designation) {
                        if ($Designation->status == 1) {
                            return '<button
                            onclick="showStatusChangeAlert(' . $Designation->id . ')"
                             class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button
                            onclick="showStatusChangeAlert(' . $Designation->id . ')"
                            class="btn btn-sm btn-warning">Inactive</button>';
                        }
                    })
                    ->addColumn('description', function ($Designation) {
                        return $Designation->description;
                    })
                    ->addColumn('action', function ($Designation) {
                        if (Auth::user()->can('designation_edit')){
                            $editBtn = ' <a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                            href="' . route('admin.designation.edit', $Designation->id) . '" title="Edit"><i class="bx bxs-edit"></i></a>';
                        }else{
                            $editBtn = '';
                        }
                        if (Auth::user()->can('designation_delete')){
                            $deleteBtn = ' <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $Designation->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>';
                        }else{
                            $deleteBtn = '';
                        }
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">'.$editBtn.$deleteBtn.'</div>';
                    })
                    ->rawColumns(['status', 'action', 'description'])
                    ->make(true);
            }
            return view('admin.employee.settings.designation.index');
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
        try {
             return view('admin.employee.settings.designation.create');
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
            'name' => 'required|unique:designations,name',
            'status' => 'required',

        ]);
        // Validation End
        // Store Data
        try {
            $data = new \App\Models\Employee\Designation();
            $data->name = $request->name;
            $data->status = $request->status;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->description = $request->description;
            $data->created_by = Auth::id();
            $data->save();
            return redirect()->route('admin.designation.index')->with('message', 'Create successfully.');
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
            $Designation = Designation::where('id', $id)->first();
            return view('admin.employee.settings.designation.edit', compact('Designation'));
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
            'name' => 'required|unique:designations,name,NULL,id,deleted_at,NULL'.$id,
            'status' => 'required',

        ]);
        // Validation End

        // Store Data
        try {
            $data = Designation::where('id', $id)->first();
            $data->name = $request->name;
            $data->status = $request->status;
            $data->description = $request->description;
            $data->updated_by = Auth::id();
            $data->update();

            return redirect()->route('admin.designation.index')->with('message', 'Update successfully.');
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
                Designation::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Designation Deleted Successfully.',
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
            $Designation = Designation::findOrFail($request->id);
            $Designation->status == 1 ? $Designation->status = 0 : $Designation->status = 1;

            $Designation->update();

            if ($Designation->status == 1) {
                return "active";

            } else {
                return "inactive";
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
