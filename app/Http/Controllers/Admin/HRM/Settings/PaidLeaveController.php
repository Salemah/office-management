<?php

namespace App\Http\Controllers\Admin\HRM\Settings;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\HRM\Settings\PaidLeave;

class PaidLeaveController extends Controller
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
                $data = PaidLeave::latest()->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('days', function ($data) {
                        return $data->days;
                    })
                    ->addColumn('paid_per_day', function ($data) {
                        return $data->paid_per_day;
                    })
                    ->addColumn('total_amount', function ($data) {
                        return $data->total_amount;
                    })
                    ->addColumn('status', function ($data) {
                        if ($data->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $data->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $data->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($data) {

                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <span class="btn btn-sm btn-primary text-white" style="cursor:pointer" onclick="getSelectedLeaveData(' . $data->id . ',1' . ')"  title="Edit" data-coreui-toggle="modal" data-coreui-target="#leaveEditSingleModal"><i class="bx bxs-edit"></i></span>
                                    <span class="btn btn-sm btn-success text-white" style="cursor:pointer" onclick="getSelectedLeaveData(' . $data->id . ',2' . ')"  title="Show" data-coreui-toggle="modal" data-coreui-target="#leaveSingleModal"><i class="bx bxs-show"></i></span>
                                    <a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $data->id . ')" title="Delete"> <i class="bx bxs-trash"></i></a>
                                </div>';
                    })
                    ->rawColumns(['days', 'paid_per_day','total_amount','status','action'])
                    ->make(true);
            }
            return view('admin.hrm.settings.paid_leave.index');
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
       return view('admin.hrm.settings.paid_leave.create');
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
            'days'=>'required|integer',
            'paid_per_day'=>'required|integer',
        ]);

        try{
            $data = new PaidLeave();
            $data->days = $request->days;
            $data->paid_per_day = $request->paid_per_day;
            $data->total_amount = $request->total_amount;
            $data->description = $request->description;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();
    
            $LastStatus = PaidLeave::latest()->first();
            $allStatus = PaidLeave::where('id', '!=', $LastStatus->id)->get();
            foreach ($allStatus as $status) {
                if ($status->status == 1) {
                    $status->update([
                        'status' => 0
                    ]);
                }
            }
            return redirect()->route('admin.hrm.paid-leave.index')->with('Succesfully Done');
        }catch (\Exception $exception) {
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
    public function paidLeaveUpdate(Request $request){
        $request->validate([
            'days'=>'required|integer',
            'paid_per_day'=>'required|integer',
        ]);

        try{
            $data = PaidLeave::findOrFail($request->paid_leave_id);
            $data->days = $request->days;
            $data->paid_per_day = $request->paid_per_day;
            $data->total_amount = $request->total_amount;
            $data->description = $request->description;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->update();
            return redirect()->route('admin.hrm.paid-leave.index')->with('Succesfully Done');
        }catch (\Exception $exception) {
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
                $data = PaidLeave::findOrFail($id);
                $data->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Paid Leave Deleted Successfully.',
                ]);
            } catch (\Exception $exception) {
                return redirect()->back()->with('error', $exception->getMessage());
            }
        }
    }
    public function statusUpdate(Request $request, $id)
    {
        try {
            $data = PaidLeave::findOrFail($id);
            // Check Item Current Status
            if ($data->status == 1) {
                $data->status = 0;
                $allStatus = PaidLeave::where('id', '!=', $data->id)->get();
                foreach ($allStatus as $status) {
                    if ($status->status == 1) {
                        $status->update([
                            'status' => 0
                        ]);
                    }
                }
            } else {
                $data->status = 1;
                $allStatus = PaidLeave::where('id', '!=', $data->id)->get();
                foreach ($allStatus as $status) {
                    if ($status->status == 1) {
                        $status->update([
                            'status' => 0
                        ]);
                    }
                }
            }

            $data->save();
            return response()->json([
                'success' => true,
                'message' => 'Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
    public function leaveShow(Request $request)
    {

        try {
            $data = PaidLeave::findOrFail($request->id);
            if ($request->type == 1) {
                $html = view('admin.hrm.settings.paid_leave.partial.edit', compact('data'))->render();

            }else{
                $html = view('admin.hrm.settings.paid_leave.partial.show', compact('data'))->render();
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
