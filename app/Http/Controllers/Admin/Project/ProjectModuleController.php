<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\ProjectDuration;
use App\Models\Project\Projects;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectModuleController extends Controller
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
    public function create(Request $request,$id)
    {
        try {
            $project = Projects::findOrFail($id);
            return view('admin.project.project.show.duration.module.module-show', compact('project'));
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
       // \dd($request->all());
        $request->validate([
            'module_name' => 'required',
            'status' => 'required',
        ]);
        try {
                $module = new Projects();
                $module->parent_id = $request->project_id;
               $module->project_title = $request->module_name;
                $module->type =2; // Project Module Type = 1
                $module->description = $request->description;
                $module->status = $request->status;
                $module->created_by = Auth::user()->id;
                $module->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $module ->save();
            // if($request->module_start_date){
            //     $data = new ProjectDuration();
            //     $module =Projects::findOrFail($request->module_name);
            //     $data->duration_type_id =  $module->id;
            //     $data->project_id = $request->project_id;
            //     $data->duration_type  = 2; // Project Module Type = 1
            //     $data->name  = $request->module_name;
            //     $data->start_date = $request->module_start_date;
            //     $data->end_date = $request->module_end_date;
            //     $data->estimate_day = $request->module_estimate_day;
            //     $data->estimate_hour_per_day = $request->estimated_hour_day;
            //     $data->estimate_hour = $request->module_total_hour;
            //     $data->final_hour = $request->final_hour;
            //     $data->on_hold = 0;
            //     $data->vacation_day = $request->module_vacation_day;
            //     $data->final_day = $request->module_final_day;
            //     $data->adjustment_type = $request->adjustment_type;
            //     $data->adjustment_hour = $request->adjustment_hour;
            //     $data->status = $request->status;
            //     $data->created_by = Auth::user()->id;
            //     $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            //     $data->save();
            // }
            return redirect()-> route('admin.project.module.add',$request->project_id)->with('message', 'Project Module Add Successfully');


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
                $projects = Projects::with('createdBy')
                            ->where('parent_id',$id)
                            ->latest()
                            ->get();
                return DataTables::of($projects)
                    ->addIndexColumn()
                    ->setRowAttr([
                        'module_id' => function($projects) {
                            return $projects->id;
                        },
                        'class' => function() {
                            return 'mdlBtn';
                        },
                    ])
                    ->addColumn('module_start_date', function ($projects) {
                        return Carbon::parse($projects->start_date)->format('d M, Y');
                    })
                    ->addColumn('module_end_date', function ($projects) {
                            return Carbon::parse($projects->end_date)->format('d M, Y');
                    })
                    ->addColumn('final_day', function ($projects) {
                            return $projects->final_day .'d';
                    })
                    ->addColumn('module_estimate_hour', function ($projects) {
                            return $projects->estimate_hour .'h';
                    })
                    ->addColumn('module_final_hour', function ($projects) {
                            return $projects->final_hour .'h';
                    })
                    ->addColumn('status', function ($projects) {
                        if($projects->status == 1){
                            $status = '<span class="badge bg-info">Up Coming</span>';
                        }
                        else if($projects->status == 2){
                            $status = '<span class="badge bg-success">On Going</span>';
                        }
                        else if($projects->status == 3){
                            $status = '<span class="badge bg-primary">Complete</span>';
                        }
                        else if($projects->status == 4){
                            $status = '<span class="badge bg-danger">Cancel</span>';
                        }
                        else if($projects->status == 5){
                            $status = '<span class="badge bg-warning">On Hold</span>';
                        }
                        return $status;
                    })
                    ->addColumn('createdBy', function ($projects) {
                            return $projects->createdBy->name;
                    })
                    ->addColumn('action', function ($projects) {
                        if($projects->status  == 5){
                                return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                            <a type="button" class="btn btn-sm btn-primary text-white mdlEditBtn" style="cursor:pointer" data-coreui-toggle="modal" data-coreui-target="#editModuleModal" title="Edit Module"><i class="bx bxs-edit"></i></a>
                                            <a type="button" href="'.route("admin.project.module.unhold",$projects->id).'" class="btn btn-sm btn-success text-white " style="cursor:pointer"  title="Un-Hold"><i class="bx bxs-hourglass-top text-primary"></i></a>
                                            <a  onclick="getSelectedLeaveData(' . $projects->id . ')" data-coreui-toggle="modal" data-coreui-target="#exampleModal"  class="btn btn-sm btn-success text-white mdlBtn" style="cursor:pointer"  title="Hold-List"><i class="bx bx-list-ul text-primary"></i></a>
                                            <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $projects->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                        </div>';
                        }
                        else{
                            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                        <a type="button" class="btn btn-sm btn-primary text-white mdlEditBtn" style="cursor:pointer"data-coreui-toggle="modal" data-coreui-target="#editModuleModal" title="Edit Module"><i class="bx bxs-edit"></i></a>
                                        <a type="button" onclick="showHoldConfirm(' . $projects->id . ')"  class="btn btn-sm btn-warning text-white " style="cursor:pointer"  title="Hold"><i class="bx bxs-hourglass-bottom text-primary"></i></a>
                                        <a onclick="getSelectedLeaveData(' . $projects->id .')"   data-coreui-toggle="modal" data-coreui-target="#exampleModal" class="btn btn-sm btn-success text-white " style="cursor:pointer"  title="Hold-List"><i class="bx bx-list-ul text-primary"></i></a>
                                        <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $projects->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                    </div>';
                        }

                    })
                    // href="'.route("admin.project.module.hold",$projects->id).'"
                    ->rawColumns(['status','createdBy','action'])
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {

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
        try {
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function projectUpdate(Request $request)
    {
        $request->validate([
            'module_name' => 'required',
            'project_id' => 'required',
            'project_duration_id' => 'required',
            'module_start_date' => 'required',
            'module_end_date' => 'required',
            'estimated_hour_day' => 'required',
            'module_total_day' => 'required',
            'module_total_hour' => 'required',
            'final_hour' => 'required',
            'status' => 'required',
        ]);

        try {
            $data = ProjectDuration::findOrFail($request->edit_project_module_id);
            $data->duration_type_id = $request->project_id;
            $data->duration_type  = 2; // Project Module Type = 1
            $data->name  = $request->module_name;
            $data->start_date = $request->module_start_date;
            $data->end_date = $request->module_end_date;
            $data->estimate_day = $request->module_estimate_day;
            $data->estimate_hour_per_day = $request->estimated_hour_day;
            $data->estimate_hour = $request->module_total_hour;
            $data->	final_hour = $request->final_hour;
            $data->adjustment_type = $request->adjustment_type;
            $data->adjustment_hour = $request->adjustment_hour;
            $data->vacation_day = $request->edit_module_vacation_day;
            $data->final_day = $request->edit_module_final_day;
            $data->status = $request->status;
            $data->updated_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            return redirect()->route('admin.project.duration', $request->project_id)
                ->with('message', 'Project Module Update Successfully');
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
                    'message' => 'Product Module Deleted Successfully.',
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
    public function projectModule(Request $request, $id)
    {
        try {
            // $projectDuration = ProjectDuration::findOrFail($id);
            // $project = Projects::where('id',$projectDuration->project_id)->first();
            // return view('admin.project.project.show.duration.module.module-show');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function moduleHold(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $projectDurations = ProjectDuration::where('duration_type_id',$id)
                                    ->where('duration_type',2)
                                    ->get();

                if(count($projectDurations) != null){
                    foreach($projectDurations as $durations){
                        $duration = ProjectDuration::findOrFail($durations->id);
                        $duration->on_hold = 1;
                        $duration->status= 5;
                        $duration->update();
                    }

                    $project= Projects::findOrFail($id);

                    $project->status= 5;
                    $project->update();
                    $projectDuration = $projectDurations->first();

                    $data = new ProjectDuration();
                    $data->duration_type_id =$id;
                    $data->project_id = $project->parent_id;

                    $data->duration_type  = 2; // Project Module Type = 1
                    $data->module_duration_id  = $projectDuration->id; // Duration Id
                    $data->start_date = Carbon::now();
                    $data->estimate_day = $projectDuration->estimate_day;
                    $data->estimate_hour_per_day = $projectDuration->estimate_hour_per_day;
                    $data->estimate_hour = $projectDuration->estimate_hour;
                    $data->final_hour = $projectDuration->final_hour;
                    $data->vacation_day = $projectDuration->vacation_day ;
                    $data->final_day = $projectDuration->final_day;

                    $data->adjustment_type = $projectDuration->adjustment_type;
                    $data->adjustment_hour = $projectDuration->adjustment_hour;
                    $data->status = 5;
                    $data->on_hold = 1;
                    $data->created_by = Auth::user()->id;
                    $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
                    $data->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Module Hold Successfully.',
                    ]);

                    // return redirect()->route('admin.project.duration', $project->parent_id)
                    //     ->with('message', 'Module Hold Successfully');
                }
                else{
                    return response()->json([
                        'error' => true,
                        'message' => 'Module No Duration.',
                    ]);
                    //return redirect()->back()->with('error', 'Module No Duration');
                }


            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }

    }
    public function moduleUnHold(Request $request, $id)
    {
        try {
            $projectDuration = ProjectDuration::where('duration_type_id',$id)
                                ->where('duration_type',2)
                                ->get();

            foreach($projectDuration as $durations){
                $duration = ProjectDuration::findOrFail($durations->id);
                $duration->on_hold = 0;
                $duration->status= 2;

                if($duration->module_duration_id != null){
                    $duration->end_date = Carbon::now();
                    $duration->update();
                    // $duration->delete();
                }
                else{
                    $duration->update();
                }
            }

            $project= Projects::findOrFail($id);
            $project->status= 2;
            $project->update();

            return redirect()->route('admin.project.duration', $project->parent_id)
                ->with('message', 'Module Hold Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function moduleSearch(Request $request)
    {
        $result = Projects::query()
            ->where('type',2)
            ->where('parent_id',$request->project)
            ->where('status','!=',5)
            ->where('project_title', 'LIKE', "%{$request->search}%")
            ->get(['project_title', 'id']);
        return $result;
    }
    public function holdList(Request $request,$id)
    {
        try {
                $data = ProjectDuration::with('project')
                        ->where('duration_type_id',$id)
                         ->where('duration_type',2)
                         ->where('module_duration_id', $id)
                         ->get();
                         $module = '';
                         if (count($data)> 0) {
                            $module = ProjectDuration::findOrFail($data->first()->duration_type_id);
                         }
                $html = view('admin.project.project.show.duration.module.hold-history', compact('data','module'))->render();

            return response()->json([
                'type' => $request->type,
                'data' => $html,
            ]);

        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

}
