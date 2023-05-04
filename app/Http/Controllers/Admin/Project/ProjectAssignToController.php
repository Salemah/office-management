<?php
namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Employee\Department;
use App\Models\Employee\Employee;
use App\Models\Project\Projects;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProjectAssignToController extends Controller
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
                $projects = Projects::with('projectCategory')->latest()->get();
                return DataTables::of($projects)
                    ->addIndexColumn()
                    ->addColumn('project_category', function ($projects) {
                        if ($projects->projectCategory) {
                            return $projects->projectCategory->name;
                        } else {
                            return '';
                        }

                    })
                    ->addColumn('status', function ($projects) {
                        if ($projects->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white">Up Coming</button>';
                        } else if ($projects->status == 2) {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" >On Going</button>';

                        } else if ($projects->status == 3) {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" >Complete</button>';

                        } else if ($projects->status == 4) {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white">Cancel</button>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($projects) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                  <a href="' . route('admin.projects.show', $projects->id) . '" class="btn btn-sm btn-primary text-white" style="cursor:pointer" title="Show"><i class="bx bx-show"></i></a>
                                  <a href="' . route('admin.projects.edit', $projects->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $projects->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['project_category','status','action'])
                    ->make(true);
            }
            return view('admin.project.project.index');
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
            $projects = Projects::count()+1;
            return view('admin.project.project.create',compact('projects'));
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
            'department_id' =>'required',
            'employee_id' =>'required',
        ]);

        try {
            $data = User::where('user_type', 1)
                    ->where('user_id', $request->employee_id)
                    ->first();

            $project = Projects::findOrFail($request->project_id);
            $assign_to = json_decode($project->assign_employee_id);
            $assign_to[] = $data->id;
            $project->assign_employee_id = json_encode($assign_to);//store employee auth id
            $project->updated_by = Auth::user()->id;
            $project->update();

            return redirect()->route('admin.project.employee.assign.to',$request->project_id)->with('message', 'Project Assign Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function employeeAssignTo(Request $request, $id)
    {
        try {
            $departments =  Department::all();
            $project= Projects::findOrFail($id);
            return view('admin.project.project.show.assignto.assignto-show', compact('project','departments'));
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
    public function show(Request $request, $id)
    {
        try{
            if ($request->ajax()) {
                $project = Projects::where('id', $id)
                            ->first('assign_employee_id'); //assign to ==  Employee(Auth Id)

                $userIds = [];
                if($project->assign_employee_id != null)
                {
                    $userIds = User::where('user_type', 1)
                    ->whereIn('id', json_decode($project->assign_employee_id))
                    ->pluck('user_id'); //here id == employee(Auth id) . because assign to using auth id
                    //user_id' ==  employee Table Id
                }

                $employees = Employee::whereIn('id', $userIds)
                            ->get();

                return DataTables::of($employees)
                    ->addIndexColumn()
                    ->addColumn('assignTo', function ($employees) {
                        return $employees->name;
                    })
                    ->addColumn('action', function ($employees) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                            <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="assignDeleteConfirm(' . $employees->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['assignTo', 'action'])
                    ->make(true);
            }

            return view('admin.project.project.show.assignto.assignto-show',compact('project'));
        }
        catch(\Exception $exception){
            return redirect()->back()->with('error', $exception->getMessage());
        }
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
            $projects = Projects::count()+1;
            return view('admin.project.project.edit',compact('projects'));
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
                $user_id = User::where('user_type', 1)
                    ->where('user_id', $id)
                    ->first();

                $Client = Projects::where('id', $request->clientId)->first();

                $assign_to = json_decode($Client->assign_employee);

                $array = array_diff($assign_to, [$user_id->id]);

                $Client->assign_employee = json_encode($array);
                dd($Client);
                $Client->update();

                return response()->json([
                    'success' => true,
                    'message' => 'Assign Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return back()->with('error', $exception->getMessage());
            }
        }
    }

    /**
     * Status Change the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function AllEmployeeSearch(Request $request)
    {
        if ($request->ajax()) {
            try {
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
    }
    public function ReportingEmployeeSearch(Request $request)
    {
        if ($request->ajax()) {
            try {
                $project = Projects::where('id', $request->project)
                    ->first('reporting_person_id'); //assign to ==  Employee(Auth Id)

                    // $userIds = User::where('user_type', 1)
                    // ->whereNotIn('id', json_decode($project))
                    // ->pluck('user_id');

                    // whereIn('id', $userIds)
                    // ->
                $result = Employee::get();

                return $result;
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    public function AddReportingEmployee(Request $request)
    {
        $request->validate([
            'reporting_person_id' =>'required',
        ]);

        try {
            $data = User::where('user_type', 1)
                    ->where('user_id', $request->reporting_person_id)
                    ->first();
            $project = Projects::findOrFail($request->project_id);
            $assign_to = json_decode($project->reporting_person_id);
            $assign_to[] = $data->id;
            $project->reporting_person_id = json_encode($assign_to);//store employee auth id
            $project->updated_by = Auth::user()->id;
            $project->update();

            return redirect()->route('admin.project.employee.assign.to',$request->project_id)->with('message', 'Project Assign Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function ReportingEmployeeShow(Request $request, $id)
    {
        try{
            if ($request->ajax()) {
                $project = Projects::where('id', $id)
                            ->first('reporting_person_id'); //assign to ==  Employee(Auth Id)
                $userIds = [];
                if($project->reporting_person_id != null)
                    {
                        $userIds = User::where('user_type', 1)
                            ->whereIn('id', json_decode($project->reporting_person_id))
                            ->pluck('user_id'); //here id == employee(Auth id) . because assign to using auth id
                            //user_id' ==  employee Table Id
                    }

                $employees = Employee::whereIn('id', $userIds)
                            ->get();
                return DataTables::of($employees)
                    ->addIndexColumn()
                    ->addColumn('assignTo', function ($employees) {
                        return $employees->name;
                    })
                    ->addColumn('action', function ($employees) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                            <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="assignDeleteConfirm(' . $employees->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a></div>';
                    })
                    ->rawColumns(['assignTo', 'action'])
                    ->make(true);
            }

            return view('admin.project.project.show.assignto.assignto-show',compact('project'));
        }
        catch(\Exception $exception){
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }



}
