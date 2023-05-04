<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee\EmployeeReference;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

class EmployeeReferenceController extends Controller
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
            'reference' => 'required',
        ]);
        try {
            $data = new EmployeeReference();
            $data->employee_id = $request->employee_id;
            $data->reference_id = $request->reference;
            $data->reference_note = $request->reference_details;
            $data->user_type = 1; // usertype  1 == Employee
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.employee.show', $request->employee_id)->with('message', ' Certificates Add successfully.');
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
                $EmployeeReferences = EmployeeReference::where('employee_id', $id)->where('user_type', 1)->with('reference')->latest()->get();
                return DataTables::of($EmployeeReferences)
                    ->addIndexColumn()
                    ->addColumn('reference', function ($EmployeeReferences) {
                        return $EmployeeReferences->reference->name;
                    })
                    ->addColumn('action', function ($EmployeeReferences) {
                        if (Auth::user()->can('employee_reference_delete')){
                            $deleteBtn = '<a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="referenceDeleteConfirm(' . $EmployeeReferences->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>';
                        }else{
                            $deleteBtn = '';
                        }
                        return '<div class="btn-group" role="group"  aria-label="Basic mixed styles example">'.$deleteBtn.'</div>';
                    })
                    ->rawColumns(['reference', 'action'])
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
                EmployeeReference::where('id', $id)->where('user_type', 1)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Reference Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
