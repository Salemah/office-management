<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee\EmployeeCertificate;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

class EmployeeCertificateController extends Controller
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
            'organization_name' => 'required',
            'certificate' => 'required',
            'duration' => 'required',
            'certificate_year' => 'required',
        ]);
        try {
            $data = new EmployeeCertificate();
            $data->employee_id = $request->employee_id;
            $data->organization_name = $request->organization_name;
            $data->certificate = $request->certificate;
            $data->duration = $request->duration;
            $data->certificate_year = $request->certificate_year;
            $data->certificate_note = $request->certificate_note;
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
                $EmployeeCertificates = EmployeeCertificate::where('employee_id', $id)->where('user_type', 1)->latest()->get();
                return DataTables::of($EmployeeCertificates)
                    ->addIndexColumn()
                    ->addColumn('action', function ($EmployeeCertificates) {
                        if (Auth::user()->can('employee_certification_delete')){
                            $deleteBtn = '<a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="certificateDeleteConfirm(' . $EmployeeCertificates->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>';
                        }else{
                            $deleteBtn = '';
                        }
                        return '<div class="btn-group" role="group"  aria-label="Basic mixed styles example">'.$deleteBtn .'</div>';
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
                EmployeeCertificate::where('id', $id)->where('user_type', 1)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Certificate Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
