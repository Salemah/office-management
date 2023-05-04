<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Account\BankAccount;
use App\Models\Account\Transaction;
use App\Models\Project\ProjectBudget;
use App\Models\Project\ProjectDuration;
use App\Models\Project\Projects;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class ProjectAccountController extends Controller
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
        $request->validate([
            'amount' => 'required',
        ]);
        try {

                $budget = new ProjectBudget();
                $budget->project_id = $request->project_id;
                $budget->amount = $request->amount;
                $budget->description = $request->description;
                $budget->status = 1;
                $budget->created_by = Auth::user()->id;
                $budget->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $budget->save();

            return redirect()-> route('admin.project.account-budget.view',$request->project_id)->with('message', 'Project Budget Add Successfully');

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
                $projects = ProjectBudget::with('createdBy')->where('project_id',$id)->latest()->get();
                return DataTables::of($projects)
                    ->addIndexColumn()
                    ->addColumn('createdBy', function ($projects) {
                        return $projects->createdBy->name;
                     })
                    ->addColumn('time', function ($projects) {
                        return Carbon::parse($projects->created_at)->format('d M, Y');
                     })
                    ->addColumn('due-amount', function ($projects) {
                       $projectsa = ProjectBudget::where('project_id',$projects->id)->sum('amount');
                       $due = Transaction::where('project_id',$projects->id)->where('status',1)
                            ->where('transaction_purpose',16) ->sum('amount');

                       return $projectsa-$due;
                     })
                    ->addColumn('action', function ($projects) {
                                return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                            <a href="' . route('admin.project.account-budget.edit', $projects->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Show"><i class="bx bxs-edit"></i></a>
                                            <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $projects->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                                        </div>';
                    })
                    ->rawColumns(['due-amount','createdBy','time','action'])
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
            $projectBudget = ProjectBudget::findOrFail($id);
            $project = Projects::find($projectBudget->project_id);
            return view('admin.project.project.show.account.partial.edit',compact('project','projectBudget'));
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
            'amount' => 'required',
        ]);
        try {

                $budget =ProjectBudget::findOrFail($id);
                $budget->project_id = $request->project_id;
                $budget->amount = $request->amount;
                $budget->description = $request->description;
                $budget->status = 1;
                $budget->updated_by = Auth::user()->id;
                $budget->update();

            return redirect()-> route('admin.project.account-budget.view',$request->project_id)->with('message', 'Project Budget Update Successfully');
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
                ProjectBudget::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Project Budget Deleted Successfully.',
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
    public function projectAccounts(Request $request, $id)
    {
        try {
            $cash_account = BankAccount::where('status', 1)->where('type',1)->get();
            $bankAccounts = BankAccount::where('status', 1)->where('type',2)->get();
            // $projectDuration = ProjectDuration::findOrFail($id);
            $project = Projects::findOrFail($id);

            $total_budget =number_format( ProjectBudget::where('project_id',$id)->sum('amount'),2);

            $projectsa = ProjectBudget::where('project_id',$project->id)->sum('amount');

            $due = Transaction::where('project_id',$project->id)->where('status',1)->where('transaction_purpose',16)->sum('amount');

            $total_due = $projectsa-$due;

            return view('admin.project.project.show.account.account-show',compact('project','total_budget','bankAccounts','cash_account','total_due'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

}
