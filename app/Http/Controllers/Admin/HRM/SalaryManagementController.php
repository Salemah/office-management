<?php

namespace App\Http\Controllers\Admin\HRM;

use App\Models\HRM\Salary;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;
use DataTables;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\DateTime;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class SalaryManagementController extends Controller
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
        $Salary = Salary::latest()->get();
        return DataTables::of($Salary)
        ->addIndexColumn()
        ->addColumn('year', function ($Salary) {
            return $Salary->year;
        })
        ->addColumn('month', function ($Salary) {
            return $Salary->month;
        })
        ->addColumn('houserent', function ($Salary) {
            return $Salary->houserent;
        })
        ->addColumn('action', function ($Salary) {
            return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"><a class="btn btn-sm btn-info text-white " title="Show" style="cursor:pointer"href="' . route('admin.hrm.salary.show', $Salary->id) . '"><i class="bx bx-show"></i> </a>
            <a class="btn btn-sm btn-success text-white " title="Edit" style="cursor:pointer"
            href="' . route('admin.hrm.salary.edit', $Salary->id) . '"><i class="bx bx-edit"> </i> </a>
            <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $Salary->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                </div>';
        })
        ->rawColumns(['year','month', 'houserent','action'])
        ->make(true);
    }
        return view('admin.hrm.salary.index');
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
        $years = DateTime::getYear();
        $months = DateTime::allMonths();
       return view('admin.hrm.salary.create', compact('years','months'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $request->validate([
        'year'=>'required',
        'month'=>'required',
        'houserent'=>'required',
       ]);
try {
    $data = new Salary();
    $data->year = $request->year;
    $data->month = $request->month;
    $data->houserent = $request->houserent;
    $data->medical = $request->medical;
    $data->transport_allowance = $request->transport_allowance;
    $data->mobile_allowance = $request->mobile_allowance;
    $data->leave_allowance = $request->leave_allowance;
    $data->description = $request->description;
    $data->created_by       =       Auth::user()->id;
    $data->access_id        =       json_encode(UserRepository::accessId(Auth::id()));

    $data->save();
    return redirect()->route('admin.hrm.salary.index')->with('Successfully added');
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $years = DateTime::getYear();
        $months = DateTime::allMonths();
        $salary = Salary::find($id);
        return view('admin.hrm.salary.edit', compact('salary','years','months'));
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
        $request->validate([
            'year'=>'required',
            'month'=>'required',
            'houserent'=>'required',
           ]);
           try {
            $data = Salary::where('id', $id)->first();
            $data->year = $request->year;
            $data->month = $request->month;
            $data->houserent = $request->houserent;
            $data->medical = $request->medical;
            $data->transport_allowance = $request->transport_allowance;
            $data->mobile_allowance = $request->mobile_allowance;
            $data->leave_allowance = $request->leave_allowance;
            $data->description = $request->description;
            $data->created_by       =       Auth::user()->id;
            $data->access_id        =       json_encode(UserRepository::accessId(Auth::id()));
        
            $data->save();
            return redirect()->route('admin.hrm.salary.index')->with('Successfully updated');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
            }
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
       try{
        $data = Salary::where('id', $id)->first();
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Expense Deleted Successfully.',
        ]);
       }catch(\Exception $exception){
            return redirect()->back()->with('error', $exception->getMessage());
       }
    }
    }
}
