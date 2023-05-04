<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;

use App\Models\Project\ProjectType;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class ProjectTypeController extends Controller
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
                // $Warehouse = Warehouse::latest()->get();
                $category = ProjectType::get();
                return DataTables::of($category)
                    ->addIndexColumn()
                    ->addColumn('status', function ($category) {
                        if ($category->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $category->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $category->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('description', function ($category) {
                        return Str::limit($category->description, 20, $end = '.....');
                    })
                    ->addColumn('action', function ($category) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.project.type.edit', $category->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $category->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'status', 'description'])
                    ->make(true);
            }
            return view('admin.project.type.index');
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
            return view('admin.project.type.create');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'            =>      'required|string',
            'description'     =>      'string|nullable',
            'status'          =>      'required|numeric'
        ]);

        try {
            $data                   =       new ProjectType();
            $data->name             =       $request->name;
            $data->description      =       strip_tags($request->description);
            $data->status           =       $request->status;
            $data->created_by       =       Auth::user()->id;
            $data->access_id        =       json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.project.type.index')
                    ->with('toastr-success', 'Project type Created Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $type = ProjectType::findOrFail($id);
            return view('admin.project.type.edit', compact('type'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validation Start
        $request->validate([
            'name'            =>      'required|string',
            'description'     =>      'string|nullable',
            'status'          =>      'required|numeric'
        ]);
        // Validation End

        // Store Data
        try {
            $data                   =       ProjectType::where('id', $id)->first();
            $data->name             =       $request->name;
            $data->description      =       strip_tags($request->description);;
            $data->status           =       $request->status;
            $data->updated_by       =       Auth::user()->id;
            $data->save();

            return redirect()->route('admin.project.type.index')
                    ->with('toastr-success', 'Product type Updated Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                ProjectType::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Product type Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    /**
     * Status Change the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function statusUpdate(Request $request, $id)
    {
        try {
            $type = ProjectType::findOrFail($id);
            // Check Item Current Status
            if ($type->status == 1) {
                $type->status = 0;
            } else {
                $type->status = 1;
            }

            $type->save();
            return response()->json([
                'success' => true,
                'message' => 'Product type Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
