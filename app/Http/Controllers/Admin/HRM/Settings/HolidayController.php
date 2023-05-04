<?php

namespace App\Http\Controllers\Admin\HRM\Settings;

use DataTables;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\HRM\HrmNotice;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Models\Employee\Department;
use App\Http\Controllers\Controller;
use App\Models\HRM\Settings\Holiday;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
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
                $holidays = Holiday::latest()->get();
                return DataTables::of($holidays)
                    ->addIndexColumn()
                    ->addColumn('description', function ($holidays) {
                        return Str::limit($holidays->description, 20, $end = '.....');
                    })
                    ->addColumn('status', function ($holidays) {
                        if ($holidays->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $holidays->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $holidays->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($holidays) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"> <a class="btn btn-sm btn-primary text-white " title="Show" style="cursor:pointer"
                        href="' . route('admin.hrm.setting.holiday.show', $holidays->id) . '"><i class="bx bx-show"> </i> </a><a href="' . route('admin.hrm.setting.holiday.edit', $holidays->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $holidays->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'description', 'status'])
                    ->make(true);
            }
            return view('admin.hrm.settings.holiday.index');
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
            return view('admin.hrm.settings.holiday.create');
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
            'title' => 'required',
        ]);

        if($request->type == 1 ){
            $request->validate([
                'single_date' => 'required',
            ]);
        }
        else{
            $request->validate([
                'start_date' => 'required',
                'end_date'   => 'required',
            ]);
        }

        try {
            $holiday = new Holiday();
            $holiday->title       =  $request->title;
            $holiday->type        =  $request->type;
            $holiday->single_date =  $request->single_date;
            $holiday->start_date  =  $request->start_date;
            $holiday->end_date    =  $request->end_date;
            $holiday->status      =  $request->status;
            $holiday->description =  $request->description;
            $holiday->created_by  =  Auth::user()->id;
            $holiday->access_id   =  json_encode(UserRepository::accessId(Auth::id()));

            if ($holiday->save()) {
                return redirect()->route('admin.hrm.setting.holiday.index')->with('message', 'Holiday Created  Successfully');
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
            $holiday = Holiday::findOrFail($id);
            return view('admin.hrm.settings.holiday.edit', compact('holiday'));
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
        // dd($request->all());
        $request->validate([
            'title' => 'required',
        ]);

        if($request->type == 1 ){
            $request->validate([
                'single_date' => 'required',
            ]);
        }
        else{
            $request->validate([
                'start_date' => 'required',
                'end_date'   => 'required',
            ]);
        }

        try {
            $holiday = Holiday::findOrFail($id);
            $holiday->title = $request->title;
            $holiday->type = $request->type;

            if ( $holiday->type == 1) {
                $holiday->start_date = '';
                $holiday->end_date = '';
                $holiday->single_date = $request->single_date;

            } else {
                $holiday->single_date = '';
                $holiday->start_date = $request->start_date;
                $holiday->end_date = $request->end_date;
            }

            $holiday->status = $request->status;
            $holiday->description = $request->description;

            $holiday->updated_by = Auth::user()->id;
            $holiday->update();

            return redirect()->route('admin.hrm.setting.holiday.index')->with('message', 'Holiday Updated Successfully');
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
                Holiday::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Holiday Deleted Successfully.',
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
            $holiday = Holiday::findOrFail($id);
            // Check Item Current Status
            if ($holiday->status == 1) {
                $holiday->status = 0;
            } else {
                $holiday->status = 1;
            }

            $holiday->save();
            return response()->json([
                'success' => true,
                'message' => 'Holiday Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}




