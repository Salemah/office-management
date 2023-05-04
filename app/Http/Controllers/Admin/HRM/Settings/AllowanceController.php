<?php

namespace App\Http\Controllers\Admin\HRM\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\HRM\Settings\Allowance;
use Yajra\DataTables\Facades\DataTables;

class AllowanceController extends Controller
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
                $Allowance = Allowance::latest()->get();
                return DataTables::of($Allowance)
                    ->addIndexColumn()
                    ->addColumn('allowance_name', function ($Allowance) {
                        return $Allowance->allowance_name;
                    })
                    ->addColumn('home_allowance', function ($Allowance) {
                        $percentgeIcon = '';
                        if ($Allowance->is_home_allowance_percentage == 1) {
                            $percentgeIcon = "%";
                        }
                        return $Allowance->home_allowance . $percentgeIcon;
                    })
                    ->addColumn('status', function ($Allowance) {
                        if ($Allowance->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $Allowance->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $Allowance->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($Allowance) {

                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <span class="btn btn-sm btn-primary text-white" style="cursor:pointer" onclick="getSelectedAllowanceData(' . $Allowance->id . ',1' . ')"  title="Edit" data-coreui-toggle="modal" data-coreui-target="#allowanceEditSingleModal"><i class="bx bxs-edit"></i></span>
                                    <span class="btn btn-sm btn-success text-white" style="cursor:pointer" onclick="getSelectedAllowanceData(' . $Allowance->id . ',2' . ')"  title="Show" data-coreui-toggle="modal" data-coreui-target="#allowanceSingleModal"><i class="bx bxs-show"></i></span>
                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="AllowanceDeleteConfirm(' . $Allowance->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['home_allowance', 'status', 'action'])
                    ->make(true);
            }
            return view('admin.hrm.settings.allowance.index');
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
       return view('admin.hrm.settings.allowance.create');
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
            'home_allowance' => 'required',
        ]);

        try {
            $data = new Allowance();
            $data->allowance_name = $request->allowance_name;
            $data->home_allowance = $request->home_allowance;
            $data->is_home_allowance_percentage = $request->is_home_allowance_percentage;
            $data->transport_allowance = $request->transport_allowance;
            $data->medical_allowance = $request->medical_allowance;
            $data->mobile_allowance = $request->mobile_allowance;
            $data->description = $request->description;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            $LastStatus = Allowance::latest()->first();
            $allStatus = Allowance::where('id', '!=', $LastStatus->id)->get();
            foreach ($allStatus as $status) {
                if ($status->status == 1) {
                    $status->update([
                        'status' => 0
                    ]);
                }
            }

            return redirect()->route('admin.hrm.allowance.index')->with('Succesfully Done');
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
        //
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
        //
    }
    public function allowanceUpdate(Request $request)
    {
        $request->validate([
            'home_allowance' => 'required',
        ]);
        try {
            $data = Allowance::findOrFail($request->allowance_id);
            $data->allowance_name = $request->allowance_name;
            $data->home_allowance = $request->home_allowance;
            $data->is_home_allowance_percentage = $request->is_home_allowance_percentage;
            $data->transport_allowance = $request->transport_allowance;
            $data->medical_allowance = $request->medical_allowance;
            $data->mobile_allowance = $request->mobile_allowance;
            $data->description = $request->description;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->update();
            return redirect()->route('admin.hrm.allowance.index')->with('Succesfully Done');
            // return response()->json([
            //     "success"=>200,
            //     "data"=>$data
            // ]);
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
            try {
                $Allowance = Allowance::findOrFail($id);
                $Allowance->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Allowance Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
        public function statusUpdate(Request $request, $id)
    {
        try {
            $Allowance = Allowance::findOrFail($id);
            // Check Item Current Status
            if ($Allowance->status == 1) {
                $Allowance->status = 0;
                $allStatus = Allowance::where('id', '!=', $Allowance->id)->get();
                foreach ($allStatus as $status) {
                    if ($status->status == 1) {
                        $status->update([
                            'status' => 0
                        ]);
                    }
                }
            } else {
                $Allowance->status = 1;
                $allStatus = Allowance::where('id', '!=', $Allowance->id)->get();
                foreach ($allStatus as $status) {
                    if ($status->status == 1) {
                        $status->update([
                            'status' => 0
                        ]);
                    }
                }
            }

            $Allowance->save();
            return response()->json([
                'success' => true,
                'message' => 'Allowance Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    
    public function allowanceShow(Request $request)
    {

        try {
            $allowance = Allowance::findOrFail($request->id);
            if ($request->type == 1) {
                $html = view('admin.hrm.settings.allowance.partial.edit', compact('allowance'))->render();

            }else{
                $html = view('admin.hrm.settings.allowance.partial.show', compact('allowance'))->render();
            }

            return response()->json([
                'type' => $request->type,
                'data' => $html,
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    
}
