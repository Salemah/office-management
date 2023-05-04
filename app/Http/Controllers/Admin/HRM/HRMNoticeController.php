<?php

namespace App\Http\Controllers\Admin\HRM;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Employee\Department;
use App\Models\HRM\HrmNotice;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HRMNoticeController extends Controller
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
                $notice = HrmNotice::latest()->get();
                return DataTables::of($notice)
                    ->addIndexColumn()
                    ->addColumn('description', function ($notice) {
                        return Str::limit($notice->description, 20, $end = '.....');
                    })
                    ->addColumn('status', function ($notice) {
                        if ($notice->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $notice->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $notice->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($notice) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"> <a class="btn btn-sm btn-primary text-white " title="Show" style="cursor:pointer"
                        href="' . route('admin.hrm.notice.show', $notice->id) . '"><i class="bx bx-show"> </i> </a><a href="' . route('admin.hrm.notice.edit', $notice->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $notice->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'description', 'status'])
                    ->make(true);
            }
            return view('admin.hrm.notice.index');
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
            $employees   = Employee::all();
            $departments = Department::all();
            return view('admin.hrm.notice.create',
                compact('employees', 'departments'));
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
        $request->validate([
            'notice_title'  => 'required',
            'notice_date'   => 'required',
            'department_id' => 'required',
            'expense_by_id' => 'required',
        ]);

        $document_file = [];
        if ($request->hasfile('document_file')) {
            foreach ($request->file('document_file') as $image) {
                $name = $image->getClientOriginalName();
                $image->move(public_path() . '/img/hrm/notice/documents', $name);
                $document_file[] = $name;
                array_push($document_file);
            }
        }
        try {
            $notice = new HrmNotice();

            $notice->notice_title   =   $request->notice_title;
            $notice->notice_date    =   $request->notice_date;
            $notice->status         =   $request->status;
            $notice->department_id  =   json_encode($request->department_id);
            $notice->employee_id    =   json_encode($request->expense_by_id);
            $notice->document_file  =   json_encode($document_file);
            $notice->document_title =   json_encode($request->document_title);

            $meeting = [];
            foreach ($request->meeting_date as $key => $value) {
                $meeting ['date'][]     = $request->meeting_date[$key];
                $meeting ['time'][]     = $request->meeting_time[$key];
                $meeting ['link'][]     = $request->meeting_link[$key];
                $meeting ['purpose'][]  = $request->meeting_purpose[$key];
            }
            $notice->meetings   = json_encode($meeting);

            $notice->description    = strip_tags($request->description);
            $notice->created_by     = Auth::user()->id;
            $notice->access_id      = json_encode(UserRepository::accessId(Auth::id()));

            if ($notice->save()) {
                return redirect()->route('admin.hrm.notice.index')->with('message', 'Notice Created  Successfully');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
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
        $notice = HrmNotice::with('expenseBy', 'createdBy')->findOrFail($id);

        $departmentIds   =  json_decode($notice->department_id);
        $employeeIds     =  json_decode($notice->employee_id);
        $departmentNames =  '';
        $employeeNames   =  '';
        if( in_array("0", $departmentIds) && in_array("0", $employeeIds) ){
            $departmentNames = 'All Department, ';
            $employeeNames   = 'All Employee, ';
        }
        else if( !in_array("0", $departmentIds) && in_array("0", $employeeIds) ){
            $departments = Department::whereIn('id', $departmentIds)->get(['id', 'name']);
            foreach ($departments as $key => $item) {
                $departmentNames .= $item->name . ", ";
            }
            $employeeNames   = 'All Employee, ';
        }
        else if( in_array("0", $departmentIds) && !in_array("0", $employeeIds)){
            $employees = Employee::whereIn('id', $employeeIds)->get(['id', 'name']);
            foreach ($employees as $key => $item) {
                $employeeNames .= $item->name . ", ";
            }
            $departmentNames = 'All Department, ';
        }
        else{
            $departments = Department::whereIn('id', $departmentIds)->get(['id', 'name']);
            foreach ($departments as $key => $item) {
                $departmentNames .= $item->name . ", ";
            }
            $employees = Employee::whereIn('id', $employeeIds)->get(['id', 'name']);
            foreach ($employees as $key => $item) {
                $employeeNames .= $item->name . ", ";
            }
        }
        $departmentNames = substr($departmentNames, 0, -2);
        $employeeNames   = substr($employeeNames, 0, -2);

        $meetings = collect(json_decode($notice->meetings));
        $document_file = json_decode($notice->document_file);
        $documents_title = json_decode($notice->document_title);

        return view('admin.hrm.notice.show',
            compact('notice', 'document_file', 'documents_title',
                    'departmentNames', 'employeeNames', 'meetings'));
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
            $notice      =  HrmNotice::with('expenseBy', 'createdBy')->findOrFail($id);
            $departments =  Department::all();
            $employees   =  Employee::all();

            $meetings        =  collect(json_decode($notice->meetings));
            $documents       =  json_decode($notice->document_file);
            $documents_title =  json_decode($notice->document_title);

            return view('admin.hrm.notice.edit',
                compact('notice', 'documents', 'documents_title',
                    'departments','employees', 'meetings'));
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
        $request->validate([
            'notice_date' => 'required',
            'notice_title' => 'required',
        ]);

        $document = [];
        if ($request->hasfile('documents')) {
            foreach ($request->file('documents') as $image) {
                $name = $image->getClientOriginalName();
                $image->move(public_path() . '/img/hrm/notice/documents', $name);
                $document[] = $name;
                array_push($document);
            }
        }
        try {
            $notice = HrmNotice::findOrFail($id);
            $notice->notice_title   =   $request->notice_title;
            $notice->notice_date    =   $request->notice_date;
            $notice->status         =   $request->status;
            $notice->department_id  =   json_encode($request->department_id);
            $notice->employee_id    =   json_encode($request->expense_by_id);
            $notice->document_file  =   json_encode($document);
            $notice->document_title =   json_encode($request->document_title);

            $meeting = [];
            foreach ($request->meeting_date as $key => $value) {
                $meeting ['date'][]     =   $request->meeting_date[$key];
                $meeting ['time'][]     =   $request->meeting_time[$key];
                $meeting ['link'][]     =   $request->meeting_link[$key];
                $meeting ['purpose'][]  =   $request->meeting_purpose[$key];
            }
            $notice->meetings    = json_encode($meeting);
            $notice->description = strip_tags($request->description);
            $notice->updated_by  = Auth::user()->id;
            $notice->update();
            return redirect()->route('admin.hrm.notice.index')->with('message', 'Notice Updated Successfully');
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
                HrmNotice::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Notice Deleted Successfully.',
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
    public function employeeSearch(Request $request)
    {
        $result = Employee::query()
            ->where('name', 'LIKE', "%{$request->search}%")
            ->get(['name', 'id']);
        return $result;
    }

    public function departmentSearch(Request $request)
    {
        $result = Department::query()
            ->where('name', 'LIKE', "%{$request->search}%")
            ->get(['name', 'id']);
        return $result;
    }

    public function statusUpdate(Request $request, $id)
    {
        try {
            $notice = HrmNotice::findOrFail($id);
            // Check Item Current Status
            if ($notice->status == 1) {
                $notice->status = 0;
            } else {
                $notice->status = 1;
            }

            $notice->save();
            return response()->json([
                'success' => true,
                'message' => 'Notice Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function getDepartmentWiseEmployee(Request $request){
        try {
            // $departments = Department::whereIn('id', $request->departmentId)->get();
            // $departmentIds = [];
            // foreach($departments as $department){
            //     $departmentIds[] = $department->id;
            // }
            // $employees = Employee::whereIn('department', $departmentIds)->get();
            if(in_array("0", $request->departmentId)){
                $employees = Employee::all();
            }else{
                $employees = Employee::whereIn('department', $request->departmentId)->get();
            }
            return $employees;
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function getDepartment(Request $request){
        $departments = Department::get();
        return $departments;
    }

}




