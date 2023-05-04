<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\ProjectDuration;
use App\Models\Project\ProjectModule;
use App\Models\Project\Projects;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectDurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
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
            $projects = Projects::count() + 1;
            return view('admin.project.project.create', compact('projects'));
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
            'start_date' => 'required',
            'end_date' => 'required',
            'total_day' => 'required',
            'estimated_hour_day' => 'required',
            'estimated_hour' => 'required',
            'final_hour' => 'required',
        ]);
        if ($request->adjustment_hour) {
            $request->validate([
                'adjustment_type' => 'required',
                'adjustment_hour' => 'required',
            ]);
        }
        try {

            $data = new ProjectDuration();
            if($request->module_id){
                $data->duration_type  = 2;// Module Duration Type = 2
                $data->duration_type_id = $request->module_id;
                $data->project_id = $request->project_id;
             }else{
                $data->duration_type  = 1;// Project Duration Type = 1
                 $data->duration_type_id = $request->project_id;
              }
            // $data->duration_type_id = $duration_type_id;
            //$data->duration_type  = $duration_type;
            $data->project_id = $request->project_id;
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

            return redirect()->route('admin.project.duration', $request->project_id)
                ->with('message', 'Project Duration AddSuccessfully');
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
                $project = ProjectDuration::with('project')
                        ->where('project_id', $id)
                        ->where('module_duration_id',null)
                        //->where('on_hold',0)
                        ->where('module_duration_id',null)
                        //->orWhere('duration_type_id', $id)
                        ->latest()
                        ->get();
                return DataTables::of($project)
                    ->addIndexColumn()
                    ->setRowAttr([
                        'start_date' => function($project) {
                            return $project->start_date;
                        },
                        'project_duration_id' => function($project) {
                            return $project->id;
                        },
                        'project_title' => function($project) {
                            return $project->project->project_title;
                        },
                        'project_type' => function($project) {
                            if($project->project->project_type == 1){
                                return 'Own Project';
                            }
                            else if($project->project->project_type == 2){
                                return 'Client Project';
                            }
                            else if($project->project->project_type == 3){
                                return 'Public Project';
                            };
                        },
                        'project_priority' => function($project) {
                            if($project->project->project_priority == 1){
                                return 'First';
                            }
                            else if($project->project->project_priority == 2){
                                return 'Second';
                            }
                            else if($project->project->project_priority == 3){
                                return 'Third';
                            };
                        },

                        'project-description' => function($project) {
                            return json_encode(['p'=>$project->project->description]);
                        },
                        'class' => function() {
                            return 'mdlCreateBtn';
                        },
                        'end_date' => function($project) {
                            return $project->end_date;
                        },
                    ])
                    ->addColumn('type', function ($project) {
                            if($project->duration_type == 1)
                            {
                                $durationType = '<span class="badge bg-primary">Project</span>';
                            }
                            else if($project->duration_type == 2){
                                $durationType = '<span class="badge bg-info">Module</span>';;
                            }
                            else if($project->duration_type == 3){
                                $durationType = '<span class="badge bg-success">Task</span>';
                            }
                            return  $durationType;
                    })
                    ->addColumn('start_date', function ($project) {
                            return Carbon::parse($project->start_date)->format('d M, Y');
                    })
                    ->addColumn('end_date', function ($project) {
                            return Carbon::parse($project->end_date)->format('d M, Y');
                    })
                    ->addColumn('total_day', function ($project) {
                            return $project->final_day .'d';
                    })
                    ->addColumn('status', function ($project) {
                        if($project->status == 1){
                            $status = '<span class="badge bg-info">Up Coming</span>';
                        }
                        else if($project->status == 2){
                            $status = '<span class="badge bg-success">On Going</span>';
                        }
                        else if($project->status == 3){
                            $status = '<span class="badge bg-primary">Complete</span>';
                        }
                        else if($project->status == 4){
                            $status = '<span class="badge bg-danger">Cancel</span>';
                        }
                        else if($project->status == 5){
                            $status = '<span class="badge bg-warning">On Hold</span>';
                        }
                        return $status;
                    })
                    ->addColumn('total_hour', function ($project) {
                            return $project->final_hour .'h';
                    })
                    ->addColumn('action', function ($project) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                <a data-coreui-toggle="modal"
                                data-coreui-target="#durationViewModal"  class="btn btn-sm btn-primary text-white" style="cursor:pointer" title="Show" ><i class="bx bx-show"></i></a>
                                  <a href="' . route('admin.project.duration.edit', $project->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Show"><i class="bx bxs-edit"></i></a>
                                  <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $project->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['type','status','start_date','end_date','total_day','total_hour','action'])
                    ->make(true);
            }
            return view('admin.project.project.show.duration.duration-show', compact('projects'));
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
    public function edit($id)
    {
        try {

            $project = ProjectDuration::findOrFail($id);
            $projects = ProjectDuration::where('duration_type_id', $project->duration_type_id)->where('duration_type',1)->get();
            $project_duration = count($projects);
            $projectInitial = '';
            if($projects != null){
                $projectInitial = $projects->first();
            }
            return view('admin.project.project.show.duration.edit', compact('project','projectInitial','project_duration'));
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
            'start_date' => 'required',
            'end_date' => 'required',
            'total_day' => 'required',
            'estimated_hour_day' => 'required',
            'estimated_hour' => 'required',
            'final_hour' => 'required',
        ]);
        if ($request->adjustment_hour) {
            $request->validate([
                'adjustment_type' => 'required',
                'adjustment_hour' => 'required',
            ]);
        }
        try {
            $data = ProjectDuration::findOrFail($id);
            $data->duration_type_id = $request->project_id;
            $data->duration_type  = 1; // Project Duration Type = 1
            $data->start_date = $request->start_date;
            $data->end_date = $request->end_date;
            $data->estimate_day = $request->total_day;
            $data->vacation_day = $request->vacation_day;
            $data->final_day = $request->final_day;
            $data->estimate_hour_per_day = $request->estimated_hour_day;
            $data->estimate_hour = $request->estimated_hour;
            $data->final_hour = $request->final_hour;
            $data->adjustment_type = $request->adjustment_type;
            $data->adjustment_hour = $request->adjustment_hour;
            $data->description = $request->description;

            $data->updated_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));

            $data->update();

            return redirect()->route('admin.project.duration', $request->project_id)->with('message', 'Project Duration AddSuccessfully');
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
                ProjectDuration::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Product Duration Deleted Successfully.',
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
    public function projectDuration(Request $request, $id)
    {
        try {
            $project = Projects::findOrFail($id);

            $projects = ProjectDuration::where('duration_type_id', $id)->where('duration_type',1)->latest()->get();

            // $projectDurations = ProjectDuration::where('duration_type_id', $id)->where('duration_type',1)->latest()->get();
            $projectDurationEnd= $projects->first();
            $projectDurationInitial= $projects->last();

            // $projectInitial = '';

            if(isset($projects ) != null){
                $projectStartDate = $projects->first();
                $projectInitial = $projects->first();
                // $projectInitial = $projects[count($projects)-1];
            }

            $project_duration = count($projects);


            return view('admin.project.project.show.duration.duration-show', compact('project','project_duration', 'projects',  'projectInitial','projectStartDate','projectDurationEnd','projectDurationInitial'));

        }
        catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function durationDetails(Request $request, $id)
    {
        try {
            $projectDuration = ProjectDuration::where('id', $id)->with('project')       ->first();
            $modules = json_decode($projectDuration->module_name);
            $modulesStare_date = json_decode($projectDuration->module_start_date);
            $modulesEnd_date = json_decode($projectDuration->module_end_date);

            return view('admin.project.project.show.duration.duration-details', compact('projectDuration', 'modules', 'modulesStare_date', 'modulesEnd_date'));

        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function durationHoldList(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                $project = ProjectDuration::with('project')
                        ->where('project_id', $id)
                        ->where('on_hold',1)
                        ->where('end_date', null)
                       //->orWhere('duration_type_id', $id)
                    // ->where('duration_type',1)
                        ->latest()
                        ->get();
                return DataTables::of($project)
                    ->addIndexColumn()
                    // ->setRowAttr([
                    //     'start_date' => function($project) {
                    //         return $project->start_date;
                    //     },
                    //     'project_duration_id' => function($project) {
                    //         return $project->id;
                    //     },
                    //     'project_title' => function($project) {
                    //         return $project->project->project_title;
                    //     },
                    //     'project_type' => function($project) {
                    //         if($project->project->project_type == 1){
                    //             return 'Own Project';
                    //         }
                    //         else if($project->project->project_type == 2){
                    //             return 'Client Project';
                    //         }
                    //         else if($project->project->project_type == 3){
                    //             return 'Public Project';
                    //         };
                    //     },
                    //     'project_priority' => function($project) {
                    //         if($project->project->project_priority == 1){
                    //             return 'First';
                    //         }
                    //         else if($project->project->project_priority == 2){
                    //             return 'Second';
                    //         }
                    //         else if($project->project->project_priority == 3){
                    //             return 'Third';
                    //         };
                    //     },

                    //     'project-description' => function($project) {
                    //         return json_encode(['p'=>$project->project->description]);
                    //     },
                    //     'class' => function() {
                    //         return 'mdlCreateBtn';
                    //     },
                    //     'end_date' => function($project) {
                    //         return $project->end_date;
                    //     },
                    // ])
                    ->addColumn('type', function ($project) {
                            if($project->duration_type == 1)
                            {
                                $durationType = '<span class="badge bg-primary">Project</span>';
                            }
                            else if($project->duration_type == 2){
                                $durationType = '<span class="badge bg-info">Module</span>';;
                            }
                            else if($project->duration_type == 3){
                                $durationType = '<span class="badge bg-success">Task</span>';
                            }
                            return  $durationType;
                    })
                    ->addColumn('start_date', function ($project) {
                            return Carbon::parse($project->start_date)->format('d M, Y');
                    })
                    ->addColumn('end_date', function ($project) {
                        if($project->end_date){
                            return Carbon::parse($project->end_date)->format('d M, Y');
                        }
                        else{
                            return ' -- ';
                        }


                    })
                    ->addColumn('total_day', function ($project) {
                            return $project->final_day .'d';
                    })
                    ->addColumn('total_hour', function ($project) {
                            return $project->final_hour .'h';
                    })
                    ->addColumn('status', function ($project) {
                        if($project->status == 1){
                            $status = '<span class="badge bg-info">Up Coming</span>';
                        }
                        else if($project->status == 2){
                            $status = '<span class="badge bg-success">On Going</span>';
                        }
                        else if($project->status == 3){
                            $status = '<span class="badge bg-primary">Complete</span>';
                        }
                        else if($project->status == 4){
                            $status = '<span class="badge bg-danger">Cancel</span>';
                        }
                        else if($project->status == 5){
                            $status = '<span class="badge bg-warning">On Hold</span>';
                        }
                        return $status;
                    })
                    ->rawColumns(['type','status','start_date','end_date','total_day','total_hour'])
                    ->make(true);
            }
            return view('admin.project.project.show.duration.duration-show', compact('projects'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

}
