<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee\EmployeeQualification;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

class EmployeeQualificationController extends Controller
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
        // Validation Start
        $request->validate([
            'employee_id' => 'required',
            'institute_name' => 'required',
            'degree' => 'required',
            'passing_year' => 'required',
        ]);
        try {
            $data = new EmployeeQualification();
            $data->employee_id = $request->employee_id;
            $data->institute_name = $request->institute_name;
            $data->degree = $request->degree;
            $data->passing_year = $request->passing_year;
            $data->user_type = 1; // usertype  1 == Employee
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.employee.show', $request->employee_id)->with('message', 'Employee Qualification successfully.');
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
                $EmployeeQualifications = EmployeeQualification::where('employee_id', $id)->where('user_type', 1)->latest()->get();
                return DataTables::of($EmployeeQualifications)
                    ->addIndexColumn()
                    ->addColumn('action', function ($EmployeeQualifications) {
                        if (Auth::user()->can('employee_qualifications_delete')){
                            $deleteBtn = '<a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="qualificationDeleteConfirm(' . $EmployeeQualifications->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>';
                        }else{
                            $deleteBtn = '';
                        }
                        return '<div class="btn-group" role="group"  aria-label="Basic mixed styles example">'.$deleteBtn.'</div>';
                    })
                    ->rawColumns(['action'])
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
                EmployeeQualification::where('id', $id)->where('user_type', 1)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Employee Qualification Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
