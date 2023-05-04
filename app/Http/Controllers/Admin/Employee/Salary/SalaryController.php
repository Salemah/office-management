<?php

namespace App\Http\Controllers\admin\Employee\Salary;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee\Salary\Salary;
use App\Models\HRM\Settings\Allowance;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SalaryController extends Controller
{

    public function index()
    {
       return "kk";
    }

    public function store(Request $request)
    {
        $request->validate([
            'basic_salary'=>'required',
            'salary_month'=>'required'
        ]);

        DB::beginTransaction();
       try{
        $data = new Salary();
        $data->allowance_id = $request->allowance_id;
        $data->salary_month = $request->salary_month;
        $data->basic_salary = $request->basic_salary;
        $data->home_allowance = $request->home_allowance;
        $data->transport_allowance = $request->transport_allowance;
        $data->medical_allowance = $request->medical_allowance;
        $data->mobile_allowance = $request->mobile_allowance;
        $data->gross_salary = $request->gross_salary;
        $data->employee_id = $request->employee_id;
        $data->description      =     $request->description;
        $data->created_by       =       Auth::user()->id;
        $data->access_id        =       json_encode(UserRepository::accessId(Auth::id()));
        $data->save();

        $LastStatus = Salary::latest()->first();
            $allStatus = Salary::where('id', '!=', $LastStatus->id)->get();
            foreach ($allStatus as $status) {
                if ($status->status == 1) {
                    $status->update([
                        'status' => 0
                    ]);
                }
            }
        $emp_id = $request->employee_id;
        $employee = Employee::where('id',$emp_id)->first();
        $employee->current_salary = $request->basic_salary;
        $employee->gross_salary = $request->gross_salary;
        $employee->update();
        DB::commit();
        return redirect()->back()->with('Successfully Done');
    }catch(\Exception $exception){
        return redirect()->back()->with('error', $exception->getMessage());
    }

    }

    public function show(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $allowances = Allowance::all();
        return view('admin.employee.salary.index', compact('employee', 'allowances'));
    }

    public function edit($id)
    {
        $salary = Salary::find($id);
        return view('admin.employee.salary.partial.edit', compact('salary'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'basic_salary'=>'required',
            'new_salary'=>'required',
        ]);
        try {
            $employee = Employee::where('id', $id)->first();
            $employee->update(['current_salary'=>$request->new_salary,
                                'updated_by'=>Auth::user()->id]);
            return redirect()->back()->with('New salary added');
        }catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

    }

    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $salary = Salary::findOrFail($id);
                $salary->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Salary Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }

    public function salaryList(Request $request, $id){

        try {
           if($request->ajax()){
                $salary = Salary::where('employee_id' , $id)->get();

                return DataTables::of($salary)
                ->addIndexColumn()
                ->setRowAttr([
                    'basic_salary' => function($salary) {
                        return $salary->basic_salary;
                    },
                    'allowance_salary' => function($salary) {
                        return $salary->gross_salary - $salary->basic_salary;
                    },
                    'salarEditId' => function($salary) {
                        return $salary->id;
                    },
                    'gross_salary' => function($salary) {
                        return $salary->gross_salary;
                    },
                    'class' => function() {
                        return 'mdlCreateBtn';
                    },
                    'description' => function($salary) {
                        return $salary->description;
                    },
                ])
                ->addColumn('date', function ($salary) {
                    return Carbon::parse($salary->created_at)->format("d F, Y");
                })
                ->addColumn('basic_salary', function ($salary) {
                    return $salary->basic_salary;
                })
                ->addColumn('gross_salary', function ($salary) {
                    return $salary->gross_salary;
                })
                ->addColumn('status', function ($salary) {
                    if ($salary->status == 1) {
                        $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $salary->id . ')">Active</button>';
                    } else {
                        $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $salary->id . ')">Inactive</button>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($salary) {
                    return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                    <span class="btn btn-sm btn-primary text-white" style="cursor:pointer" onclick="getSelectedSalaryData(' . $salary->id . ',1' . ')"  title="Edit" data-coreui-toggle="modal" data-coreui-target="#salaryEditSingleModal"><i class="bx bxs-edit"></i></span>
                    <span class="btn btn-sm btn-success text-white" style="cursor:pointer" onclick="getSelectedSalaryData(' . $salary->id . ',2' . ')"  title="Show" data-coreui-toggle="modal" data-coreui-target="#salarySingleModal"><i class="bx bxs-show"></i></span>
                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="salaryDeleteConfirm(' .$salary->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                </div>';
                })
                ->rawColumns(['date','basic_salary','status','action'])
                ->make(true);
           }
        return view('admin.employee.salary.partial.salary-list');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
        
    }

    public function statusUpdate(Request $request, $id)
    {
        try {
            $salary = Salary::findOrFail($id);
            // Check Item Current Status
            if ($salary->status == 1) {
                $salary->status = 0;
                $allStatus = Salary::where('id', '!=', $salary->id)->get();
                foreach ($allStatus as $status) {
                    if ($status->status == 1) {
                        $status->update([
                            'status' => 0
                        ]);
                    }
                }
            } else {
                $salary->status = 1;
                $allStatus = Salary::where('id', '!=', $salary->id)->get();
                foreach ($allStatus as $status) {
                    if ($status->status == 1) {
                        $status->update([
                            'status' => 0
                        ]);
                    }
                }
            }

            $salary->save();
            return response()->json([
                'success' => true,
                'message' => 'Allowance Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function salaryShow(Request $request)
    {
        try {
            $data = Salary::findOrFail($request->id);
            $allowances = Allowance::all();
            if ($request->type == 1) {
                $html = view('admin.employee.salary.partial.edit', compact('data','allowances'))->render();

            }else{
                $html = view('admin.employee.salary.partial.show', compact('data'))->render();
            }

            return response()->json([
                'type' => $request->type,
                'data' => $html,
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function salaryUpdate(Request $request, $id){
        $request->validate([
            'basic_salary'=>'required',
            'salary_month'=>'required'
        ]);

        DB::beginTransaction();
       try{
        $data = Salary::findOrFail($request->salary_id);
        $data->salary_month = $request->salary_month;
        $data->basic_salary = $request->basic_salary;
        $data->home_allowance = $request->home_allowance;
        $data->transport_allowance = $request->transport_allowance;
        $data->medical_allowance = $request->medical_allowance;
        $data->mobile_allowance = $request->mobile_allowance;
        $data->gross_salary = $request->gross_salary;
        $data->description      =     $request->description;
        $data->created_by       =       Auth::user()->id;
        $data->access_id        =       json_encode(UserRepository::accessId(Auth::id()));
        $data->update();
        $LastStatus = Salary::latest()->first();
            $allStatus = Salary::where('id', '!=', $LastStatus->id)->get();
            foreach ($allStatus as $status) {
                if ($status->status == 1) {
                    $status->update([
                        'status' => 0
                    ]);
                }
            }
        $employee = Employee::where('id', $id)->first();
        $employee->current_salary = $request->basic_salary;
        $employee->gross_salary = $request->gross_salary;
        $employee->update();
        DB::commit();
        return redirect()->back()->with('Successfully Done');
    }catch(\Exception $exception){
        return redirect()->back()->with('error', $exception->getMessage());
    }

    }

}
