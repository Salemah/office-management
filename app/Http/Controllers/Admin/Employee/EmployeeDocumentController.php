<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Models\Documents;
use App\Models\Employee\EmployeeDocuments;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
class EmployeeDocumentController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            // Validation Start
            $request->validate([
                'employee_id' => 'required',
                'document_name' => 'required',
                'document' => 'required',
            ]);
            try {
                $data = new Documents();
                $data->document_id = $request->employee_id;
                $data->document_name = $request->document_name;
                $data->document_type = 1; // usertype  1 == Employee

                $file = $request->file('document');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path('/img/employee/documents/'), $filename);
                $data->document_file = $filename;

                $data->created_by = Auth::user()->id;
                $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $data->save();

                return redirect()->route('admin.employee.show',$request->employee_id)->with('message', 'EmployeeDocument successfully.');
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                $EmployeeDocuments = Documents::where('document_id',$id)->where('document_type',1)->latest()->get();
                return DataTables::of($EmployeeDocuments)
                    ->addIndexColumn()
                    ->addColumn('action', function ($EmployeeDocuments) {
                        $url= asset('img/employee/documents/'.$EmployeeDocuments->document_file);
                        if (Auth::user()->can('employee_documents_delete')){
                            $deleteBtn = '<a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="documentDeleteConfirm(' . $EmployeeDocuments->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>';
                        }else{
                            $deleteBtn = '';
                        }
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <a href="'. $url .'" class="btn btn-sm btn-success text-white" style="cursor:pointer" type="submit"  title="Download"> <i class="bx bxs-download"></i></a>'.$deleteBtn.'</div>';
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                EmployeeDocuments::where('id',$id)->where('user_type',1)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'EmployeeDocument Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
}
