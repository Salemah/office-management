<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee\EmployeeIdentity;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

class EmployeeIdentityController extends Controller
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'id_type_id' => 'required',
            'id_no' => 'required|numeric',
        ]);
        try {
            $data = new EmployeeIdentity();
            $data->employee_id = $request->employee_id;
            $data->id_type_id = $request->id_type_id;
            $data->id_no = $request->id_no;
            $data->user_type = 1; // usertype  1 == Employee
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.employee.show', $request->employee_id)->with('message', 'Create successfully.');
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
    public function show(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                $EmployeeIdentity = EmployeeIdentity::where('employee_id', $id)
                    ->where('user_type', 1)
                    ->with('employee', 'identity')
                    ->latest()->get();
                return DataTables::of($EmployeeIdentity)
                    ->addIndexColumn()
                    ->addColumn('identityType', function ($Employee) {
                        return $Employee->identity->name;
                    })
                    ->addColumn('action', function ($EmployeeIdentity) {
                        if (Auth::user()->can('employee_identity_delete')){
                            $deleteBtn = ' <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $EmployeeIdentity->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>';
                        }else{
                            $deleteBtn = '';
                        }
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">'.$deleteBtn.'</div>';
                    })
                    ->rawColumns(['employeeName', 'identityType', 'action'])
                    ->make(true);
            }
            return view('admin.employee.show');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

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
                EmployeeIdentity::where('id', $id)->where('user_type', 1)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'EmployeeIdentity Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
