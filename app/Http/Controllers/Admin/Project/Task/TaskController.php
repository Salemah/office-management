<?php

namespace App\Http\Controllers\Admin\Project\Task;

use App\Http\Controllers\Controller;
use App\Models\Documents;
use App\Models\Project\ProjectDuration;
use App\Models\Project\Projects;
use App\Models\Project\Task;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class TaskController extends Controller
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
                $category = Task::get();
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
                                  <a href="' . route('admin.project.category.edit', $category->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $category->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'status', 'description'])
                    ->make(true);
            }
            return view('admin.project.category.index');
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
            return view('admin.project.category.create');
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
        // dd($request->all());
        $request->validate([
            'task_type'=>'required',
            'status'=>'required',
            'assign_employee_id'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',
            'final_hour'=>'required',
            'estimated_hour'=>'required',
            'final_day'=>'required',
        ]);

        try {


                $task = new Task();
                $task->task_type = $request->task_type;
                if($request->task_type == 2 )
                {
                    $task->task_type_id = $request->module_id;
                }
                else{
                    $task->task_type_id = $request->project_id;
                }
                $task->project_id = $request->project_id;
                $task->status = $request->status;
                $task->description = $request->description;
                $task->assign_employee_id =json_encode($request->assign_employee_id) ;
                $task->created_by = Auth::user()->id;
                $task->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $task->save();

                $data = new ProjectDuration();
                $data->duration_type  = 3;// Task Duration Type = 2
                if($request->module_id){
                    $data->duration_type_id = $request->module_id;
                    $data->project_id = $request->project_id;
                }else{
                    $data->duration_type_id = $request->project_id;

                }
            $data->start_date = $request->start_date;
            $data->end_date = $request->end_date;
            $data->estimate_day = $request->total_day;
            $data->vacation_day = $request->vacation_day;
            $data->final_day = $request->final_day;
            $data->estimate_hour_per_day = $request->estimated_hour_day;
            $data->estimate_hour = $request->estimated_hour;
            $data->	final_hour = $request->final_hour;
            $data->adjustment_type = $request->adjustment_type;
            $data->adjustment_hour = $request->adjustment_hour;
            $data->status= $request->status;
            $data->description = $request->description;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));

            $data->save();
             //file
             if (isset($request->documents)){
                foreach ($request->file('documents') as $key => $image) {
                    $name = $image->getClientOriginalName();
                    $image->move(public_path() . '/img/project/task/documents', $name);

                    $documents = new Documents();
                    $documents->document_id = $task->id;
                    $documents->document_file = $name;
                    $documents->document_name = $request->document_title[$key];
                    $documents->document_type = 6; //task_type 6 == task
                    $documents->created_by = Auth::user()->id;
                    $documents->access_id = json_encode(UserRepository::accessId(Auth::id()));

                    $documents->save();
                }
            }

            return redirect()->route('admin.project.task.view',$request->project_id)
                ->with('toastr-success', 'Task Added Successfully');
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
    public function show(Request $request , $id)
    {
        try {
            if ($request->ajax()) {
                $task = Task::with('project','document','createdBy')
                            ->where('project_id',$id)
                            ->latest()
                            ->get();
                return DataTables::of($task)
                    ->addIndexColumn()
                    ->setRowAttr([
                        'module_id' => function($task) {
                            return $task->id;
                        },
                        'class' => function() {
                            return 'mdlBtn';
                        },
                    ])
                    ->addColumn('name', function ($task) {
                        return $task->project->project_title;
                    })
                    ->addColumn('type', function ($task) {
                        if($task->task_type == 1)
                        {
                            $type = '<span class="badge bg-info">Project</span>';
                        }
                        else if($task->task_type == 2){
                            $type = '<span class="badge bg-primary">Module</span>';
                        }
                        return $type;
                    })
                    ->addColumn('document', function ($task) {
                        // if($task->document)
                        // {
                        //     $type = '<span class="badge bg-info">Project</span>';
                        // }
                        // else if($task->task_type == 2){
                        //     $type = '<span class="badge bg-primary">Module</span>';
                        // }
                        return $task->document;
                    })
                    // ->addColumn('module_end_date', function ($projects) {
                    //         return Carbon::parse($projects->end_date)->format('d M, Y');
                    // })
                    // ->addColumn('final_day', function ($projects) {
                    //         return $projects->final_day .'d';
                    // })
                    // ->addColumn('module_estimate_hour', function ($projects) {
                    //         return $projects->estimate_hour .'h';
                    // })
                    // ->addColumn('module_final_hour', function ($projects) {
                    //         return $projects->final_hour .'h';
                    // })
                    ->addColumn('status', function ($task) {
                        if($task->status == 1){
                            $status = '<span class="badge bg-info">Up Coming</span>';
                        }
                        else if($task->status == 2){
                            $status = '<span class="badge bg-success">On Going</span>';
                        }
                        else if($task->status == 3){
                            $status = '<span class="badge bg-primary">Complete</span>';
                        }
                        else if($task->status == 4){
                            $status = '<span class="badge bg-danger">Cancel</span>';
                        }
                        else if($task->status == 5){
                            $status = '<span class="badge bg-warning">On Hold</span>';
                        }
                        return $status;
                    })
                    ->addColumn('createdBy', function ($task) {
                            return $task->createdBy->name;
                    })
                    ->addColumn('action', function ($task) {
                                return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                            <a type="button" class="btn btn-sm btn-primary text-white mdlEditBtn" style="cursor:pointer" data-coreui-toggle="modal" data-coreui-target="#editModuleModal" title="Edit Module"><i class="bx bxs-edit"></i></a>
                                            <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $task->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                        </div>';
                    })
                    ->rawColumns(['name','document','type','status','createdBy','action'])
                    ->make(true);
            }
            return view('admin.project.project.show.duration.duration-show');
        } catch (\Exception $exception) {
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
            $category = Task::findOrFail($id);
            return view('admin.project.category.edit', compact('category'));
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
            $data                   =       Task::where('id', $id)->first();
            $data->name             =       $request->name;
            $data->description      =       strip_tags($request->description);;
            $data->status           =       $request->status;
            $data->updated_by       =       Auth::user()->id;
            $data->save();

            return redirect()->route('admin.project.category.index')
                ->with('toastr-success', 'Product Category Updated Successfully');
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
                Task::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Product Category Deleted Successfully.',
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
            $Category = Task::findOrFail($id);
            // Check Item Current Status
            if ($Category->status == 1) {
                $Category->status = 0;
            } else {
                $Category->status = 1;
            }

            $Category->save();
            return response()->json([
                'success' => true,
                'message' => 'Product Category Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function taskDetails(Request $request, $id)
    {
        try {

            $project = Projects::findOrFail($id);

            $projectDurations = ProjectDuration::where('duration_type_id', $id)->where('duration_type',1)->latest()->get();
            $projectDuration= $projectDurations->first();
            $projectDurationInitial= $projectDurations->last();
            $module = Projects::where('type',2)
                                ->where('parent_id',$id)
                                ->where('status','!=',5)
                                ->count();

            return view('admin.project.project.show.task.task-show', compact('project', 'projectDuration','projectDurationInitial','module'));

        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}
