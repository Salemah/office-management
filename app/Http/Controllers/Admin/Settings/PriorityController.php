<?php

namespace App\Http\Controllers\Admin\Settings;

use DataTables;
use Illuminate\Http\Request;
use App\Models\Settings\Priority;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class PriorityController extends Controller
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
                $prioritys = Priority::latest()->get();
                return DataTables::of($prioritys)
                    ->addIndexColumn()
                    ->addColumn('status', function ($prioritys) {
                        if ($prioritys->status == 1) {
                            return '<button
                            onclick="showStatusChangeAlert(' . $prioritys->id . ')"
                             class="btn btn-sm btn-primary">Active</button>';
                        } else {
                            return '<button
                            onclick="showStatusChangeAlert(' . $prioritys->id . ')"
                            class="btn btn-sm btn-warning">In-active</button>';
                        }
                    })
                    ->addColumn('action', function ($prioritys) {
                        if (Auth::user()->can('priority_edit')){
                            $editBtn = ' <a class="btn btn-sm btn-success text-white " style="cursor:pointer"
                            href="' . route('admin.settings.priority.edit', $prioritys->id) . '" title="Edit"><i class="bx bxs-edit"></i></a>';
                        }else{
                            $editBtn = '';
                        }
                        if (Auth::user()->can('priority_delete')){
                            $deleteBtn = ' <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $prioritys->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>';
                        }else{
                            $deleteBtn = '';
                        }
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">'.$editBtn .$deleteBtn.'</div>';
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
            return view('admin.settings.priority.index');
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
            // return view('admin.account.prioritys.create');
            return view('admin.settings.priority.create');
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
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
        $request->validate([
            'name'   => 'required|string|unique:priorities,name',
            'status' => 'required'
        ]);

        try {
            $data              =    new Priority();
            $data->name        =    $request->name;
            $data->description =    $request->description;
            $data->status      =    $request->status;
            $data->created_by  =    Auth::user()->id;
            $data->access_id   =    json_encode(UserRepository::accessId(Auth::id()));
            $data->save();
            return redirect()->route('admin.settings.priority.index')->with('message', 'Add successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
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
        // try {
        //     $Priority = Priority::where('id', $id)->with('createdByUser')->first();
        //     return view('administrator.account.Priority.Priority_info.show', compact('Priority'));
        // } catch (\Exception $exception) {
        //     return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
        // }
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
            $priority = Priority::findOrFail($id);
            return view('admin.settings.priority.edit', compact('priority'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
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
        $request->validate([
            'name'      =>  'required|string',
            'status'    =>  'required|numeric'
        ]);

        try {
            $data               =    Priority::find($id);
            $data->name         =    $request->name;
            $data->description  =    $request->description;
            $data->status       =    $request->status;
            $data->updated_by   =    Auth::user()->id;
            $data->update();

            return redirect()->route('admin.settings.priority.index')->with('message', 'Update successfully.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('exception', 'Operation failed ! ' . $exception->getMessage());
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
                Priority::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Priority Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    /**
     * Update the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    //starts status change function
    public function statusUpdate(Request $request)
    {
        try {
            $priority         =  Priority::findOrFail($request->id);
            $priority->status == 1 ? $priority->status = 0 : $priority->status = 1;
            $priority->update();

            if ($priority->status == 1) {
                return "active";
            } else {
                return "inactive";
            }
        }
        catch (\Exception $exception) {
            return  $exception->getMessage();
        }
    }

}
