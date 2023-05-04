<?php

namespace App\Http\Controllers\Admin\HRM\Settings;

use DataTables;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\HRM\Settings\Weekend;
use App\Repositories\Admin\DateTime;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class WeekendController extends Controller
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
                $weekends = Weekend::latest()->get();
                return DataTables::of($weekends)
                    ->addIndexColumn()

                    ->addColumn('weekend_name', function ($weekends){
                        $weekend = '';
                        if($weekends->day){
                            $weekendIds = json_decode($weekends->day);
                            foreach ($weekendIds as $weekendId){
                                $weekend .= DateTime::getDay($weekendId).", ";
                            }
                            $weekend = substr($weekend, 0, -2);
                            return $weekend;
                        }
                    })

                    ->addColumn('status', function ($weekends) {
                        if ($weekends->status == 1) {
                            $status = '<button type="submit" class="btn btn-sm btn-success mb-2 text-white" onclick="showStatusChangeAlert(' . $weekends->id . ')">Active</button>';
                        } else {
                            $status = '<button type="submit" class="btn btn-sm btn-danger mb-2 text-white" onclick="showStatusChangeAlert(' . $weekends->id . ')">Inactive</button>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($weekends) {
                        return '<div class="btn-group" role="group" aria-label="Basic mixed styles example"> <a class="btn btn-sm btn-primary text-white " title="Show" style="cursor:pointer"
                        href="' . route('admin.hrm.setting.weekend.show', $weekends->id) . '"><i class="bx bx-show"> </i> </a><a href="' . route('admin.hrm.setting.weekend.edit', $weekends->id) . '" class="btn btn-sm btn-success text-white" style="cursor:pointer" title="Edit"><i class="bx bxs-edit"></i></a><a class="btn btn-sm btn-danger text-white" style="cursor:pointer" type="submit" onclick="showDeleteConfirm(' . $weekends->id . ')" title="Delete"><i class="bx bxs-trash"></i></a>
                            </div>';
                    })
                    ->rawColumns(['action', 'description', 'status'])
                    ->make(true);
            }
            return view('admin.hrm.settings.weekend.index');
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
            $days = DateTime::allWeeks();
            return view('admin.hrm.settings.weekend.create', compact('days'));
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
        // dd($request->all());
        $request->validate([
            'days' => 'required'
        ]);

        try {
            $weekend               =  new Weekend();
            $weekend->day          =  json_encode($request->days);
            $weekend->status       =  1;
            $weekend->description  =  $request->description;
            $weekend->created_by   =  Auth::user()->id;
            $weekend->access_id    =  json_encode(UserRepository::accessId(Auth::id()));

            $weekend->save();
            $LastStatus = Weekend::latest()->first();
            $allStatus = Weekend::where('id', '!=', $LastStatus->id)->get();
            foreach ($allStatus as $status) {
                if ($status->status == 1) {
                    $status->update([
                        'status' => '0'
                    ]);
                }
            }
            return redirect()->route('admin.hrm.setting.weekend.index')->with('message', 'Weekend Created  Successfully');

            if ($weekend->save()) {
                $LastStatus = Weekend::latest()->first();
                $allStatus = Weekend::where('id', '!=', $LastStatus->id)->get();
                foreach ($allStatus as $status) {
                    if ($status->status == 1) {
                        $status->update([
                            'status' => '0'
                        ]);
                    }
                }
                return redirect()->route('admin.hrm.setting.weekend.index')->with('message', 'Weekend Created  Successfully');
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
            $days = DateTime::allWeeks();
            $weekend = Weekend::findOrFail($id);
            return view('admin.hrm.settings.weekend.edit', compact('weekend', 'days'));
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
            'days' => 'required',
        ]);

        try {
            $weekend = Weekend::findOrFail($id);
            $weekend->day          =  json_encode($request->days);
            $weekend->status       =  $request->status;
            $weekend->description  =  $request->description;
            $weekend->updated_by = Auth::user()->id;
            $weekend->update();

            return redirect()->route('admin.hrm.setting.weekend.index')->with('message', 'Weekend Updated Successfully');
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
                Weekend::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Weekend Deleted Successfully.',
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
            $weekend = Weekend::findOrFail($id);
            // Check Item Current Status
            if ($weekend->status == 1) {
                $weekend->status = 0;
            } else {
                $weekend->status = 1;
            }

            $weekend->save();
            return response()->json([
                'success' => true,
                'message' => 'Weekend Status Update Successfully.',
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }
}




